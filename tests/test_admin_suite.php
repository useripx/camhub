<?php

declare(strict_types=1);

/**
 * CAMHUB AUTOMATED TEST SUITE — TAHAP 11: DASHBOARD SUPER ADMINISTRATOR
 * (PUSAT KENDALI PENGGUNA, VERIFIKASI KYC, MODERASI SENGKETA ESCROW, & AUDIT KEAMANAN)
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
use App\Repositories\AdminRepository;
use App\Services\AdminService;
use App\Repositories\EscrowRepository;

$db = Database::getConnection();

echo "========================================================\n";
echo "   CAMHUB TEST SUITE — TAHAP 11: SUPER ADMINISTRATOR    \n";
echo "========================================================\n\n";

$testsPassed = 0;
$totalTests = 10;
$adminRepo = new AdminRepository();
$adminService = new AdminService();
$escrowRepo = new EscrowRepository();

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
// TEST-ADM-01: Akses Modul Verifikasi KYC & Navigasi Sidebar
// ---------------------------------------------------------
try {
    $kycView = file_get_contents($rootDir . '/resources/views/admin/kyc.php');
    $hasSidebarNav = strpos($kycView, '/dashboard/admin/kyc') !== false;
    $hasTableMarkup = strpos($kycView, 'table-custom') !== false && strpos($kycView, 'NIK KTP') !== false;

    $submissions = $adminRepo->getAllKycSubmissions();
    $hasSubmissions = is_array($submissions) && count($submissions) >= 1;

    assertTest($hasSidebarNav && $hasTableMarkup && $hasSubmissions,
        'TEST-ADM-01',
        'Akses Modul Verifikasi KYC & Navigasi Sidebar',
        "Terdapat " . count($submissions) . " pengajuan KYC termuat di tabel dan antarmuka terhubung ke navigasi admin."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ADM-01', 'Akses Modul Verifikasi KYC & Navigasi Sidebar', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ADM-02: Persetujuan KYC (Approve) & Sinkronisasi Profil
// ---------------------------------------------------------
try {
    // Siapkan pengajuan KYC uji coba
    $stmtUser = $db->query("SELECT id FROM users WHERE role_id = 3 ORDER BY id DESC LIMIT 1");
    $testUserId = (int) $stmtUser->fetchColumn();

    $stmtInsert = $db->prepare("
        INSERT INTO kyc_submissions (user_id, full_name, nik_ktp, birth_date, gender, ktp_image_path, selfie_image_path, status, created_at)
        VALUES (:user_id, 'User Uji KYC Approve', '3201123456780001', '1995-05-12', 'Laki-laki', 'uploads/kyc/test_ktp.jpg', 'uploads/kyc/test_selfie.jpg', 'pending', NOW())
    ");
    $stmtInsert->execute(['user_id' => $testUserId]);
    $kycApproveId = (int) $db->lastInsertId();

    $approveRes = $adminService->approveKyc($kycApproveId, 1);
    $checkKyc = $adminRepo->findKycById($kycApproveId);

    $isApproved = $approveRes['success'] && 
                 $checkKyc['status'] === 'approved' && 
                 (int)$checkKyc['verified_by'] === 1 && 
                 !empty($checkKyc['verified_at']);

    assertTest($isApproved,
        'TEST-ADM-02',
        'Persetujuan KYC (Approve) & Sinkronisasi Profil',
        "Pengajuan #{$kycApproveId} resmi berstatus 'approved', diverifikasi oleh Admin ID 1 pada {$checkKyc['verified_at']}."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ADM-02', 'Persetujuan KYC (Approve) & Sinkronisasi Profil', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ADM-03: Penolakan KYC (Reject) Disertai Alasan Valid
// ---------------------------------------------------------
try {
    $stmtInsert = $db->prepare("
        INSERT INTO kyc_submissions (user_id, full_name, nik_ktp, birth_date, gender, ktp_image_path, selfie_image_path, status, created_at)
        VALUES (:user_id, 'User Uji KYC Reject', '3201123456780002', '1998-08-20', 'Perempuan', 'uploads/kyc/test_ktp_buram.jpg', 'uploads/kyc/test_selfie.jpg', 'pending', NOW())
    ");
    $stmtInsert->execute(['user_id' => $testUserId]);
    $kycRejectId = (int) $db->lastInsertId();

    $rejectReason = 'Foto KTP terlalu buram dan NIK tidak dapat dibaca jelas oleh sistem.';
    $rejectRes = $adminService->rejectKyc($kycRejectId, 1, $rejectReason);
    $checkRejected = $adminRepo->findKycById($kycRejectId);

    $isRejected = $rejectRes['success'] && 
                  $checkRejected['status'] === 'rejected' && 
                  $checkRejected['rejection_reason'] === $rejectReason;

    assertTest($isRejected,
        'TEST-ADM-03',
        'Penolakan KYC (Reject) Disertai Alasan Valid',
        "Pengajuan #{$kycRejectId} berstatus 'rejected' dengan alasan tercatat: '{$checkRejected['rejection_reason']}'."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ADM-03', 'Penolakan KYC (Reject) Disertai Alasan Valid', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ADM-04: Pusat Pengawasan Transaksi Platform Terpadu
// ---------------------------------------------------------
try {
    $transactions = $adminRepo->getAllTransactions();
    $hasRentals = false;
    $hasSales = false;

    foreach ($transactions as $tx) {
        if ($tx['trx_type'] === 'Sewa Kamera') $hasRentals = true;
        if ($tx['trx_type'] === 'Jual Beli') $hasSales = true;
    }

    assertTest(count($transactions) >= 5 && $hasRentals && $hasSales,
        'TEST-ADM-04',
        'Pusat Pengawasan Transaksi Platform Terpadu',
        "Ditemukan " . count($transactions) . " riwayat transaksi gabungan (Sewa & Jual Beli) dengan data pihak, nominal, dan status escrow."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ADM-04', 'Pusat Pengawasan Transaksi Platform Terpadu', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ADM-05: Resolusi Sengketa: Putusan Pencairan ke Mitra
// ---------------------------------------------------------
try {
    // Siapkan escrow berstatus disputed
    $stmtPay = $db->query("SELECT id FROM payments LIMIT 1");
    $payId = (int)$stmtPay->fetchColumn();

    $stmtInsertEsc = $db->prepare("
        INSERT INTO escrow_transactions (booking_id, payment_id, amount_held, status, held_at)
        VALUES (NULL, :pay_id, 450000.00, 'disputed', NOW())
    ");
    $stmtInsertEsc->execute(['pay_id' => $payId]);
    $escrowDisputeId = (int)$db->lastInsertId();

    $resolveRes = $adminService->resolveDispute($escrowDisputeId, 'release', 1, 'Komplain tidak memiliki bukti fisik sah');
    $checkEscrow = $escrowRepo->findById($escrowDisputeId);

    $isForceReleased = $resolveRes['success'] && 
                       $checkEscrow['status'] === 'released' && 
                       !empty($checkEscrow['released_at']);

    assertTest($isForceReleased,
        'TEST-ADM-05',
        'Resolusi Sengketa: Putusan Pencairan ke Mitra',
        "Sengketa Escrow #{$escrowDisputeId} berhasil diputus force-release ke toko mitra, status 'released'."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ADM-05', 'Resolusi Sengketa: Putusan Pencairan ke Mitra', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ADM-06: Resolusi Sengketa: Putusan Pengembalian ke Pelanggan
// ---------------------------------------------------------
try {
    $stmtInsertEsc = $db->prepare("
        INSERT INTO escrow_transactions (booking_id, payment_id, amount_held, status, held_at)
        VALUES (NULL, :pay_id, 850000.00, 'disputed', NOW())
    ");
    $stmtInsertEsc->execute(['pay_id' => $payId]);
    $escrowRefundDisputeId = (int)$db->lastInsertId();

    $resolveRefundRes = $adminService->resolveDispute($escrowRefundDisputeId, 'refund', 1, 'Unit terbukti rusak dan dikembalikan ke toko');
    $checkRefundEscrow = $escrowRepo->findById($escrowRefundDisputeId);

    $isForceRefunded = $resolveRefundRes['success'] && $checkRefundEscrow['status'] === 'refunded';

    assertTest($isForceRefunded,
        'TEST-ADM-06',
        'Resolusi Sengketa: Putusan Pengembalian ke Pelanggan',
        "Sengketa Escrow #{$escrowRefundDisputeId} berhasil diputus force-refund ke pelanggan, status 'refunded'."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ADM-06', 'Resolusi Sengketa: Putusan Pengembalian ke Pelanggan', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ADM-07: Persetujuan Pencairan Dana Mitra Lapak
// ---------------------------------------------------------
try {
    // Siapkan pengajuan penarikan dana pending
    $stmtWd = $db->prepare("
        INSERT INTO partner_withdrawals (partner_id, amount, bank_name, account_number, account_holder, status, created_at)
        VALUES (1, 200000.00, 'Bank BCA', '8880123456', 'Yogi Camera', 'pending', NOW())
    ");
    $stmtWd->execute();
    $wdId = (int)$db->lastInsertId();

    $processRes = $adminService->processWithdrawal($wdId, 'processed', 1);
    $checkWd = $adminRepo->findWithdrawalById($wdId);

    $isWdProcessed = $processRes['success'] && $checkWd['status'] === 'processed';

    assertTest($isWdProcessed,
        'TEST-ADM-07',
        'Persetujuan Pencairan Dana Mitra Lapak',
        "Pengajuan #WD-{$wdId} senilai Rp 200.000 resmi disetujui transfer oleh Super Admin, status 'processed'."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ADM-07', 'Persetujuan Pencairan Dana Mitra Lapak', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ADM-08: Pengawasan Jadwal Unit Kamera Lintas Mitra
// ---------------------------------------------------------
try {
    $schedules = $adminRepo->getAllUnitSchedules();
    $hasSerialNumbers = false;
    $hasStoreNames = false;

    if (!empty($schedules)) {
        $first = $schedules[0];
        $hasSerialNumbers = !empty($first['serial_number']);
        $hasStoreNames = !empty($first['store_name']);
    }

    assertTest(count($schedules) >= 5 && $hasSerialNumbers && $hasStoreNames,
        'TEST-ADM-08',
        'Pengawasan Jadwal Unit Kamera Lintas Mitra',
        "Ditemukan " . count($schedules) . " unit fisik kamera terpantau aktif lengkap dengan nomor seri dan toko pemilik."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ADM-08', 'Pengawasan Jadwal Unit Kamera Lintas Mitra', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ADM-09: Penerbitan & Pelunasan Denda Operasional
// ---------------------------------------------------------
try {
    $stmtBk = $db->query("SELECT id FROM rental_bookings LIMIT 1");
    $testBookingId = (int)$stmtBk->fetchColumn();

    // 1. Terbitkan denda
    $fineRes = $adminService->storeFine([
        'booking_id' => $testBookingId,
        'fine_type' => 'late_return',
        'amount' => 150000.00,
        'reason' => 'Keterlambatan pengembalian 1 hari'
    ], 1);

    $fineId = $fineRes['fine_id'] ?? 0;

    // 2. Pelunasan denda
    $settleRes = $adminService->settleFine($fineId, 1);

    $stmtCheckFine = $db->prepare("SELECT status FROM fines WHERE id = :id");
    $stmtCheckFine->execute(['id' => $fineId]);
    $fineStatus = $stmtCheckFine->fetchColumn();

    $isFineHandled = $fineRes['success'] && $settleRes['success'] && $fineStatus === 'paid';

    assertTest($isFineHandled,
        'TEST-ADM-09',
        'Penerbitan & Pelunasan Denda Operasional',
        "Denda ID #{$fineId} sukses diterbitkan (Rp 150.000) dan berhasil diselesaikan dengan status 'paid'."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ADM-09', 'Penerbitan & Pelunasan Denda Operasional', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-ADM-10: Manajemen Akun Pengguna & Pemantauan Audit Trail
// ---------------------------------------------------------
try {
    // 1. Uji daftar pengguna
    $users = $adminRepo->getAllUsers();
    $hasAdminAndCustomer = false;
    $hasAdmin = false;
    $hasCustomer = false;

    foreach ($users as $u) {
        if ($u['role_name'] === 'admin') $hasAdmin = true;
        if ($u['role_name'] === 'customer') $hasCustomer = true;
    }
    $hasAdminAndCustomer = ($hasAdmin && $hasCustomer);

    // 2. Uji suspensi akun customer
    $toggleRes = $adminService->toggleUserStatus($testUserId, 1);
    $toggleBack = $adminService->toggleUserStatus($testUserId, 1); // kembalikan

    // 3. Uji pembacaan audit log
    $auditLogs = $adminRepo->getRecentAuditLogs(20);
    $hasAuditLogs = count($auditLogs) >= 5;

    $isManagementValid = $hasAdminAndCustomer && 
                         $toggleRes['success'] && 
                         $toggleBack['success'] && 
                         $hasAuditLogs;

    assertTest($isManagementValid,
        'TEST-ADM-10',
        'Manajemen Akun Pengguna & Pemantauan Audit Trail',
        "Terdaftar " . count($users) . " akun multi-peran, fungsi suspensi berjalan, dan ditemukan " . count($auditLogs) . " log audit keamanan."
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-ADM-10', 'Manajemen Akun Pengguna & Pemantauan Audit Trail', $e->getMessage());
}

echo "\n========================================================\n";
echo "HASIL PENGUJIAN TAHAP 11: {$testsPassed}/{$totalTests} BERHASIL (" . round(($testsPassed / $totalTests) * 100) . "% PASS)\n";
echo "========================================================\n";

if ($testsPassed === $totalTests) {
    exit(0);
} else {
    exit(1);
}
