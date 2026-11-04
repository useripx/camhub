<?php

declare(strict_types=1);

/**
 * CamHub — Entry Point & Front Controller
 * 
 * @package CamHub
 * @author Kelompok 1 (Yogi, Regie, Ivan, Krisna)
 */

// Define directory constants
define('CAMHUB_START', microtime(true));
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEW_PATH', ROOT_PATH . '/resources/views');

// 1. Send HTTP Security Headers Early
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Permitted-Cross-Domain-Policies: none');
}

// 2. Load Global Helpers
require_once APP_PATH . '/Helpers/functions.php';

// 3. Load Environment Variables (.env)
if (file_exists(ROOT_PATH . '/.env')) {
    load_env(ROOT_PATH . '/.env');
}

// 4. Configure PHP Error Reporting & Logging
$appDebug = (bool) env('APP_DEBUG', true);
if ($appDebug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', STORAGE_PATH . '/logs/error.log');

    // Production Graceful Exception Handler
    set_exception_handler(function (\Throwable $e) {
        $logMsg = date('[Y-m-d H:i:s]') . ' EXCEPTION: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
        @error_log($logMsg, 3, STORAGE_PATH . '/logs/error.log');

        http_response_code(500);
        if (function_exists('view')) {
            echo view('errors.500');
        } else {
            echo "<!DOCTYPE html><html><head><title>500 - Server Error</title></head><body style='font-family:sans-serif;text-align:center;padding:50px;'><h1>500 | Terjadi Kendala Sistem</h1><p>Sistem CamHub sedang dalam pemeliharaan berkala.</p></body></html>";
        }
        exit;
    });
}

// 5. Set Timezone
date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Jakarta'));

// 6. Native PSR-4 Autoloader for App namespace
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

// 7. Secure Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_secure' => (bool) env('SESSION_SECURE', false),
    ]);
}

// 8. Load Routes
require_once ROOT_PATH . '/routes/web.php';

// 9. Dispatch Incoming Request
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

$response = \App\Core\Router::dispatch($requestMethod, $requestUri);

if (is_string($response)) {
    echo $response;
}
