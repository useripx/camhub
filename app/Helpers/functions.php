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

if (!function_exists('session_start_safe')) {
    /**
     * Start PHP session with secure cookie parameters if not active
     */
    function session_start_safe(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                session_start([
                    'cookie_httponly' => true,
                    'cookie_samesite' => 'Lax',
                    'cookie_secure' => (bool) env('SESSION_SECURE', false),
                ]);
            } else {
                @session_start();
            }
        }
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

/* =========================================================================
 * AUTH, SESSION, AND CSRF HELPERS
 * ========================================================================= */

if (!function_exists('auth')) {
    /**
     * Get currently authenticated user array or null
     */
    function auth(): ?array
    {
        session_start_safe();
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('auth_check')) {
    /**
     * Determine if current visitor is authenticated
     */
    function auth_check(): bool
    {
        session_start_safe();
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate or retrieve active CSRF token
     */
    function csrf_token(): string
    {
        session_start_safe();
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate HTML hidden input for CSRF protection
     */
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('verify_csrf')) {
    /**
     * Verify CSRF token from POST request
     */
    function verify_csrf(?string $token = null): bool
    {
        session_start_safe();
        $submittedToken = $token ?? $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $sessionToken = $_SESSION['_csrf_token'] ?? '';

        if (empty($submittedToken) || empty($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $submittedToken);
    }
}

if (!function_exists('flash')) {
    /**
     * Set or get a flash session message
     */
    function flash(string $key, mixed $value = null): mixed
    {
        session_start_safe();
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }

        if (isset($_SESSION['_flash'][$key])) {
            $msg = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $msg;
        }

        return null;
    }
}

if (!function_exists('has_flash')) {
    /**
     * Check if a flash key exists
     */
    function has_flash(string $key): bool
    {
        session_start_safe();
        return isset($_SESSION['_flash'][$key]);
    }
}

if (!function_exists('old')) {
    /**
     * Retrieve old input flashed from previous form submission
     */
    function old(string $key, mixed $default = ''): mixed
    {
        session_start_safe();
        return $_SESSION['_old_input'][$key] ?? $default;
    }
}

if (!function_exists('flash_input')) {
    /**
     * Flash current request inputs for next request
     */
    function flash_input(array $input): void
    {
        session_start_safe();
        // Do not preserve password in old input for security
        unset($input['password'], $input['password_confirmation'], $input['_token']);
        $_SESSION['_old_input'] = $input;
    }
}

if (!function_exists('clear_old_input')) {
    /**
     * Clear previously flashed input
     */
    function clear_old_input(): void
    {
        session_start_safe();
        unset($_SESSION['_old_input']);
    }
}
