<?php

declare(strict_types=1);

/**
 * Migration: Add username column to users table
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

// Check if username column exists
$stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'username'");
$exists = $stmt->fetch();

if (!$exists) {
    echo "Menambahkan kolom 'username' ke tabel users...\n";
    $pdo->exec("ALTER TABLE users ADD COLUMN username VARCHAR(50) NULL UNIQUE AFTER role_id");
    echo "Kolom 'username' berhasil ditambahkan.\n";
} else {
    echo "Kolom 'username' sudah tersedia di tabel users.\n";
}

// Update schema.sql as well
$schemaPath = $rootDir . '/database/schema.sql';
$schemaContent = file_get_contents($schemaPath);
if (strpos($schemaContent, '`username`') === false) {
    $schemaContent = str_replace(
        "`role_id` INT NOT NULL,\n    `full_name`",
        "`role_id` INT NOT NULL,\n    `username` VARCHAR(50) NULL UNIQUE,\n    `full_name`",
        str_replace("\r\n", "\n", $schemaContent)
    );
    file_put_contents($schemaPath, str_replace("\n", "\r\n", $schemaContent));
    echo "database/schema.sql berhasil disinkronkan.\n";
}

