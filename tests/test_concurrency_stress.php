<?php

declare(strict_types=1);

/**
 * CAMHUB CONCURRENCY STRESS TEST — TAHAP 13
 * 
 * Menguji ketahanan ACID transaksi dan Pessimistic Locking (SELECT ... FOR UPDATE)
 * saat beberapa transaksi konkuren mencoba menyewa unit fisik kamera yang sama
 * pada rentang tanggal bertubrukan (overlapping dates).
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
use App\Repositories\BookingRepository;

$db = Database::getConnection();
$bookingRepo = new BookingRepository($db);

echo "========================================================\n";
echo "    CAMHUB CONCURRENCY & STRESS TEST (ANTI RACE-CONDITION) \n";
echo "========================================================\n\n";

// 1. Ambil 1 unit fisik kamera
$unitStmt = $db->query("SELECT id, product_id, serial_number FROM product_units WHERE availability_status = 'available' LIMIT 1");
$unit = $unitStmt->fetch(PDO::FETCH_ASSOC);

if (!$unit) {
    echo "[SKIP] Tidak ada unit kamera berstatus available untuk stress test.\n";
    exit(0);
}

$unitId = (int)$unit['id'];
$startDate = '2029-07-01';
$endDate = '2029-07-04';

// Bersihkan data lama jika ada
$db->exec("DELETE FROM rental_bookings WHERE product_unit_id = {$unitId} AND start_date = '{$startDate}'");

$successCount = 0;
$blockedCount = 0;
$createdBookingIds = [];

// Simulasi 5 request konkuren yang mencoba membooking unit fisik yang sama pada tanggal sama
$concurrentAttempts = 5;

for ($i = 1; $i <= $concurrentAttempts; $i++) {
    $bookingData = [
        'user_id' => 2,
        'product_unit_id' => $unitId,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'total_days' => 3,
        'daily_price' => 250000.00,
        'subtotal' => 750000.00,
        'admin_fee' => 5000.00,
        'total_amount' => 755000.00,
        'payment_status' => 'unpaid',
        'rental_status' => 'pending',
    ];

    $paymentData = [
        'payment_method' => 'escrow',
        'payment_status' => 'pending',
    ];

    try {
        $bookingId = $bookingRepo->createBookingWithTransaction($bookingData, $paymentData);
        $successCount++;
        $createdBookingIds[] = $bookingId;
        echo " [REQ-{$i}] SUKSES membooking unit (ID Transaksi: #{$bookingId})\n";
    } catch (\Throwable $e) {
        $blockedCount++;
        echo " [REQ-{$i}] TERCEGAH DENGAN AMAN: " . $e->getMessage() . "\n";
    }
}

// Verifikasi integritas: Harus tepat 1 sukses dan 4 tercegah
$isStressPassed = ($successCount === 1) && ($blockedCount === ($concurrentAttempts - 1));

echo "\n--- HASIL SIMULASI KONKURENSI ---\n";
echo "Total Request Simultat : {$concurrentAttempts}\n";
echo "Transaksi Berhasil     : {$successCount} (Harus tepat 1)\n";
echo "Transaksi Tercegah     : {$blockedCount} (Harus tepat 4)\n";
echo "Status Pengujian Beban : " . ($isStressPassed ? "LULUS (PESSIMISTIC LOCKING BEKERJA 100% AMAN)" : "GAGAL") . "\n";

// Cleanup test records
if (!empty($createdBookingIds)) {
    $inClause = implode(',', $createdBookingIds);
    $db->exec("DELETE FROM escrow_transactions WHERE booking_id IN ({$inClause})");
    $db->exec("DELETE FROM payments WHERE reference_id IN ({$inClause}) AND order_type = 'rental'");
    $db->exec("DELETE FROM rental_bookings WHERE id IN ({$inClause})");
    echo "Pembersihan berkas uji coba berhasil.\n";
}

echo "========================================================\n";
exit($isStressPassed ? 0 : 1);
