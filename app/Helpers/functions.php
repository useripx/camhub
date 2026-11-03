<?php

/**
 * Global Helper Functions - CamHub
 */

if (!function_exists('load_env')) {
    /**
     * Load environment variables from .env file
     */
    function load_env(string $filePath): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove surrounding quotes
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                    putenv(sprintf('%s=%s', $key, $value));
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }
}

if (!function_exists('env')) {
    /**
     * Get an environment variable with a default fallback
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }

        if ($value === 'true' || $value === '(true)') return true;
        if ($value === 'false' || $value === '(false)') return false;
        if ($value === 'null' || $value === '(null)') return null;

        return $value;
    }
}

if (!function_exists('base_url')) {
    /**
     * Generate a fully qualified URL to the application base or given path
     */
    function base_url(string $path = ''): string
    {
        $appUrl = rtrim(env('APP_URL', 'http://localhost/camhub/public'), '/');
        $path = ltrim($path, '/');
        return $path !== '' ? "{$appUrl}/{$path}" : $appUrl;
    }
}

if (!function_exists('asset_url')) {
    /**
     * Generate a URL for static assets
     */
    function asset_url(string $path = ''): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('view')) {
    /**
     * Render a view template with injected data
     */
    function view(string $viewName, array $data = []): string
    {
        $viewPath = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $viewName) . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            die("View [{$viewName}] tidak ditemukan di {$viewPath}");
        }

        extract($data);
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format number to Indonesian Rupiah currency
     */
    function format_rupiah(float|int $amount, bool $withPrefix = true): string
    {
        $formatted = number_format($amount, 0, ',', '.');
        return $withPrefix ? "Rp {$formatted}" : $formatted;
    }
}

if (!function_exists('json_response')) {
    /**
     * Send a JSON HTTP response
     */
    function json_response(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to another URL or route
     */
    function redirect(string $path): void
    {
        $target = str_starts_with($path, 'http') ? $path : base_url($path);
        header("Location: {$target}");
        exit;
    }
}

