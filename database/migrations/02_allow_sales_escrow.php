<?php

declare(strict_types=1);

/**
 * Migration: Allow sales_order escrow in escrow_transactions
 */

$rootDir = dirname(__DIR__, 2);
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

$pdo = \App\Core\Database::getConnection();

echo "Memeriksa skema escrow_transactions...\n";

// 1. Jadikan booking_id NULLable
$pdo->exec("ALTER TABLE escrow_transactions MODIFY booking_id INT NULL");

// 2. Tambahkan kolom sales_order_id jika belum ada
$stmt = $pdo->query("SHOW COLUMNS FROM escrow_transactions LIKE 'sales_order_id'");
$exists = $stmt->fetch();

if (!$exists) {
    echo "Menambahkan kolom 'sales_order_id' ke escrow_transactions...\n";
    $pdo->exec("
        ALTER TABLE escrow_transactions 
        ADD COLUMN sales_order_id INT NULL AFTER booking_id,
        ADD CONSTRAINT fk_escrow_order FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id) ON DELETE CASCADE
    ");
    echo "Kolom 'sales_order_id' dan foreign key berhasil ditambahkan.\n";
} else {
    echo "Kolom 'sales_order_id' sudah tersedia.\n";
}

echo "Migrasi 02_allow_sales_escrow selesai sukses.\n";
