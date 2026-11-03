<?php

declare(strict_types=1);

/**
 * Migration CLI Runner - CamHub
 * 
 * Executes database/schema.sql on MySQL server.
 * Usage: php database/migrate.php
 */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/app/Helpers/functions.php';

if (file_exists($rootDir . '/.env')) {
    load_env($rootDir . '/.env');
}

echo "========================================================\n";
echo "           CAMHUB DATABASE MIGRATION RUNNER            \n";
echo "========================================================\n\n";

$host = env('DB_HOST', '127.0.0.1');
$port = (int) env('DB_PORT', 3306);
$dbname = env('DB_NAME', 'camhub_db');
$user = env('DB_USER', 'root');
$pass = env('DB_PASS', '');

try {
    // 1. Connect without db to ensure database exists
    echo "[1/3] Memeriksa ketersediaan basis data '{$dbname}'...\n";
    $rootPdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "      Basis data '{$dbname}' siap digunakan.\n\n";

    // 2. Connect to the target database
    echo "[2/3] Menghubungkan ke basis data '{$dbname}'...\n";
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "      Koneksi PDO aktif.\n\n";

    // 3. Read and execute schema.sql
    echo "[3/3] Mengeksekusi berkas DDL (database/schema.sql)...\n";
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new RuntimeException("Berkas schema.sql tidak ditemukan di {$schemaFile}");
    }

    $sql = file_get_contents($schemaFile);
    $pdo->exec($sql);
    echo "      Skema SQL berhasil dieksekusi.\n\n";

    // 4. Verify created tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "--------------------------------------------------------\n";
    echo "DAFTAR TABEL YANG TERBENTUK (" . count($tables) . " Tabel):\n";
    foreach ($tables as $index => $tableName) {
        $num = str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT);
        echo "  [{$num}] {$tableName}\n";
    }

    echo "--------------------------------------------------------\n";
    if (count($tables) === 22) {
        echo "STATUS: SUKSES (22 / 22 TABEL TERVERIFIKASI)\n";
        echo "========================================================\n";
        exit(0);
    } else {
        echo "PERINGATAN: Jumlah tabel (" . count($tables) . ") tidak sesuai target (22).\n";
        echo "========================================================\n";
        exit(1);
    }

} catch (Throwable $e) {
    echo "\n[ERROR] Migrasi gagal: " . $e->getMessage() . "\n";
    echo "========================================================\n";
    exit(1);
}

