<?php

/**
 * Foundation Automated Test Suite - CamHub
 * 
 * Verifies that all architectural requirements of Tahap 01 are 100% PASS.
 * 
 * Usage: php tests/test_foundation.php
 */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/app/Helpers/functions.php';

// Bootstrap environment
if (file_exists($rootDir . '/.env')) {
    load_env($rootDir . '/.env');
}

// Autoloader for tests
spl_autoload_register(function ($class) use ($rootDir) {
    $prefix = 'App\\';
    $baseDir = $rootDir . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require_once $file;
});

echo "========================================================\n";
echo "   CAMHUB AUTOMATED TEST SUITE — TAHAP 01 FOUNDATION    \n";
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

// TEST-01: Git Status & Remote Origin
$gitConfig = @file_get_contents($rootDir . '/.git/config');
$hasRemote = $gitConfig && (str_contains($gitConfig, 'github.com/useripx/camhub.git') || str_contains($gitConfig, 'useripx/camhub'));
$assert_test("TEST-01", "Git Repository Initialized & Remote Set", (bool)$hasRemote, "Remote origin GitHub tidak ditemukan di .git/config");

// TEST-02: Secret Protection in .gitignore
$gitignore = @file_get_contents($rootDir . '/.gitignore');
$hasEnvIgnored = $gitignore && str_contains($gitignore, '.env');
$assert_test("TEST-02", "Secret Protection (.env in .gitignore)", (bool)$hasEnvIgnored, ".env belum diabaikan di .gitignore");

// TEST-03: Modular Architecture Directory Structure
$requiredDirs = [
    'app/Core',
    'app/Controllers',
    'app/Models',
    'app/Repositories',
    'app/Services',
    'app/Middleware',
    'app/Validators',
    'app/Helpers',
    'config',
    'database/migrations',
    'database/seeders',
    'public/assets/css',
    'resources/views',
    'routes',
    'storage/logs',
    'storage/uploads',
    'storage/cache',
    'Docs/scema',
    'changelog'
];

$allDirsExist = true;
$missingDir = '';
foreach ($requiredDirs as $d) {
    if (!is_dir($rootDir . '/' . $d)) {
        $allDirsExist = false;
        $missingDir = $d;
        break;
    }
}
$assert_test("TEST-03", "Modular Directory Structure Complete", $allDirsExist, "Direktori {$missingDir} tidak ditemukan");

// TEST-04: Front Controller & Router Dispatcher
require_once $rootDir . '/routes/web.php';
$routerClassExists = class_exists(\App\Core\Router::class);
$assert_test("TEST-04", "Front Controller & Router Class Loaded", $routerClassExists, "Class App\\Core\\Router tidak ditemukan");

// TEST-05: View Rendering Engine
$renderedView = view('welcome', [
    'title' => 'CamHub Test',
    'appName' => 'CamHub',
]);
$viewValid = str_contains($renderedView, 'CamHub') && str_contains($renderedView, 'CAMERA MARKETPLACE');
$assert_test("TEST-05", "View Renderer Produces Expected Output", $viewValid, "View welcome tidak menghasilkan markup yang sesuai");

// TEST-06: Centralized Configuration Files
$appConfig = require $rootDir . '/config/app.php';
$dbConfig = require $rootDir . '/config/database.php';
$configsValid = is_array($appConfig) && is_array($dbConfig) && ($appConfig['name'] === 'CamHub');
$assert_test("TEST-06", "Configuration Integrity (app.php & database.php)", $configsValid, "File konfigurasi tidak valid");

// TEST-07: Global Helper Functions
$rupiahFormatted = format_rupiah(250000);
$baseUrlWorks = !empty(base_url());
$assert_test("TEST-07", "Helper Functions (format_rupiah & base_url)", ($rupiahFormatted === 'Rp 250.000' && $baseUrlWorks), "Helper format_rupiah menghasilkan: {$rupiahFormatted}");

echo "\n--------------------------------------------------------\n";
echo "HASIL PENGUJIAN: {$testsPassed} / {$testsTotal} LULUS (" . round(($testsPassed / $testsTotal) * 100) . "%)\n";

if ($testsPassed === $testsTotal) {
    echo "STATUS: 100% PASS (0 GAGAL / 0 ERROR) — VERIFIKASI SELESAI\n";
    echo "========================================================\n";
    exit(0);
} else {
    echo "STATUS: GAGAL (" . count($errors) . " Error Ditemukan)\n";
    echo "========================================================\n";
    exit(1);
}
