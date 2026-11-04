<?php

declare(strict_types=1);

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

use App\Core\Database;

try {
    $db = Database::getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS `partner_withdrawals` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `partner_id` INT NOT NULL,
        `amount` DECIMAL(15, 2) NOT NULL,
        `bank_name` VARCHAR(50) NOT NULL,
        `account_number` VARCHAR(50) NOT NULL,
        `account_holder` VARCHAR(150) NOT NULL,
        `status` ENUM('pending', 'processed', 'rejected') NOT NULL DEFAULT 'pending',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT `fk_withdr_partner` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql);
    echo "MIGRATION: partner_withdrawals table verified/created successfully.\n";
} catch (Exception $e) {
    echo "MIGRATION ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
