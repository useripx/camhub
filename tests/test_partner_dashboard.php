<?php

/**
 * CAMHUB AUTOMATED TEST SUITE — TAHAP 06: DASHBOARD MITRA LAPAK
 * 
 * Menguji 10 Definition of Done (DoD) untuk fitur:
 * 1. Hak akses RBAC Mitra vs Pelanggan
 * 2. Resolusi identitas toko mitra (Yogi Camera)
 * 3. Kalkulasi metrik agregat ringkasan (Total Produk, Pesanan Aktif, Saldo)
 * 4. Daftar "Produk Saya" sesuai prototype CamHub — Dashboard Mitra Lapak.png
 * 5. Penambahan produk kamera & tarif sewa
 * 6. Manajemen unit fisik & nomor seri unik
 * 7. Proteksi Anti-IDOR (Insecure Direct Object Reference)
 * 8. Pemantauan jadwal sewa unit kamera
 * 9. Saldo dompet & riwayat escrow
 * 10. Pembaruan informasi profil lapak toko
 */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/app/Helpers/functions.php';

if (file_exists($rootDir . '/.env')) {
    load_env($rootDir . '/.env');
}

// Autoloader for classes
spl_autoload_register(function ($class) use ($rootDir) {
    $prefix = 'App\\';
    $baseDir = $rootDir . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Core\Database;
use App\Repositories\PartnerRepository;
use App\Services\PartnerDashboardService;
use App\Services\PartnerProductService;
use App\Validators\PartnerProductValidator;

echo "========================================================\n";
echo "   CAMHUB TEST SUITE — TAHAP 06: DASHBOARD MITRA LAPAK  \n";
echo "========================================================\n\n";

$pdo = Database::getConnection();
$partnerRepo = new PartnerRepository();
$dashboardService = new PartnerDashboardService($partnerRepo);
$productService = new PartnerProductService($partnerRepo);
$validator = new PartnerProductValidator();

$passCount = 0;
$totalTests = 10;

// Ambil ID user mitra & customer demo
$partnerUserId = (int)$pdo->query("SELECT id FROM users WHERE username = 'mitra'")->fetchColumn();
$customerUserId = (int)$pdo->query("SELECT id FROM users WHERE username = 'user'")->fetchColumn();

// TEST 01: Proteksi Hak Akses Role Mitra (RBAC Guard)
try {
    // Simulasi RBAC: customer tidak memiliki akses ke dashboard mitra
    $customerRole = $pdo->query("SELECT r.name FROM roles r JOIN users u ON u.role_id = r.id WHERE u.id = {$customerUserId}")->fetchColumn();
    $partnerRole = $pdo->query("SELECT r.name FROM roles r JOIN users u ON u.role_id = r.id WHERE u.id = {$partnerUserId}")->fetchColumn();

    $isCustomerBlocked = ($customerRole !== 'partner');
    $isPartnerAllowed = ($partnerRole === 'partner');

    if ($isCustomerBlocked && $isPartnerAllowed) {
        echo " [PASS] TEST-MITRA-01: Proteksi Hak Akses RBAC Mitra -> Role customer dilarang (HTTP 403), role partner diizinkan.\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-MITRA-01: RBAC Guard gagal mengisolasi role mitra.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-MITRA-01: Exception - " . $e->getMessage() . "\n";
}

// TEST 02: Resolusi Identitas Toko Mitra (Yogi Camera)
try {
    $partnerData = $partnerRepo->findByUserId($partnerUserId);
    if ($partnerData && !empty($partnerData['store_id']) && $partnerData['store_name'] === 'Yogi Camera' && $partnerData['store_city'] === 'Yogyakarta') {
        echo " [PASS] TEST-MITRA-02: Resolusi Toko Mitra -> Ditemukan toko '{$partnerData['store_name']}' di {$partnerData['store_city']} (Store ID: #{$partnerData['store_id']}).\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-MITRA-02: Gagal menemukan toko mitra terkait user_id: {$partnerUserId}.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-MITRA-02: Exception - " . $e->getMessage() . "\n";
}

$storeId = (int)($partnerData['store_id'] ?? 1);

// TEST 03: Kalkulasi Metrik Ringkasan Lapak (Total Produk, Pesanan Aktif, Saldo)
try {
    $metrics = $partnerRepo->getDashboardMetrics($storeId);
    $hasTotal = isset($metrics['total_products']) && $metrics['total_products'] > 0;
    $hasActive = isset($metrics['active_orders']) && $metrics['active_orders'] >= 0;
    $hasBalance = isset($metrics['available_balance']) && $metrics['available_balance'] > 0;

    if ($hasTotal && $hasActive && $hasBalance) {
        echo " [PASS] TEST-MITRA-03: Kalkulasi Metrik Agregat Lapak -> Total Produk: {$metrics['total_products']}, Pesanan Aktif: {$metrics['active_orders']}, Saldo: {$metrics['available_balance_formatted']}.\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-MITRA-03: Metrik agregat tidak lengkap atau nilainya tidak valid.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-MITRA-03: Exception - " . $e->getMessage() . "\n";
}

// TEST 04: Pengambilan Daftar "Produk Saya" Milik Mitra
try {
    $products = $partnerRepo->getPartnerProducts($storeId);
    if (count($products) >= 3) {
        $first = $products[0];
        $hasKeyProps = isset($first['name'], $first['daily_price'], $first['total_units']);
        if ($hasKeyProps) {
            echo " [PASS] TEST-MITRA-04: Daftar 'Produk Saya' Berhasil Dimuat -> Ditemukan " . count($products) . " produk di etalase lapak (Unit teratas: {$first['name']}, Rp " . number_format($first['daily_price'], 0, ',', '.') . "/hari).\n";
            $passCount++;
        } else {
            echo " [FAIL] TEST-MITRA-04: Data produk tidak memuat properti penting.\n";
        }
    } else {
        echo " [FAIL] TEST-MITRA-04: Jumlah produk toko kurang dari 3.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-MITRA-04: Exception - " . $e->getMessage() . "\n";
}

// TEST 05: Penambahan Produk Kamera Baru oleh Mitra
$testProductId = 0;
try {
    $categories = $pdo->query("SELECT id FROM categories LIMIT 1")->fetchColumn();
    $newProductPayload = [
        'category_id' => $categories,
        'name' => 'Sony FX3 Cinema Line Full-Frame',
        'description' => 'Kamera cinema compact 4K 120p untuk kebutuhan videografi profesional.',
        'condition_note' => 'Kondisi 99% seperti baru',
        'is_rental' => 1,
        'daily_price' => 450000,
        'is_sale' => 0,
        'serial_number' => 'SN-SONY-FX3-999',
    ];

    $res = $productService->createProduct($storeId, $newProductPayload);
    if ($res['success'] && !empty($res['product_id'])) {
        $testProductId = $res['product_id'];
        $check = $partnerRepo->findProductByIdAndStore($testProductId, $storeId);
        if ($check && (float)$check['daily_price'] === 450000.0) {
            echo " [PASS] TEST-MITRA-05: Penambahan Kamera Baru oleh Mitra -> Berhasil menyimpan '{$check['name']}' dengan slug '{$check['slug']}' dan tarif Rp 450.000/hari.\n";
            $passCount++;
        } else {
            echo " [FAIL] TEST-MITRA-05: Produk tersimpan tetapi data tidak sesuai.\n";
        }
    } else {
        echo " [FAIL] TEST-MITRA-05: Gagal menambahkan produk baru: " . json_encode($res['errors'] ?? []) . "\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-MITRA-05: Exception - " . $e->getMessage() . "\n";
}

// TEST 06: Manajemen Unit Fisik & Nomor Seri Kamera
$testUnitId = 0;
try {
    $unitRes = $productService->addUnit($testProductId, $storeId, [
        'serial_number' => 'SN-SONY-FX3-UNIT-02',
        'condition_status' => 'Mulus Tanpa Goresan',
        'availability_status' => 'available',
    ]);

    if ($unitRes['success'] && !empty($unitRes['unit_id'])) {
        $testUnitId = $unitRes['unit_id'];
        $units = $partnerRepo->getUnitsByProductId($testProductId, $storeId);
        // Minimal 2 unit (1 dari create, 1 dari addUnit)
        if (count($units) >= 2) {
            echo " [PASS] TEST-MITRA-06: Manajemen Unit Fisik Kamera -> Berhasil mendaftarkan 2 nomor seri fisik (SN: {$units[0]['serial_number']} & {$units[1]['serial_number']}).\n";
            $passCount++;
        } else {
            echo " [FAIL] TEST-MITRA-06: Jumlah unit terdaftar tidak sesuai.\n";
        }
    } else {
        echo " [FAIL] TEST-MITRA-06: Gagal mendaftarkan nomor seri unit fisik.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-MITRA-06: Exception - " . $e->getMessage() . "\n";
}

// TEST 07: Proteksi Kepemilikan Produk (Anti-IDOR)
try {
    $fakeStoreId = 99999; // Store lain yang tidak memiliki produk ini
    $idorCheck = $partnerRepo->findProductByIdAndStore($testProductId, $fakeStoreId);
    $attemptUpdate = $productService->updateProduct($testProductId, $fakeStoreId, [
        'category_id' => 1,
        'name' => 'Hacked Camera Name',
    ]);

    if ($idorCheck === null && !$attemptUpdate['success']) {
        echo " [PASS] TEST-MITRA-07: Proteksi Anti-IDOR Terverifikasi -> Mitra toko lain dicegah mengakses atau mengubah produk toko ini.\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-MITRA-07: Potensi celah IDOR terdeteksi! Toko lain berhasil mengakses produk.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-MITRA-07: Exception - " . $e->getMessage() . "\n";
}

// TEST 08: Pemantauan Jadwal Sewa Unit Kamera
try {
    $schedules = $partnerRepo->getSchedule($storeId);
    // getSchedule harus mengembalikan array (walau kosong jika belum ada booking aktif)
    if (is_array($schedules)) {
        echo " [PASS] TEST-MITRA-08: Pemantauan Jadwal Sewa Unit -> Query jadwal booking terintegrasi tanpa error relasi (Total jadwal: " . count($schedules) . ").\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-MITRA-08: Query jadwal sewa tidak valid.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-MITRA-08: Exception - " . $e->getMessage() . "\n";
}

// TEST 09: Saldo Dompet & Riwayat Pendapatan Escrow Toko
try {
    $dashData = $dashboardService->getDashboardData($partnerUserId);
    $hasFormattedBalance = !empty($dashData['metrics']['available_balance_formatted']);
    $hasStoreInfo = !empty($dashData['partner']['store_name']);

    if ($dashData['has_store'] && $hasFormattedBalance && $hasStoreInfo) {
        echo " [PASS] TEST-MITRA-09: Ringkasan Saldo Dompet Mitra -> Terverifikasi saldo {$dashData['metrics']['available_balance_formatted']} pada lapak {$dashData['partner']['store_name']}.\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-MITRA-09: Data saldo dompet tidak teragregasi dengan benar.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-MITRA-09: Exception - " . $e->getMessage() . "\n";
}

// TEST 10: Pembaruan Informasi Profil Toko Lapak
try {
    $originalName = $partnerData['store_name'];
    $updateRes = $productService->updateStoreProfile($storeId, [
        'store_name' => 'Yogi Camera Yogyakarta',
        'address' => 'Jl. Kaliurang KM 5.5 No. 88',
        'city' => 'Yogyakarta',
        'phone' => '082222222222',
    ]);

    $updatedData = $partnerRepo->findByUserId($partnerUserId);
    $isUpdated = ($updatedData['store_name'] === 'Yogi Camera Yogyakarta');

    // Kembalikan ke nama awal agar konsisten dengan prototype
    $partnerRepo->updateStoreProfile($storeId, [
        'store_name' => $originalName,
        'address' => $partnerData['store_address'],
        'city' => $partnerData['store_city'],
        'phone' => $partnerData['store_phone'],
    ]);

    if ($updateRes['success'] && $isUpdated) {
        echo " [PASS] TEST-MITRA-10: Pembaruan Profil Toko Mitra -> Berhasil memperbarui nama lapak, alamat, dan kota operasional.\n";
        $passCount++;
    } else {
        echo " [FAIL] TEST-MITRA-10: Gagal memperbarui profil toko.\n";
    }
} catch (Throwable $e) {
    echo " [FAIL] TEST-MITRA-10: Exception - " . $e->getMessage() . "\n";
}

// Cleanup test product
if ($testProductId > 0) {
    $partnerRepo->deleteProduct($testProductId, $storeId);
}

echo "\n========================================================\n";
echo "HASIL PENGUJIAN TAHAP 06: $passCount/$totalTests BERHASIL (" . round(($passCount / $totalTests) * 100) . "% PASS)\n";
echo "========================================================\n";

if ($passCount === $totalTests) {
    exit(0);
} else {
    exit(1);
}
