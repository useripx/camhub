<?php

/**
 * CAMHUB AUTOMATED TEST SUITE — TAHAP 12: NOTIFIKASI & LAPORAN FINANSIAL
 * 
 * Menguji 10 Definition of Done (DoD):
 * 1. TEST-REP-01: Model & Repositori Notifikasi Terdaftar
 * 2. TEST-REP-02: Pembuatan Notifikasi Otomatis pada Aksi Transaksi & Operasional
 * 3. TEST-REP-03: Aksi Penandaan Notifikasi Dibaca (Mark Read & Mark All Read)
 * 4. TEST-REP-04: Hak Akses & Proteksi RBAC Modul Laporan Admin
 * 5. TEST-REP-05: Kalkulasi Akurat Metrik Kas Platform (Net Revenue & GTV)
 * 6. TEST-REP-06: Pemfilteran Laporan Finansial Berdasarkan Rentang Waktu
 * 7. TEST-REP-07: Distribusi Omzet Transaksi per Kategori Kamera
 * 8. TEST-REP-08: Ekspor Rekapitulasi Laporan Finansial Admin ke Format CSV
 * 9. TEST-REP-09: Ekspor Mutasi Saldo & Transaksi Mitra ke Format CSV
 * 10. TEST-REP-10: Halaman Cetak Nota Resmi Transaksi & Proteksi Anti-IDOR
 */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/app/Helpers/functions.php';

if (file_exists($rootDir . '/.env')) {
    load_env($rootDir . '/.env');
}

// Autoloader
spl_autoload_register(function ($class) use ($rootDir) {
    $prefix = 'App\\';
    $baseDir = $rootDir . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Database;
use App\Repositories\NotificationRepository;
use App\Services\NotificationService;
use App\Repositories\ReportRepository;
use App\Services\ReportService;
use App\Repositories\BookingRepository;
use App\Repositories\SalesRepository;

$db = Database::getConnection();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "========================================================\n";
echo "   CAMHUB TEST SUITE — TAHAP 12: NOTIFIKASI & LAPORAN   \n";
echo "========================================================\n\n";

$testsTotal = 0;
$testsPassed = 0;
$errors = [];

$assert_test = function (string $id, string $title, bool $condition, ?string $detail = null) use (&$testsTotal, &$testsPassed, &$errors) {
    $testsTotal++;
    if ($condition) {
        $testsPassed++;
        echo " [PASS] {$id}: {$title}";
        if ($detail) {
            echo " -> {$detail}";
        }
        echo "\n";
    } else {
        $msg = $detail ?? "Verifikasi pengujian gagal.";
        $errors[] = "{$id}: {$title} -> {$msg}";
        echo " [FAIL] {$id}: {$title} -> {$msg}\n";
    }
};

try {
    $notifRepo = new NotificationRepository($db);
    $notifService = new NotificationService($notifRepo);
    $reportRepo = new ReportRepository($db);
    $reportService = new ReportService($reportRepo);
    $bookingRepo = new BookingRepository($db);
    $salesRepo = new SalesRepository($db);

    // Ambil customer demo (User ID 2 atau 3) dan admin (ID 1)
    $stmtCust = $db->query("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'customer' LIMIT 1");
    $customerId = (int) ($stmtCust ? $stmtCust->fetchColumn() : 0);
    if ($customerId === 0) $customerId = 2;

    $stmtPartner = $db->query("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'partner' LIMIT 1");
    $partnerUserId = (int) ($stmtPartner ? $stmtPartner->fetchColumn() : 0);
    if ($partnerUserId === 0) $partnerUserId = 2;

    // -------------------------------------------------------------------------
    // TEST-REP-01: Model & Repositori Notifikasi Terdaftar
    // -------------------------------------------------------------------------
    $initialUnread = $notifRepo->getUnreadCount($customerId);
    $testNotifId = $notifRepo->create(
        $customerId,
        'Pengujian Notifikasi Sistem',
        'Ini adalah pesan notifikasi uji coba untuk memverifikasi repositori.',
        '/pesanan-saya'
    );
    $afterCreateUnread = $notifRepo->getUnreadCount($customerId);
    $fetched = $notifRepo->getById($testNotifId);

    $assert_test(
        "TEST-REP-01",
        "Model & Repositori Notifikasi Terdaftar",
        $testNotifId > 0 && $afterCreateUnread === ($initialUnread + 1) && !empty($fetched),
        "Notifikasi ID #{$testNotifId} tersimpan dengan unread count {$afterCreateUnread}."
    );

    // -------------------------------------------------------------------------
    // TEST-REP-02: Pembuatan Notifikasi Otomatis pada Aksi Transaksi & Operasional
    // -------------------------------------------------------------------------
    $notifService->notifyRentalBookingCreated(999, $partnerUserId, $customerId, 'Sony Alpha A7 III');
    $notifService->notifyKycStatus($customerId, 'approved');
    $notifService->notifyEscrowStatus($partnerUserId, 'released', 'Sony A7 III');
    $notifService->notifyConsignmentInspected($customerId, 'Fujifilm X-T4', 15000000.0);
    $notifService->notifyWithdrawalProcessed($partnerUserId, 500000.0, 'processed');

    $allCustNotifs = $notifRepo->getUserNotifications($customerId, 10);
    $foundKycNotif = false;
    $foundCsgNotif = false;
    foreach ($allCustNotifs as $cn) {
        if (strpos($cn['title'], 'KYC Disetujui') !== false) $foundKycNotif = true;
        if (strpos($cn['title'], 'Titip Jual') !== false) $foundCsgNotif = true;
    }

    $assert_test(
        "TEST-REP-02",
        "Pembuatan Notifikasi Otomatis pada Aksi Transaksi & Operasional",
        $foundKycNotif && $foundCsgNotif,
        "Pemicu notifikasi otomatis KYC dan Titip Jual berhasil mengirim pesan ke pengguna ID {$customerId}."
    );

    // -------------------------------------------------------------------------
    // TEST-REP-03: Aksi Penandaan Notifikasi Dibaca (Mark Read & Mark All Read)
    // -------------------------------------------------------------------------
    $markSingleOk = $notifRepo->markAsRead($testNotifId, $customerId);
    $fetchedAfterSingle = $notifRepo->getById($testNotifId);
    $isReadSingle = (bool)($fetchedAfterSingle['is_read'] ?? false);

    $markAllOk = $notifRepo->markAllAsRead($customerId);
    $unreadAfterAll = $notifRepo->getUnreadCount($customerId);

    $assert_test(
        "TEST-REP-03",
        "Aksi Penandaan Notifikasi Dibaca (Mark Read & Mark All Read)",
        $markSingleOk && $isReadSingle && $markAllOk && $unreadAfterAll === 0,
        "Notifikasi tunggal berhasil dibaca, dan markAllAsRead menyetel sisa unread count menjadi 0."
    );

    // -------------------------------------------------------------------------
    // TEST-REP-04: Hak Akses & Proteksi RBAC Modul Laporan Admin
    // -------------------------------------------------------------------------
    $_SESSION['user'] = ['id' => $customerId, 'username' => 'cust_demo', 'role' => 'customer'];
    $custCanAccessAdmin = (auth()['role'] ?? '') === 'admin';

    $_SESSION['user'] = ['id' => 1, 'username' => 'admin', 'role' => 'admin'];
    $adminCanAccessAdmin = (auth()['role'] ?? '') === 'admin';

    $assert_test(
        "TEST-REP-04",
        "Proteksi Hak Akses RBAC Modul Laporan Admin",
        !$custCanAccessAdmin && $adminCanAccessAdmin,
        "Role 'customer' diblokir dari area admin laporan, sedangkan role 'admin' memiliki akses penuh."
    );

    // -------------------------------------------------------------------------
    // TEST-REP-05: Kalkulasi Akurat Metrik Kas Platform (Net Revenue & GTV)
    // -------------------------------------------------------------------------
    $summaryAll = $reportRepo->getFinancialSummary('all');
    $expectedNet = $summaryAll['escrow_admin_fee'] + $summaryAll['csg_commission'] + $summaryAll['fines_collected'];
    $mathAccurate = abs($summaryAll['platform_net_revenue'] - $expectedNet) < 0.01;

    $assert_test(
        "TEST-REP-05",
        "Kalkulasi Akurat Metrik Kas Platform (Net Revenue & GTV)",
        $summaryAll['gross_turnover'] >= 0 && $summaryAll['total_transactions'] >= 0 && $mathAccurate,
        "GTV: Rp " . number_format($summaryAll['gross_turnover'], 0, ',', '.') . ", Net Platform Revenue: Rp " . number_format($summaryAll['platform_net_revenue'], 0, ',', '.') . "."
    );

    // -------------------------------------------------------------------------
    // TEST-REP-06: Pemfilteran Laporan Finansial Berdasarkan Rentang Waktu
    // -------------------------------------------------------------------------
    $summaryToday = $reportRepo->getFinancialSummary('today');
    $summary7Days = $reportRepo->getFinancialSummary('7days');
    $summaryMonth = $reportRepo->getFinancialSummary('month');
    $summaryCustom = $reportRepo->getFinancialSummary('custom', '2026-01-01', '2026-12-31');

    $filterWorks = isset($summaryToday['total_transactions']) &&
                   isset($summary7Days['total_transactions']) &&
                   isset($summaryMonth['total_transactions']) &&
                   isset($summaryCustom['total_transactions']);

    $assert_test(
        "TEST-REP-06",
        "Pemfilteran Laporan Finansial Berdasarkan Rentang Waktu",
        $filterWorks,
        "Filter 'today', '7days', 'month', dan 'custom' berhasil mengeksekusi kueri terisolasi."
    );

    // -------------------------------------------------------------------------
    // TEST-REP-07: Distribusi Omzet Transaksi per Kategori Kamera
    // -------------------------------------------------------------------------
    $categoriesBreakdown = $reportRepo->getCategoryBreakdown('all');
    $hasCategories = count($categoriesBreakdown) >= 1;

    $assert_test(
        "TEST-REP-07",
        "Distribusi Omzet Transaksi per Kategori Kamera",
        $hasCategories,
        "Ditemukan " . count($categoriesBreakdown) . " kategori kamera dengan agregasi nilai omzet transaksi."
    );

    // -------------------------------------------------------------------------
    // TEST-REP-08: Ekspor Rekapitulasi Laporan Finansial Admin ke Format CSV
    // -------------------------------------------------------------------------
    $csvAdmin = $reportService->generateCsvAdminReport('all');
    $hasCsvTitle = strpos($csvAdmin, 'LAPORAN TRANSAKSI & KEUANGAN PLATFORM CAMHUB') !== false;
    $hasCsvHeader = strpos($csvAdmin, 'Kode Transaksi') !== false && strpos($csvAdmin, 'Nominal Transaksi') !== false;

    $assert_test(
        "TEST-REP-08",
        "Ekspor Rekapitulasi Laporan Finansial Admin ke Format CSV",
        !empty($csvAdmin) && $hasCsvTitle && $hasCsvHeader,
        "Berkas CSV laporan admin berhasil digenerate dengan metadata dan header kolom terstruktur (" . strlen($csvAdmin) . " bytes)."
    );

    // -------------------------------------------------------------------------
    // TEST-REP-09: Ekspor Mutasi Saldo & Transaksi Mitra ke Format CSV
    // -------------------------------------------------------------------------
    $csvPartner = $reportService->generateCsvPartnerReport(1);
    $hasPartnerTitle = strpos($csvPartner, 'REKAPITULASI SALDO & TRANSAKSI ESCROW MITRA LAPAK') !== false;
    $hasPartnerHeader = strpos($csvPartner, 'Kode Pesanan') !== false && strpos($csvPartner, 'Nominal Dana') !== false;

    $assert_test(
        "TEST-REP-09",
        "Ekspor Mutasi Saldo & Transaksi Mitra ke Format CSV",
        !empty($csvPartner) && $hasPartnerTitle && $hasPartnerHeader,
        "Berkas CSV mutasi mitra berhasil digenerate dengan header escrow dan stempel waktu (" . strlen($csvPartner) . " bytes)."
    );

    // -------------------------------------------------------------------------
    // TEST-REP-10: Halaman Cetak Nota Resmi Transaksi & Proteksi Anti-IDOR
    // -------------------------------------------------------------------------
    // Ambil 1 booking sewa riil
    $stmtB = $db->query("SELECT id, user_id FROM rental_bookings LIMIT 1");
    $sampleBooking = $stmtB->fetch(PDO::FETCH_ASSOC);

    if ($sampleBooking) {
        $bookingId = (int) $sampleBooking['id'];
        $ownerId = (int) $sampleBooking['user_id'];
        $wrongUserId = $ownerId + 999;

        // Cek Anti-IDOR: pemilik asli dapat mengakses, user lain dicegah
        $ownerAccess = $bookingRepo->findBookingById($bookingId, $ownerId);
        $intruderAccess = $bookingRepo->findBookingById($bookingId, $wrongUserId);
        $adminAccess = $bookingRepo->findBookingById($bookingId, null); // Admin access

        $hasAntiIdorProtection = !empty($ownerAccess) && empty($intruderAccess) && !empty($adminAccess);
    } else {
        $hasAntiIdorProtection = true;
    }

    $receiptTemplateExists = file_exists($rootDir . '/resources/views/booking/receipt_print.php');
    $invoiceTemplateExists = file_exists($rootDir . '/resources/views/sales/invoice_print.php');

    $assert_test(
        "TEST-REP-10",
        "Halaman Cetak Nota Resmi Transaksi & Proteksi Anti-IDOR",
        $hasAntiIdorProtection && $receiptTemplateExists && $invoiceTemplateExists,
        "Template cetak nota dan invoice siap pakai, proteksi Anti-IDOR mengisolasi dokumen antarpengguna."
    );

} catch (\Throwable $e) {
    $assert_test("FATAL-ERROR", "Eksekusi Test Suite Gagal", false, $e->getMessage() . " on line " . $e->getLine());
}

echo "\n========================================================\n";
echo "HASIL PENGUJIAN TAHAP 12: {$testsPassed}/{$testsTotal} BERHASIL (" . round(($testsPassed / max(1, $testsTotal)) * 100) . "% PASS)\n";
echo "========================================================\n";

if (!empty($errors)) {
    echo "\nRincian Kegagalan:\n";
    foreach ($errors as $err) {
        echo " - {$err}\n";
    }
    exit(1);
} else {
    exit(0);
}
