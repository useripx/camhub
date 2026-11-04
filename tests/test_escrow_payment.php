<?php

declare(strict_types=1);

/**
 * CAMHUB AUTOMATED TEST SUITE — TAHAP 10: INTEGRASI PEMBAYARAN & ESCROW GATEWAY
 * (PENYELESAIAN TRANSAKSI, REKENING BERSAMA, & PENCAIRAN SALDO MITRA)
 *
 * Memverifikasi 10 Definition of Done (DoD) Skenario Pengujian Mutu
 */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/app/Helpers/functions.php';

if (file_exists($rootDir . '/.env')) {
    load_env($rootDir . '/.env');
}

spl_autoload_register(function ($class) use ($rootDir) {
    $prefix = 'App\\';
    $baseDir = $rootDir . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Core\Database;
use App\Repositories\EscrowRepository;
use App\Services\EscrowService;
use App\Repositories\PartnerRepository;

$db = Database::getConnection();

echo "========================================================\n";
echo "   CAMHUB TEST SUITE — TAHAP 10: PEMBAYARAN & ESCROW    \n";
echo "========================================================\n\n";

$testsPassed = 0;
$totalTests = 10;
$escrowRepo = new EscrowRepository();
$escrowService = new EscrowService();
$partnerRepo = new PartnerRepository();

function assertTest(bool $condition, string $code, string $desc, ?string $detail = null): void {
    global $testsPassed;
    if ($condition) {
        $testsPassed++;
        echo " [PASS] {$code}: {$desc}" . ($detail ? " -> {$detail}" : "") . "\n";
    } else {
        echo " [FAIL] {$code}: {$desc}" . ($detail ? " -> {$detail}" : "") . "\n";
    }
}

// ---------------------------------------------------------
// TEST-ESC-01: Pembersihan Cacat Visual Dashboard Mitra
// ---------------------------------------------------------
try {
    $partnerView = file_get_contents($rootDir . '/resources/views/partner/dashboard.php');

    // Ambil bagian body HTML (di luar block <style>)
    $bodyHtml = substr($partnerView, strpos($partnerView, '<body>') ?: 0);

    // Buang komentar HTML normal <!-- ... -->
    $withoutValidComments = preg_replace('/<!--.*?-->/s', '', $bodyHtml);

    // Cek tidak ada sisa orphan `-->`
    $hasLeakedCommentClose = strpos($withoutValidComments, '-->') !== false;

    // Cek tidak ada teks komentar yang bocor ke markup luar
    $hasLeakedCommentText = (
        strpos($withoutValidComments, 'PERSIS MOCKUP') !== false ||
        strpos($withoutValidComments, 'KONTEN UTAMA') !== false ||
        strpos($withoutValidComments, '3 KARTU METRIK') !== false ||
        strpos($withoutValidComments, 'SEKSI PRODUK SAYA') !== false
    );

    // Cek menu Kelola Titip Jual harus tepat muncul 1 kali di sidebar
    $countTitipJual = substr_count($partnerView, 'Kelola Titip Jual');

    $isClean = (!$hasLeakedCommentClose && !$hasLeakedCommentText && $countTitipJual === 1);

    assertTest($isClean, 
        'TEST-ESC-01', 
        'Pembersihan Cacat Visual Dashboard Mitra', 
        'Tidak ada teks komentar bocor dan menu Kelola Titip Jual tepat berjumlah 1.'
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ESC-01', 'Pembersihan Cacat Visual Dashboard Mitra', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ESC-02: Integritas Record Escrow pada Booking Sewa
// ---------------------------------------------------------
$rentalBookingId = 0;
$rentalEscrowId = 0;
try {
    $stmt = $db->query("
        SELECT e.*, b.user_id 
        FROM escrow_transactions e
        JOIN rental_bookings b ON e.booking_id = b.id
        WHERE e.status = 'held'
        ORDER BY e.id DESC
        LIMIT 1
    ");
    $rentalEscrow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rentalEscrow) {
        $rentalBookingId = (int)$rentalEscrow['booking_id'];
        $rentalEscrowId = (int)$rentalEscrow['id'];
        $amount = (float)$rentalEscrow['amount_held'];

        assertTest($rentalBookingId > 0 && $amount > 0 && $rentalEscrow['status'] === 'held',
            'TEST-ESC-02',
            'Integritas Record Escrow pada Booking Sewa',
            "Booking #RENT-" . str_pad((string)$rentalBookingId, 5, '0', STR_PAD_LEFT) . " memiliki escrow ID #{$rentalEscrowId} berstatus 'held' senilai Rp " . number_format($amount, 0, ',', '.') . "."
        );
    } else {
        assertTest(false, 'TEST-ESC-02', 'Integritas Record Escrow pada Booking Sewa', 'Tidak ditemukan record escrow sewa held');
    }
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ESC-02', 'Integritas Record Escrow pada Booking Sewa', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ESC-03: Integritas Record Escrow pada Order Jual Beli
// ---------------------------------------------------------
$salesOrderId = 0;
$salesEscrowId = 0;
try {
    $stmt = $db->query("
        SELECT e.*, s.order_number, s.user_id 
        FROM escrow_transactions e
        JOIN sales_orders s ON e.sales_order_id = s.id
        WHERE e.status = 'held'
        ORDER BY e.id DESC
        LIMIT 1
    ");
    $salesEscrow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($salesEscrow) {
        $salesOrderId = (int)$salesEscrow['sales_order_id'];
        $salesEscrowId = (int)$salesEscrow['id'];
        $amount = (float)$salesEscrow['amount_held'];

        assertTest($salesOrderId > 0 && $amount > 0 && $salesEscrow['status'] === 'held',
            'TEST-ESC-03',
            'Integritas Record Escrow pada Order Jual Beli',
            "Order #{$salesEscrow['order_number']} memiliki escrow ID #{$salesEscrowId} berstatus 'held' senilai Rp " . number_format($amount, 0, ',', '.') . "."
        );
    } else {
        assertTest(false, 'TEST-ESC-03', 'Integritas Record Escrow pada Order Jual Beli', 'Tidak ditemukan record escrow jual beli held');
    }
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ESC-03', 'Integritas Record Escrow pada Order Jual Beli', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ESC-04: Simulasi Status Pembayaran Sukses & Paid_At
// ---------------------------------------------------------
try {
    $stmtPay = $db->query("SELECT id FROM payments ORDER BY id DESC LIMIT 1");
    $targetPayId = (int)$stmtPay->fetchColumn();

    $simulated = $escrowService->simulatePaymentSuccess($targetPayId);

    $stmtCheck = $db->prepare("SELECT * FROM payments WHERE id = :id");
    $stmtCheck->execute(['id' => $targetPayId]);
    $paymentRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    $isPaySuccess = $simulated && 
                     $paymentRow['payment_status'] === 'success' && 
                     !empty($paymentRow['paid_at']);

    assertTest($isPaySuccess,
        'TEST-ESC-04',
        'Simulasi Status Pembayaran Sukses & Paid_At',
        "Payment ID #{$targetPayId} sukses diaktifkan, status 'success' dengan stempel waktu paid_at: {$paymentRow['paid_at']}."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ESC-04', 'Simulasi Status Pembayaran Sukses & Paid_At', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ESC-05: Konfirmasi Pelanggan & Pelepasan Dana (Release)
// ---------------------------------------------------------
try {
    // Ambil order booking sewa yang held
    $stmtHold = $db->query("
        SELECT b.id as booking_id, b.user_id, e.id as escrow_id, e.amount_held
        FROM rental_bookings b
        JOIN escrow_transactions e ON e.booking_id = b.id
        WHERE e.status = 'held'
        ORDER BY b.id DESC
        LIMIT 1
    ");
    $targetHold = $stmtHold->fetch(PDO::FETCH_ASSOC);

    if (!$targetHold) {
        throw new Exception("Tidak ada record booking held untuk diuji release.");
    }

    $bId = (int)$targetHold['booking_id'];
    $uId = (int)$targetHold['user_id'];
    $eId = (int)$targetHold['escrow_id'];

    $relRes = $escrowService->releaseRentalEscrow($bId, $uId);
    $updatedEscrow = $escrowRepo->findById($eId);

    // Periksa juga rental_status pada rental_bookings
    $stmtCheckBk = $db->prepare("SELECT rental_status FROM rental_bookings WHERE id = :id");
    $stmtCheckBk->execute(['id' => $bId]);
    $bkStatus = $stmtCheckBk->fetchColumn();

    $isReleased = $relRes['success'] && 
                 $updatedEscrow['status'] === 'released' && 
                 !empty($updatedEscrow['released_at']) &&
                 $bkStatus === 'completed';

    assertTest($isReleased,
        'TEST-ESC-05',
        'Konfirmasi Pelanggan & Pelepasan Dana (Release)',
        "Escrow #{$eId} berhasil dirilis ke toko mitra, status 'released', released_at: {$updatedEscrow['released_at']}, booking status: 'completed'."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ESC-05', 'Konfirmasi Pelanggan & Pelepasan Dana (Release)', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ESC-06: Kalkulasi Saldo Tersedia Toko Mitra
// ---------------------------------------------------------
try {
    // Toko Yogi Camera milik partner ID 1
    $metrics = $escrowRepo->getPartnerBalanceMetrics(1);

    $hasAvailable = isset($metrics['available_balance']) && $metrics['available_balance'] >= 2450000.0;
    $hasHeld = isset($metrics['held_balance']);
    $hasWithdrawn = isset($metrics['total_withdrawn']);

    assertTest($hasAvailable && $hasHeld && $hasWithdrawn,
        'TEST-ESC-06',
        'Kalkulasi Saldo Tersedia Toko Mitra',
        "Saldo Tersedia: Rp " . number_format($metrics['available_balance'], 0, ',', '.') . " | Saldo Tertahan: Rp " . number_format($metrics['held_balance'], 0, ',', '.') . "."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ESC-06', 'Kalkulasi Saldo Tersedia Toko Mitra', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ESC-07: Mekanisme Pembatalan & Pengembalian Dana (Refund)
// ---------------------------------------------------------
try {
    // Buat data escrow dummy untuk pengujian refund terisolasi
    $stmtPay = $db->query("SELECT id FROM payments LIMIT 1");
    $dummyPayId = (int)$stmtPay->fetchColumn();

    $stmtInsert = $db->prepare("
        INSERT INTO escrow_transactions (booking_id, payment_id, amount_held, status, held_at)
        VALUES (NULL, :payment_id, 350000.00, 'held', NOW())
    ");
    $stmtInsert->execute(['payment_id' => $dummyPayId]);
    $refundEscrowId = (int)$db->lastInsertId();

    $refRes = $escrowService->refundEscrow($refundEscrowId, 1, 'Pembatalan transaksi uji otomatis');
    $refundedRow = $escrowRepo->findById($refundEscrowId);

    $isRefundOk = $refRes['success'] && $refundedRow['status'] === 'refunded';

    assertTest($isRefundOk,
        'TEST-ESC-07',
        'Mekanisme Pembatalan & Pengembalian Dana (Refund)',
        "Escrow ID #{$refundEscrowId} berhasil di-refund, status 'refunded' dan dana tidak cair ke mitra."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ESC-07', 'Mekanisme Pembatalan & Pengembalian Dana (Refund)', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ESC-08: Mekanisme Pengajuan Sengketa (Dispute)
// ---------------------------------------------------------
try {
    // Siapkan transaksi sales held untuk pengujian dispute
    $stmtSalesHold = $db->query("
        SELECT s.id as sales_order_id, s.user_id, e.id as escrow_id 
        FROM sales_orders s
        JOIN escrow_transactions e ON e.sales_order_id = s.id
        WHERE e.status = 'held'
        ORDER BY s.id DESC
        LIMIT 1
    ");
    $salesHold = $stmtSalesHold->fetch(PDO::FETCH_ASSOC);

    if ($salesHold) {
        $sId = (int)$salesHold['sales_order_id'];
        $uId = (int)$salesHold['user_id'];
        $eId = (int)$salesHold['escrow_id'];

        $dispRes = $escrowService->requestDispute('sale', $sId, $uId, 'Kamera tidak sesuai deskripsi (sensor berdebu)');
        $disputedRow = $escrowRepo->findById($eId);

        // Pastikan dana tidak dapat dirilis saat disputed
        $tryRelease = $escrowService->releaseSalesEscrow($sId, $uId);

        $isDisputedOk = $dispRes['success'] && 
                        $disputedRow['status'] === 'disputed' && 
                        !$tryRelease['success'];

        assertTest($isDisputedOk,
            'TEST-ESC-08',
            'Mekanisme Pengajuan Sengketa (Dispute)',
            "Escrow #{$eId} terkunci 'disputed', upaya pelepasan dana otomatis diblokir sistem."
        );
    } else {
        assertTest(false, 'TEST-ESC-08', 'Mekanisme Pengajuan Sengketa (Dispute)', 'Tidak ada order jual beli held untuk di-dispute');
    }
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ESC-08', 'Mekanisme Pengajuan Sengketa (Dispute)', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ESC-09: Validasi Formulir Penarikan Saldo Mitra
// ---------------------------------------------------------
try {
    $partnerId = 1; // Yogi Camera

    // 1. Uji nominal melebihi saldo tersedia (Rp 999.000.000)
    $resExceed = $escrowService->requestWithdrawal($partnerId, 999000000.0, [
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Yogi Ario'
    ]);

    // 2. Uji nominal di bawah batas minimum Rp 50.000
    $resBelowMin = $escrowService->requestWithdrawal($partnerId, 25000.0, [
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Yogi Ario'
    ]);

    // 3. Uji data bank kosong
    $resEmptyBank = $escrowService->requestWithdrawal($partnerId, 100000.0, [
        'bank_name' => '',
        'account_number' => '',
        'account_holder' => ''
    ]);

    // 4. Uji penarikan sah
    $resValid = $escrowService->requestWithdrawal($partnerId, 100000.0, [
        'bank_name' => 'Bank Mandiri',
        'account_number' => '1370012345678',
        'account_holder' => 'Yogi Camera Lapak'
    ]);

    $allValidationPass = (!$resExceed['success']) && 
                         (!$resBelowMin['success']) && 
                         (!$resEmptyBank['success']) && 
                         ($resValid['success']);

    assertTest($allValidationPass,
        'TEST-ESC-09',
        'Validasi Formulir Penarikan Saldo Mitra',
        "Sistem menolak saldo berlebih, menolak < Rp 50.000, menolak bank kosong, dan sukses mencatat ID #{$resValid['withdrawal_id']}."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ESC-09', 'Validasi Formulir Penarikan Saldo Mitra', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ESC-10: Perekaman Audit Trail Finansial & Transaksi ACID
// ---------------------------------------------------------
try {
    $stmtAudit = $db->query("
        SELECT action, target_table, target_id, changes_json, created_at 
        FROM audit_logs 
        WHERE action IN ('ESCROW_RELEASE', 'ESCROW_REFUND', 'ESCROW_DISPUTE')
        ORDER BY id DESC
        LIMIT 5
    ");
    $auditLogs = $stmtAudit->fetchAll(PDO::FETCH_ASSOC);

    $hasAuditLogs = count($auditLogs) >= 2;

    assertTest($hasAuditLogs,
        'TEST-ESC-10',
        'Perekaman Audit Trail Finansial & Transaksi ACID',
        "Ditemukan " . count($auditLogs) . " jejak audit mutasi finansial tersimpan aman di tabel audit_logs."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ESC-10', 'Perekaman Audit Trail Finansial & Transaksi ACID', $e->getMessage());
}

echo "\n========================================================\n";
echo "HASIL PENGUJIAN TAHAP 10: {$testsPassed}/{$totalTests} BERHASIL (" . round(($testsPassed / $totalTests) * 100) . "% PASS)\n";
echo "========================================================\n";

if ($testsPassed === $totalTests) {
    exit(0);
} else {
    exit(1);
}
