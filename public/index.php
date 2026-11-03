<?php

declare(strict_types=1);

/**
 * CamHub — Entry Point & Front Controller
 * 
 * @package CamHub
 * @author Yogi Ario
 */

// Define directory constants
define('CAMHUB_START', microtime(true));
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEW_PATH', ROOT_PATH . '/resources/views');

// 1. Load Global Helpers
require_once APP_PATH . '/Helpers/functions.php';

// 2. Load Environment Variables (.env)
if (file_exists(ROOT_PATH . '/.env')) {
    load_env(ROOT_PATH . '/.env');
}

// 3. Configure PHP Error Reporting based on APP_DEBUG
if (env('APP_DEBUG', true)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', STORAGE_PATH . '/logs/error.log');
}

// 4. Set Timezone
date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Jakarta'));

// 5. Native PSR-4 Autoloader for App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';

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

// 6. Secure Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_secure' => (bool) env('SESSION_SECURE', false),
    ]);
}

// 7. Load Routes
require_once ROOT_PATH . '/routes/web.php';

// 8. Dispatch Incoming Request
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

$response = \App\Core\Router::dispatch($requestMethod, $requestUri);

if (is_string($response)) {
    echo $response;
}
