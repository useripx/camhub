<?php

/**
 * ============================================================================
 * CAMHUB AUTOMATED TEST SUITE — TAHAP 05: WEBSITE PUBLIK & KATALOG PRODUK
 * ============================================================================
 * Proyek        : CamHub – Camera Marketplace Platform
 * Tim Pengembang: Kelompok 1
 *                 1. Yogi Ario Pratama (Project Owner & Lead Developer)
 *                 2. Muhammad Regieyanto (Backend & System Architect)
 *                 3. Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
 *                 4. Krisna Mukti (Quality Assurance & Presentation Coordinator)
 * 
 * Target Acuan  :
 * 1. template/CamHub — Interactive UI UX Prototype/CamHub — Beranda.png
 * 2. template/CamHub — Interactive UI UX Prototype/CamHub — Katalog Produk.png
 * 3. template/CamHub — Interactive UI UX Prototype/CamHub — Detail Produk.png
 * ============================================================================
 */

declare(strict_types=1);

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
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Core\Database;
use App\Models\Product;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\ProductCatalogService;
use App\Services\RentalCalculatorService;

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function runTest(string $testId, string $description, callable $closure): void
{
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    try {
        $result = $closure();
        if ($result === true || (is_array($result) && $result['status'] === true)) {
            $passedTests++;
            $msg = is_array($result) && !empty($result['msg']) ? " -> " . $result['msg'] : '';
            echo " [PASS] {$testId}: {$description}{$msg}\n";
        } else {
            $failedTests++;
            $msg = is_array($result) && !empty($result['msg']) ? " -> " . $result['msg'] : '';
            echo " [FAIL] {$testId}: {$description}{$msg}\n";
        }
    } catch (\Throwable $e) {
        $failedTests++;
        echo " [ERROR] {$testId}: {$description} -> Exception: " . $e->getMessage() . "\n";
    }
}

echo "========================================================\n";
echo "   CAMHUB TEST SUITE — TAHAP 05: KATALOG & DETAIL PRODUK \n";
echo "========================================================\n\n";

$categoryRepo = new CategoryRepository();
$productRepo = new ProductRepository();
$catalogService = new ProductCatalogService();
$calculator = new RentalCalculatorService();

// TEST 01
runTest('TEST-CAT-01', 'Pembacaan Master Kategori Fotografi & Hitungan Produk Aktif', function () use ($categoryRepo) {
    $categories = $categoryRepo->getAllWithCount();
    $foundSlugs = array_map(fn($c) => $c->slug, $categories);
    $hasBody = in_array('kamera-body', $foundSlugs, true);
    $hasLensa = in_array('lensa', $foundSlugs, true);

    return [
        'status' => (count($categories) >= 5 && $hasBody && $hasLensa),
        'msg'    => "Ditemukan " . count($categories) . " kategori aktif dengan slug 'kamera-body' & 'lensa'."
    ];
});

// TEST 02
runTest('TEST-CAT-02', 'Pengambilan Produk Unggulan Beranda (Populer Disewa) dengan Relasi Toko', function () use ($productRepo) {
    $featured = $productRepo->getFeaturedForHome(6);
    $allHavePrices = true;
    $allHaveStores = true;

    foreach ($featured as $p) {
        if ($p->daily_price <= 0) $allHavePrices = false;
        if (empty($p->store_name)) $allHaveStores = false;
    }

    return [
        'status' => (count($featured) >= 5 && $allHavePrices && $allHaveStores),
        'msg'    => "Berhasil memuat " . count($featured) . " kamera unggulan berelasi toko dan tarif sewa."
    ];
});

// TEST 03
runTest('TEST-CAT-03', 'Fungsionalitas Pencarian Kata Kunci Kamera (Search Keyword)', function () use ($productRepo) {
    $searchResult = $productRepo->getCatalogFiltered(['q' => 'Sony'], 1, 10);
    $allMatch = true;

    foreach ($searchResult['data'] as $p) {
        if (!str_contains(strtolower($p->name), 'sony') && !str_contains(strtolower($p->description ?? ''), 'sony')) {
            $allMatch = false;
        }
    }

    return [
        'status' => ($searchResult['total'] >= 1 && $allMatch),
        'msg'    => "Pencarian 'Sony' menghasilkan " . $searchResult['total'] . " unit relevan."
    ];
});

// TEST 04
runTest('TEST-CAT-04', 'Filter Katalog Berdasarkan Kategori (Category Slug Filtering)', function () use ($productRepo) {
    $filtered = $productRepo->getCatalogFiltered(['category' => 'kamera-body'], 1, 10);
    $allBody = true;

    foreach ($filtered['data'] as $p) {
        if ($p->category_slug !== 'kamera-body') {
            $allBody = false;
        }
    }

    return [
        'status' => ($filtered['total'] >= 3 && $allBody),
        'msg'    => "Ditemukan " . $filtered['total'] . " kamera dalam kategori 'kamera-body'."
    ];
});

// TEST 05
runTest('TEST-CAT-05', 'Filter Katalog Berdasarkan Rentang Harga Sewa Harian (Min & Max Price)', function () use ($productRepo) {
    $min = 160000;
    $max = 220000;
    $priceFiltered = $productRepo->getCatalogFiltered(['min_price' => $min, 'max_price' => $max], 1, 10);
    $allInRange = true;

    foreach ($priceFiltered['data'] as $p) {
        if ($p->daily_price < $min || $p->daily_price > $max) {
            $allInRange = false;
        }
    }

    return [
        'status' => ($priceFiltered['total'] >= 1 && $allInRange),
        'msg'    => "Filter tarif Rp {$min} s.d Rp {$max} menghasilkan " . $priceFiltered['total'] . " produk akurat."
    ];
});

// TEST 06
runTest('TEST-CAT-06', 'Pengurutan Produk (Sorting): Harga Termurah & Termahal', function () use ($productRepo) {
    $ascResult = $productRepo->getCatalogFiltered(['sort' => 'price_asc'], 1, 10);
    $descResult = $productRepo->getCatalogFiltered(['sort' => 'price_desc'], 1, 10);

    $lowestPrice = $ascResult['data'][0]->daily_price ?? 0;
    $highestPrice = $descResult['data'][0]->daily_price ?? 0;

    return [
        'status' => ($lowestPrice <= $highestPrice && $lowestPrice > 0),
        'msg'    => "Harga termurah Rp " . number_format($lowestPrice, 0) . " <= Harga termahal Rp " . number_format($highestPrice, 0) . "."
    ];
});

// TEST 07
runTest('TEST-CAT-07', 'Fungsionalitas Pagination Katalog (Limit, Offset, Total Pages)', function () use ($productRepo) {
    $page1 = $productRepo->getCatalogFiltered([], 1, 2);
    $page2 = $productRepo->getCatalogFiltered([], 2, 2);

    $isPaged = ($page1['total'] >= 5 && count($page1['data']) === 2 && count($page2['data']) === 2);
    $isDistinct = ($page1['data'][0]->id !== $page2['data'][0]->id);

    return [
        'status' => ($isPaged && $isDistinct),
        'msg'    => "Total items: {$page1['total']}, per_page: 2, total halaman: {$page1['last_page']}."
    ];
});

// TEST 08
runTest('TEST-CAT-08', 'Pengambilan Detail Produk Berdasarkan Slug beserta Galeri & Toko', function () use ($productRepo) {
    $detail = $productRepo->findBySlug('canon-eos-r50');
    
    $hasStore = !empty($detail->store_name) && !empty($detail->store_city);
    $hasImages = !empty($detail->images);
    $hasPricing = !empty($detail->daily_price);

    return [
        'status' => ($detail !== null && $hasStore && $hasImages && $hasPricing),
        'msg'    => "Kamera '{$detail->name}' termuat lengkap dengan toko '{$detail->store_name}' di {$detail->store_city}."
    ];
});

// TEST 09
runTest('TEST-CAT-09', 'Pemeriksaan Ketersediaan Unit Fisik Kamera (Units Availability Status)', function () use ($productRepo) {
    $detail = $productRepo->findBySlug('fujifilm-x-t30');
    $count = $productRepo->countAvailableUnits($detail->id);

    return [
        'status' => ($count >= 1 && count($detail->units) >= 1),
        'msg'    => "Kamera ID #{$detail->id} memiliki {$count} unit fisik berstatus 'available'."
    ];
});

// TEST 10
runTest('TEST-CAT-10', 'Kalkulasi Logika Tarif Sewa & Diskon Durasi Bertingkat + Escrow', function () use ($calculator) {
    $dailyRate = 200000.0;

    // Skenario 1: 1 Hari (Normal, no discount, escrow 5.000) -> 200.000 + 5.000 = 205.000
    $est1 = $calculator->calculate($dailyRate, 1);
    $ok1 = ($est1['discount_amount'] == 0 && $est1['total_price'] == 205000);

    // Skenario 2: 3 Hari (Diskon 5% -> 600.000 - 30.000 = 570.000 + 5.000 = 575.000)
    $est3 = $calculator->calculate($dailyRate, 3);
    $ok3 = ($est3['discount_amount'] == 30000 && $est3['total_price'] == 575000);

    // Skenario 3: 7 Hari (Diskon 10% -> 1.400.000 - 140.000 = 1.260.000 + 5.000 = 1.265.000)
    $est7 = $calculator->calculate($dailyRate, 7);
    $ok7 = ($est7['discount_amount'] == 140000 && $est7['total_price'] == 1265000);

    return [
        'status' => ($ok1 && $ok3 && $ok7),
        'msg'    => "Formula durasi 1 hari (Rp 205k), 3 hari (Rp 575k), 7 hari (Rp 1.265k) 100% presisi matematis."
    ];
});

echo "\n========================================================\n";
echo "HASIL PENGUJIAN TAHAP 05: {$passedTests}/{$totalTests} BERHASIL (" . round(($passedTests / $totalTests) * 100) . "% PASS)\n";
echo "========================================================\n";

if ($failedTests > 0) {
    exit(1);
}
exit(0);
