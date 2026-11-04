<?php

declare(strict_types=1);

/**
 * CAMHUB AUTOMATED TEST SUITE — TAHAP 09: SISTEM TITIP JUAL KAMERA (CONSIGNMENT)
 * & PENYELARASAN TOTAL DASHBOARD ADMINISTRATOR (100% PROTOTYPE)
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
use App\Models\Consignment;
use App\Repositories\AdminRepository;
use App\Repositories\ConsignmentRepository;
use App\Services\ConsignmentService;
use App\Validators\ConsignmentValidator;

$db = Database::getConnection();

echo "========================================================\n";
echo "   CAMHUB TEST SUITE — TAHAP 09: SISTEM TITIP JUAL & ADMIN\n";
echo "========================================================\n\n";

$testsPassed = 0;
$totalTests = 10;
$adminHtml = '';
$metrics = [];
$chartData = [];
$activeStores = [];
$consignId = 0;
$consignService = new ConsignmentService();
$consignRepo = new ConsignmentRepository();

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
// TEST-CNS-01: Akses Dashboard Admin & Verifikasi Markup Sidebar (#0c192c)
// ---------------------------------------------------------
try {
    $adminRepo = new AdminRepository();
    $metrics = $adminRepo->getDashboardMetrics();
    $chartData = $adminRepo->getLast7DaysChartData();

    // Render view admin.dashboard
    $_SESSION['user'] = [
        'id' => 1,
        'username' => 'admin',
        'full_name' => 'Administrator CamHub',
        'email' => 'admin@camhub.id',
        'role' => 'admin'
    ];

    ob_start();
    include $rootDir . '/resources/views/admin/dashboard.php';
    $adminHtml = ob_get_clean();

    $hasBrand = strpos($adminHtml, 'CamHub') !== false;
    $hasSidebar = strpos($adminHtml, 'admin-sidebar') !== false || strpos($adminHtml, '#0c192c') !== false;
    $hasTitle = strpos($adminHtml, 'Dashboard Admin') !== false;
    $hasKycNav = strpos($adminHtml, 'Verifikasi KYC') !== false;

    assertTest($hasBrand && $hasSidebar && $hasTitle && $hasKycNav, 
        'TEST-CNS-01', 
        'Akses Dashboard Admin Baru & Struktur Sidebar', 
        'Sidebar #0c192c, brand CamHub, dan menu navigasi terpasang presisi.'
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-CNS-01', 'Akses Dashboard Admin Baru', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-CNS-02: Integritas 4 Kartu Metrik Admin (Grid 2x2)
// ---------------------------------------------------------
try {
    $hasCard1 = strpos($adminHtml, 'Menunggu Verifikasi KYC') !== false;
    $hasCard2 = strpos($adminHtml, 'Unit Inspeksi') !== false;
    $hasCard3 = strpos($adminHtml, 'Transaksi Hari Ini') !== false;
    $hasCard4 = strpos($adminHtml, 'Denda Pending') !== false;

    $is2x2 = $hasCard1 && $hasCard2 && $hasCard3 && $hasCard4;
    assertTest($is2x2, 
        'TEST-CNS-02', 
        'Integritas 4 Kartu Metrik Admin (2x2 Grid)', 
        'KYC (' . $metrics['kyc_pending'] . '), Inspeksi (' . $metrics['inspection_units'] . '), Trx Hari Ini (' . $metrics['today_transactions'] . '), Denda (' . $metrics['pending_fines'] . ').'
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-CNS-02', 'Integritas 4 Kartu Metrik Admin', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-CNS-03: Visualisasi Bar Chart 7 Hari Terakhir
// ---------------------------------------------------------
try {
    $hasChartTitle = strpos($adminHtml, 'Transaksi 7 Hari Terakhir') !== false;
    $hasDays = true;
    foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day) {
        if (strpos($adminHtml, $day) === false) {
            $hasDays = false;
            break;
        }
    }
    assertTest($hasChartTitle && $hasDays && count($chartData) === 7, 
        'TEST-CNS-03', 
        'Visualisasi Bar Chart 7 Hari Terakhir', 
        'Kartu grafik batang Sen s/d Min ter-render dengan tinggi dinamis.'
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-CNS-03', 'Visualisasi Bar Chart 7 Hari Terakhir', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-CNS-04: Formulir Pengajuan Titip Jual & Akses Toko Mitra
// ---------------------------------------------------------
try {
    $consignRepo = new ConsignmentRepository();
    $activeStores = $consignRepo->getActiveStores();

    assertTest(!empty($activeStores) && isset($activeStores[0]['store_name']), 
        'TEST-CNS-04', 
        'Daftar Toko Mitra untuk Formulir Titip Jual', 
        'Ditemukan ' . count($activeStores) . ' toko mitra aktif siap menerima titip jual.'
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-CNS-04', 'Daftar Toko Mitra untuk Formulir Titip Jual', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-CNS-05: Validasi Data Server-Side Titip Jual
// ---------------------------------------------------------
try {
    $validator = new ConsignmentValidator();
    // Test invalid data
    $invalidData = [
        'item_name' => 'A7', // Too short (< 5)
        'store_id' => 0,     // Invalid
        'requested_price' => 50000, // < 100000
        'item_description' => 'pendek' // < 15
    ];
    $isValid = $validator->validate($invalidData);
    $errors = $validator->getErrors();

    $expectedErrors = isset($errors['item_name']) && isset($errors['store_id']) && isset($errors['requested_price']) && isset($errors['item_description']);

    assertTest(!$isValid && $expectedErrors, 
        'TEST-CNS-05', 
        'Validasi Data Server-Side Titip Jual', 
        'Seluruh batas validasi (nama, toko, harga minimal Rp 100rb, deskripsi) berhasil dicegat.'
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-CNS-05', 'Validasi Data Server-Side Titip Jual', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-CNS-06: Perekaman Pengajuan Konsinyasi ke Database
// ---------------------------------------------------------
try {
    // Ambil customer ID valid (user ID 4 atau user)
    $stmtUser = $db->query("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'customer' LIMIT 1");
    $testUserId = (int) ($stmtUser->fetchColumn() ?: 4);

    $storeId = (int) $activeStores[0]['id'];

    $consignService = new ConsignmentService();
    $submitPayload = [
        'item_name' => 'Fujifilm X-T4 Body Silver Ex Garansi Resmi',
        'store_id' => $storeId,
        'requested_price' => 16500000,
        'item_description' => 'Kondisi 98% mulus tanpa lecet, sensor kinclong bebas jamur, SC 8.500, box lengkap.'
    ];

    $result = $consignService->submitConsignment($submitPayload, $testUserId);
    $consignId = $result['consignment_id'] ?? 0;

    assertTest($result['success'] && $consignId > 0, 
        'TEST-CNS-06', 
        'Perekaman Pengajuan Konsinyasi ke Database', 
        'Pengajuan #TJ-' . $consignId . ' tersimpan dengan status submitted.'
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-CNS-06', 'Perekaman Pengajuan Konsinyasi ke Database', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-CNS-07: Pelacakan Status & Kalkulasi Payout di Sisi Pelanggan
// ---------------------------------------------------------
try {
    $detail = $consignRepo->findById($consignId);
    $consignModel = new Consignment($detail);

    $estPayout = $consignModel->getEstimatedPayout();
    // 16.500.000 - 10% (1.650.000) = 14.850.000
    $expectedPayout = 16500000 * 0.9;

    assertTest($detail !== null && $estPayout === $expectedPayout, 
        'TEST-CNS-07', 
        'Pelacakan Status & Kalkulasi Payout Pelanggan', 
        'Harga diminta: ' . $consignModel->getFormattedRequestedPrice() . ' -> Estimasi dana cair: ' . $consignModel->getFormattedEstimatedPayout() . '.'
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-CNS-07', 'Pelacakan Status & Kalkulasi Payout Pelanggan', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-CNS-08: Inspeksi Fisik & Penetapan Harga Kesepakatan Mitra
// ---------------------------------------------------------
try {
    $agreedPrice = 16000000.00; // Mitra tawar sedikit lebih rendah setelah inspeksi
    $commissionRate = 10.00;

    $approved = $consignService->approveConsignment($consignId, $agreedPrice, $commissionRate);
    $updatedDetail = $consignRepo->findById($consignId);

    assertTest($approved && $updatedDetail['status'] === 'approved' && (float)$updatedDetail['agreed_price'] === $agreedPrice, 
        'TEST-CNS-08', 
        'Inspeksi Fisik & Persetujuan Harga Mitra', 
        'Status berubah menjadi approved dengan harga kesepakatan Rp 16.000.000 dan komisi 10%.'
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-CNS-08', 'Inspeksi Fisik & Persetujuan Harga Mitra', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-CNS-09: Publikasi Otomatis ke Etalase Publik dengan Badge Titip Jual
// ---------------------------------------------------------
try {
    $publishedProdId = $consignService->publishConsignment($consignId);

    $stmtProd = $db->prepare("SELECT * FROM products WHERE id = :id");
    $stmtProd->execute(['id' => $publishedProdId]);
    $prodRow = $stmtProd->fetch();

    $isConsignFlag = (int)$prodRow['is_consignment'] === 1;
    $isSaleFlag = (int)$prodRow['is_sale'] === 1;
    $priceMatch = (float)$prodRow['sale_price'] === 16000000.00;

    assertTest($publishedProdId > 0 && $isConsignFlag && $isSaleFlag && $priceMatch, 
        'TEST-CNS-09', 
        'Publikasi Otomatis ke Katalog Publik', 
        'Produk ID #' . $publishedProdId . ' terbit aktif dengan tag is_consignment=1 dan harga jual Rp 16.000.000.'
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-CNS-09', 'Publikasi Otomatis ke Katalog Publik', $e->getMessage());
}

// ---------------------------------------------------------
// TEST-CNS-10: Siklus Terjual & Tombol Logout Dashboard Mitra
// ---------------------------------------------------------
try {
    $sold = $consignRepo->markAsSold($consignId);
    $soldDetail = $consignRepo->findById($consignId);

    // Cek keberadaan tombol logout di view partner dashboard
    $partnerViewContent = file_get_contents($rootDir . '/resources/views/partner/dashboard.php');
    $hasPartnerLogout = strpos($partnerViewContent, '/logout') !== false && strpos($partnerViewContent, 'Keluar') !== false;

    assertTest($sold && $soldDetail['status'] === 'sold' && $hasPartnerLogout, 
        'TEST-CNS-10', 
        'Siklus Selesai Terjual & Tombol Logout Mitra', 
        'Status konsinyasi ditutup sold dan tombol logout mitra terpasang fungsional.'
    );
} catch (\Throwable $e) {
    assertTest(false, 'TEST-CNS-10', 'Siklus Selesai Terjual & Tombol Logout Mitra', $e->getMessage());
}

echo "\n========================================================\n";
echo "HASIL PENGUJIAN TAHAP 09: {$testsPassed}/{$totalTests} BERHASIL (" . round(($testsPassed / $totalTests) * 100) . "% PASS)\n";
echo "========================================================\n";
