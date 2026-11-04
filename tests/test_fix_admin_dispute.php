<?php

declare(strict_types=1);

/**
 * CamHub Test Suite — Fix: Resolusi Sengketa Transaksi Jual Beli Super Admin
 * 
 * Memvalidasi perbaikan bug SQL truncation pada pemutusan sengketa sales order
 * (Release ke Mitra -> 'completed', Refund ke Pelanggan -> 'cancelled').
 * 
 * @package CamHub
 * @author Kelompok 1 (Yogi, Regie, Ivan, Krisna)
 */

define('CAMHUB_TEST', true);
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

require_once APP_PATH . '/Helpers/functions.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require $file;
});

echo "========================================================\n";
echo "   CAMHUB TEST SUITE — FIX: ADMIN DISPUTE RESOLUTION    \n";
echo "========================================================\n\n";

$pdo = \App\Core\Database::getConnection();
$adminService = new \App\Services\AdminService();

$passed = 0;
$total = 0;

$assert_test = function (string $code, string $name, bool $condition, string $detail = '') use (&$passed, &$total) {
    $total++;
    if ($condition) {
        $passed++;
        echo " [PASS] {$code}: {$name}\n";
        if ($detail) echo "        -> {$detail}\n";
    } else {
        echo " [FAIL] {$code}: {$name}\n";
        if ($detail) echo "        -> {$detail}\n";
    }
};

try {
    // -------------------------------------------------------------------------
    // TEST 1: Validasi Skema MySQL sales_orders.order_status
    // -------------------------------------------------------------------------
    $stmtCol = $pdo->query("SHOW COLUMNS FROM sales_orders LIKE 'order_status'");
    $col = $stmtCol->fetch(PDO::FETCH_ASSOC);
    $typeStr = (string)($col['Type'] ?? '');
    $hasCompleted = str_contains($typeStr, "'completed'");

    $assert_test(
        "TEST-FIX-01",
        "Validasi Skema MySQL sales_orders.order_status",
        $hasCompleted,
        "Kolom order_status memuat nilai enum 'completed' ({$typeStr})."
    );

    // -------------------------------------------------------------------------
    // TEST 2 & 3: Resolusi Sengketa dengan Opsi 'release' (Cairkan ke Mitra)
    // -------------------------------------------------------------------------
    // Buat data uji sales_order dummy
    $stmtOrder1 = $pdo->prepare("
        INSERT INTO sales_orders (user_id, order_number, subtotal, admin_fee, total_amount, order_status, shipping_address, created_at)
        VALUES (2, CONCAT('TEST-DISP-', UNIX_TIMESTAMP(), '-', FLOOR(RAND()*1000)), 1500000.00, 5000.00, 1505000.00, 'paid', 'Jl. Uji Sengketa No. 1', NOW())
    ");
    $stmtOrder1->execute();
    $orderId1 = (int)$pdo->lastInsertId();

    // Buat payment record dummy
    $stmtPay1 = $pdo->prepare("
        INSERT INTO payments (user_id, order_type, reference_id, amount, admin_fee, total_amount, payment_method, payment_status, paid_at, created_at)
        VALUES (2, 'sale', :ref_id, 1500000.00, 5000.00, 1505000.00, 'escrow', 'success', NOW(), NOW())
    ");
    $stmtPay1->execute(['ref_id' => $orderId1]);
    $payId1 = (int)$pdo->lastInsertId();

    // Buat data escrow disputed
    $stmtEscrow1 = $pdo->prepare("
        INSERT INTO escrow_transactions (payment_id, sales_order_id, amount_held, status, created_at)
        VALUES (:pay_id, :order_id, 1500000.00, 'disputed', NOW())
    ");
    $stmtEscrow1->execute(['pay_id' => $payId1, 'order_id' => $orderId1]);
    $escrowId1 = (int)$pdo->lastInsertId();

    $resRelease = $adminService->resolveDispute($escrowId1, 'release', 1, 'Unit test force release');

    $assert_test(
        "TEST-FIX-02",
        "Eksekusi AdminService::resolveDispute Opsi 'release'",
        $resRelease['success'] === true,
        $resRelease['message'] ?? 'Pencairan berhasil diputus.'
    );

    // Cek status di database
    $stmtCheck1 = $pdo->prepare("SELECT order_status FROM sales_orders WHERE id = :id");
    $stmtCheck1->execute(['id' => $orderId1]);
    $finalStatus1 = (string)$stmtCheck1->fetchColumn();

    $stmtEscCheck1 = $pdo->prepare("SELECT status, released_at FROM escrow_transactions WHERE id = :id");
    $stmtEscCheck1->execute(['id' => $escrowId1]);
    $finalEsc1 = $stmtEscCheck1->fetch(PDO::FETCH_ASSOC);

    $assert_test(
        "TEST-FIX-03",
        "Integritas Status Transaksi Setelah 'release'",
        $finalStatus1 === 'completed' && ($finalEsc1['status'] ?? '') === 'released' && !empty($finalEsc1['released_at']),
        "sales_orders.order_status = '{$finalStatus1}', escrow status = '{$finalEsc1['status']}'."
    );

    // -------------------------------------------------------------------------
    // TEST 4 & 5: Resolusi Sengketa dengan Opsi 'refund' (Kembalikan ke Pelanggan)
    // -------------------------------------------------------------------------
    $stmtOrder2 = $pdo->prepare("
        INSERT INTO sales_orders (user_id, order_number, subtotal, admin_fee, total_amount, order_status, shipping_address, created_at)
        VALUES (2, CONCAT('TEST-DISP-REF-', UNIX_TIMESTAMP(), '-', FLOOR(RAND()*1000)), 2000000.00, 5000.00, 2005000.00, 'paid', 'Jl. Uji Sengketa No. 2', NOW())
    ");
    $stmtOrder2->execute();
    $orderId2 = (int)$pdo->lastInsertId();

    $stmtPay2 = $pdo->prepare("
        INSERT INTO payments (user_id, order_type, reference_id, amount, admin_fee, total_amount, payment_method, payment_status, paid_at, created_at)
        VALUES (2, 'sale', :ref_id, 2000000.00, 5000.00, 2005000.00, 'escrow', 'success', NOW(), NOW())
    ");
    $stmtPay2->execute(['ref_id' => $orderId2]);
    $payId2 = (int)$pdo->lastInsertId();

    $stmtEscrow2 = $pdo->prepare("
        INSERT INTO escrow_transactions (payment_id, sales_order_id, amount_held, status, created_at)
        VALUES (:pay_id, :order_id, 2000000.00, 'disputed', NOW())
    ");
    $stmtEscrow2->execute(['pay_id' => $payId2, 'order_id' => $orderId2]);
    $escrowId2 = (int)$pdo->lastInsertId();

    $resRefund = $adminService->resolveDispute($escrowId2, 'refund', 1, 'Unit test force refund');

    $assert_test(
        "TEST-FIX-04",
        "Eksekusi AdminService::resolveDispute Opsi 'refund'",
        $resRefund['success'] === true,
        $resRefund['message'] ?? 'Pengembalian dana berhasil diputus.'
    );

    $stmtCheck2 = $pdo->prepare("SELECT order_status FROM sales_orders WHERE id = :id");
    $stmtCheck2->execute(['id' => $orderId2]);
    $finalStatus2 = (string)$stmtCheck2->fetchColumn();

    $stmtEscCheck2 = $pdo->prepare("SELECT status FROM escrow_transactions WHERE id = :id");
    $stmtEscCheck2->execute(['id' => $escrowId2]);
    $finalEsc2 = (string)$stmtEscCheck2->fetchColumn();

    $assert_test(
        "TEST-FIX-05",
        "Integritas Status Transaksi Setelah 'refund'",
        $finalStatus2 === 'cancelled' && $finalEsc2 === 'refunded',
        "sales_orders.order_status = '{$finalStatus2}', escrow status = '{$finalEsc2}'."
    );

    // -------------------------------------------------------------------------
    // TEST 6: Audit Log Recording
    // -------------------------------------------------------------------------
    $stmtAudit = $pdo->prepare("
        SELECT action FROM audit_logs 
        WHERE target_table = 'escrow_transactions' AND target_id IN (:id1, :id2)
        ORDER BY id DESC
    ");
    $stmtAudit->execute(['id1' => $escrowId1, 'id2' => $escrowId2]);
    $actions = $stmtAudit->fetchAll(PDO::FETCH_COLUMN);

    $hasReleaseAudit = in_array('DISPUTE_FORCE_RELEASE', $actions, true);
    $hasRefundAudit = in_array('DISPUTE_FORCE_REFUND', $actions, true);

    $assert_test(
        "TEST-FIX-06",
        "Pencatatan Audit Trail Resmi Super Admin",
        $hasReleaseAudit && $hasRefundAudit,
        "Aksi DISPUTE_FORCE_RELEASE dan DISPUTE_FORCE_REFUND terekam di tabel audit_logs."
    );

} catch (Throwable $e) {
    echo " [FATAL ERROR]: " . $e->getMessage() . "\n";
}

echo "\n========================================================\n";
echo "HASIL PENGUJIAN: {$passed} / {$total} BERHASIL (" . round(($passed / max(1, $total)) * 100) . "% PASS)\n";
echo "========================================================\n";

if ($passed === $total && $total > 0) {
    exit(0);
} else {
    exit(1);
}
