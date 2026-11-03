<?php

declare(strict_types=1);

/**
 * Database Automated Test Suite - CamHub
 * 
 * Verifies that all database requirements of Tahap 02 are 100% PASS.
 * 
 * Usage: php tests/test_database.php
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
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require_once $file;
});

echo "========================================================\n";
echo "   CAMHUB AUTOMATED TEST SUITE — TAHAP 02 DATABASE      \n";
echo "========================================================\n\n";

$testsTotal = 0;
$testsPassed = 0;
$errors = [];

$assert_test = function (string $id, string $title, bool $condition, ?string $errorMessage = null) use (&$testsTotal, &$testsPassed, &$errors) {
    $testsTotal++;
    if ($condition) {
        $testsPassed++;
        echo "  [PASS] {$id} — {$title}\n";
    } else {
        $msg = $errorMessage ?? "Test verification failed.";
        $errors[] = "{$id}: {$title} -> {$msg}";
        echo "  [FAIL] {$id} — {$title}: {$msg}\n";
    }
};

try {
    // TEST-DB-01: PDO Connection via Singleton
    $pdo = \App\Core\Database::getConnection();
    $serverVersion = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    $assert_test("TEST-DB-01", "PDO Singleton Connection Active", !empty($serverVersion), "Gagal mendapatkan versi server MySQL");

    // TEST-DB-02: All 22 Tables Exist
    $requiredTables = [
        'roles', 'users', 'user_profiles', 'kyc_submissions', 'partners',
        'partner_stores', 'categories', 'products', 'product_images', 'product_units',
        'rental_pricing', 'rental_bookings', 'sales_orders', 'sales_order_items',
        'consignments', 'payments', 'payment_transactions', 'escrow_transactions',
        'fines', 'inspections', 'notifications', 'audit_logs'
    ];

    $stmt = $pdo->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $missingTables = array_diff($requiredTables, $existingTables);
    $allTablesExist = count($missingTables) === 0 && count($existingTables) === 22;
    $assert_test("TEST-DB-02", "All 22 Relational Tables Exist in MySQL", $allTablesExist, "Tabel hilang: " . implode(', ', $missingTables));

    // TEST-DB-03: Foreign Key Constraints Validation
    $stmtFK = $pdo->prepare("
        SELECT COUNT(*) FROM information_schema.table_constraints 
        WHERE constraint_schema = :db AND constraint_type = 'FOREIGN KEY'
    ");
    $stmtFK->execute(['db' => env('DB_NAME', 'camhub_db')]);
    $fkCount = (int)$stmtFK->fetchColumn();
    $assert_test("TEST-DB-03", "Foreign Key Constraints Registered (Found {$fkCount})", $fkCount >= 18, "Jumlah foreign key ({$fkCount}) kurang dari batas minimal 18");

    // TEST-DB-04: Key Indexes (products.slug, categories.slug, rental_bookings.dates)
    $stmtIdx = $pdo->prepare("
        SELECT COUNT(DISTINCT index_name) FROM information_schema.statistics 
        WHERE table_schema = :db AND index_name IN ('idx_products_slug', 'idx_bookings_dates', 'slug')
    ");
    $stmtIdx->execute(['db' => env('DB_NAME', 'camhub_db')]);
    $idxCount = (int)$stmtIdx->fetchColumn();
    $assert_test("TEST-DB-04", "Key Performance Indexes Active", $idxCount >= 2, "Indeks performa tidak ditemukan lengkap");

    // TEST-DB-05: Master Roles & Categories Seeded
    $rolesCount = (int)$pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
    $categoriesCount = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $masterValid = ($rolesCount === 3 && $categoriesCount === 5);
    $assert_test("TEST-DB-05", "Master Data Integrity (3 Roles & 5 Categories)", $masterValid, "Roles: {$rolesCount}, Categories: {$categoriesCount}");

    // TEST-DB-06: Mockup Reference Cameras & Users Seeded
    $productCount = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $unitCount = (int)$pdo->query("SELECT COUNT(*) FROM product_units")->fetchColumn();
    $pricingCount = (int)$pdo->query("SELECT COUNT(*) FROM rental_pricing")->fetchColumn();
    $regiUser = $pdo->query("SELECT full_name FROM users WHERE email = 'regi@camhub.id'")->fetchColumn();
    $yogiStore = $pdo->query("SELECT store_name FROM partner_stores WHERE store_name = 'Yogi Camera'")->fetchColumn();

    $mockupDataValid = ($productCount >= 6 && $unitCount >= 13 && $pricingCount >= 24 && $regiUser === 'Mohammad Regieyanto' && $yogiStore === 'Yogi Camera');
    $assert_test("TEST-DB-06", "Mockup Prototype Reference Data Seeded (6 Cameras, 13 Units)", $mockupDataValid, "Data mockup tidak lengkap (Products: {$productCount}, Units: {$unitCount})");

    // TEST-DB-07: Database Transaction ACID Rollback Test
    $testPassedRollback = false;
    try {
        \App\Core\Database::transaction(function (PDO $conn) {
            $conn->exec("INSERT INTO categories (name, slug, icon) VALUES ('Test Rollback Cat', 'test-rollback', 'test')");
            // Deliberately throw exception to trigger rollback
            throw new RuntimeException("Simulated Rollback Trigger");
        });
    } catch (RuntimeException $e) {
        $check = (int)$pdo->query("SELECT COUNT(*) FROM categories WHERE slug = 'test-rollback'")->fetchColumn();
        $testPassedRollback = ($check === 0);
    }
    $assert_test("TEST-DB-07", "ACID Transaction & Rollback Integrity", $testPassedRollback, "Baris transaksi rollback masih tersisa di basis data");

} catch (Throwable $e) {
    echo "\n[EXCEPTION] Gagal menjalankan pengujian: " . $e->getMessage() . "\n";
    $testsTotal++;
    $errors[] = $e->getMessage();
}

echo "\n--------------------------------------------------------\n";
echo "HASIL PENGUJIAN: {$testsPassed} / {$testsTotal} LULUS (" . round(($testsPassed / $testsTotal) * 100) . "%)\n";

if ($testsPassed === $testsTotal && $testsTotal > 0) {
    echo "STATUS: 100% PASS (0 GAGAL / 0 ERROR) — VERIFIKASI SELESAI\n";
    echo "========================================================\n";
    exit(0);
} else {
    echo "STATUS: GAGAL (" . count($errors) . " Error Ditemukan)\n";
    echo "========================================================\n";
    exit(1);
}
