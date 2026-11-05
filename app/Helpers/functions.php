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


if (!function_exists('rate_limit_check')) {
    /**
     * Check if request exceeds rate limit
     * 
     * @param string $action Action name (e.g., 'login', 'register')
     * @param int $maxAttempts Maximum allowed attempts within decay window
     * @param int $decaySeconds Cooldown period in seconds
     * @return array ['allowed' => bool, 'remaining' => int, 'retry_after' => int]
     */
    function rate_limit_check(string $action, int $maxAttempts = 5, int $decaySeconds = 60): array
    {
        session_start_safe();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = 'rate_limit_' . md5($action . '_' . $ip);

        $now = time();
        $record = $_SESSION[$key] ?? ['attempts' => 0, 'first_attempt' => $now, 'locked_until' => 0];

        // If currently locked
        if ($record['locked_until'] > $now) {
            return [
                'allowed' => false,
                'remaining' => 0,
                'retry_after' => $record['locked_until'] - $now
            ];
        }

        // If decay window expired, reset attempts
        if ($now - $record['first_attempt'] > $decaySeconds) {
            $record['attempts'] = 0;
            $record['first_attempt'] = $now;
            $record['locked_until'] = 0;
            $_SESSION[$key] = $record;
        }

        $remaining = max(0, $maxAttempts - $record['attempts']);
        return [
            'allowed' => $remaining > 0,
            'remaining' => $remaining,
            'retry_after' => 0
        ];
    }
}

if (!function_exists('rate_limit_increment')) {
    /**
     * Increment failed attempt count for rate limit
     */
    function rate_limit_increment(string $action, int $maxAttempts = 5, int $decaySeconds = 60): void
    {
        session_start_safe();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = 'rate_limit_' . md5($action . '_' . $ip);

        $now = time();
        $record = $_SESSION[$key] ?? ['attempts' => 0, 'first_attempt' => $now, 'locked_until' => 0];

        if ($now - $record['first_attempt'] > $decaySeconds) {
            $record['attempts'] = 1;
            $record['first_attempt'] = $now;
        } else {
            $record['attempts']++;
        }

        if ($record['attempts'] >= $maxAttempts) {
            $record['locked_until'] = $now + $decaySeconds;
        }

        $_SESSION[$key] = $record;
    }
}

if (!function_exists('rate_limit_clear')) {
    /**
     * Clear rate limit on successful action
     */
    function rate_limit_clear(string $action): void
    {
        session_start_safe();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = 'rate_limit_' . md5($action . '_' . $ip);
        unset($_SESSION[$key]);
    }
}

if (!function_exists('format_audit_changes')) {
    /**
     * Format payload changes_json audit log menjadi badge human-readable yang rapi
     */
    function format_audit_changes(?string $json, ?string $action = null): string
    {
        if (empty($json) || $json === '-') {
            return '<span style="color:var(--text-muted);">-</span>';
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            return '<span class="audit-chip">' . htmlspecialchars((string)$json) . '</span>';
        }

        $chips = [];
        $action = strtoupper((string)$action);

        if (str_starts_with($action, 'DISPUTE_FORCE_')) {
            $res = $data['resolution'] ?? ($action === 'DISPUTE_FORCE_RELEASE' ? 'release' : 'refund');
            $resLabel = $res === 'release' ? 'Release ke Mitra' : 'Refund ke Pelanggan';
            $resColor = $res === 'release' ? 'style="background:rgba(16,185,129,0.15); border-color:rgba(16,185,129,0.3); color:#10b981;"' : 'style="background:rgba(239,68,68,0.15); border-color:rgba(239,68,68,0.3); color:#ef4444;"';
            $chips[] = "<span class=\"audit-chip\" {$resColor}><strong>Resolusi:</strong> {$resLabel}</span>";
            if (!empty($data['amount'])) {
                $chips[] = '<span class="audit-chip"><strong>Nominal:</strong> Rp ' . number_format((float)$data['amount'], 0, ',', '.') . '</span>';
            }
            if (!empty($data['notes'])) {
                $chips[] = '<span class="audit-chip"><strong>Catatan:</strong> ' . htmlspecialchars((string)$data['notes']) . '</span>';
            }
        } elseif ($action === 'RENTAL_BOOKING_CREATE') {
            if (!empty($data['booking_code'])) {
                $chips[] = '<span class="audit-chip"><strong>Kode:</strong> ' . htmlspecialchars((string)$data['booking_code']) . '</span>';
            }
            if (!empty($data['product_unit_id'])) {
                $chips[] = '<span class="audit-chip"><strong>Unit:</strong> #' . (int)$data['product_unit_id'] . '</span>';
            }
            if (!empty($data['start_date']) && !empty($data['end_date'])) {
                $chips[] = '<span class="audit-chip"><strong>Sewa:</strong> ' . date('d/m/Y', strtotime((string)$data['start_date'])) . ' - ' . date('d/m/Y', strtotime((string)$data['end_date'])) . '</span>';
            }
            if (!empty($data['total_amount'])) {
                $chips[] = '<span class="audit-chip"><strong>Total:</strong> Rp ' . number_format((float)$data['total_amount'], 0, ',', '.') . '</span>';
            }
        } elseif ($action === 'SALES_ORDER_CREATE') {
            if (!empty($data['order_number'])) {
                $chips[] = '<span class="audit-chip"><strong>No. Order:</strong> ' . htmlspecialchars((string)$data['order_number']) . '</span>';
            }
            if (!empty($data['total_amount'])) {
                $chips[] = '<span class="audit-chip"><strong>Total:</strong> Rp ' . number_format((float)$data['total_amount'], 0, ',', '.') . '</span>';
            }
            if (!empty($data['payment_method'])) {
                $chips[] = '<span class="audit-chip"><strong>Metode:</strong> Rekber Escrow</span>';
            }
        } elseif ($action === 'LOGIN_SUCCESS') {
            $roleMap = ['admin' => 'Super Admin', 'partner' => 'Mitra Lapak', 'customer' => 'Pelanggan'];
            $roleName = $roleMap[$data['role'] ?? ''] ?? ucfirst((string)($data['role'] ?? 'Pengguna'));
            $chips[] = '<span class="audit-chip"><strong>Peran:</strong> ' . htmlspecialchars($roleName) . '</span>';
            if (!empty($data['user_agent'])) {
                $ua = (string)$data['user_agent'];
                $client = str_contains($ua, 'Browser') || str_contains($ua, 'Mozilla') ? 'Browser Web' : (str_contains($ua, 'Automated') ? 'Automated Test' : $ua);
                $chips[] = '<span class="audit-chip"><strong>Klien:</strong> ' . htmlspecialchars($client) . '</span>';
            }
        } elseif ($action === 'LOGIN_FAILED') {
            $chips[] = '<span class="audit-chip" style="background:rgba(239,68,68,0.15); border-color:rgba(239,68,68,0.3); color:#ef4444;"><strong>Gagal:</strong> ' . htmlspecialchars((string)($data['reason'] ?? 'Autentikasi Ditolak')) . '</span>';
        } elseif ($action === 'USER_STATUS_CHANGE') {
            if (!empty($data['username'])) {
                $chips[] = '<span class="audit-chip"><strong>Akun:</strong> @' . htmlspecialchars((string)$data['username']) . '</span>';
            }
            $old = ucfirst((string)($data['old_status'] ?? '-'));
            $new = ucfirst((string)($data['new_status'] ?? '-'));
            $chips[] = "<span class=\"audit-chip\"><strong>Status:</strong> {$old} &rarr; {$new}</span>";
        } elseif ($action === 'FINE_CREATE') {
            $typeMap = ['late_return' => 'Terlambat', 'damage' => 'Kerusakan', 'missing_accessory' => 'Aksesoris Hilang'];
            $typeName = $typeMap[$data['fine_type'] ?? ''] ?? ucfirst(str_replace('_', ' ', (string)($data['fine_type'] ?? 'Denda')));
            $chips[] = '<span class="audit-chip"><strong>Jenis:</strong> ' . htmlspecialchars($typeName) . '</span>';
            if (!empty($data['amount'])) {
                $chips[] = '<span class="audit-chip"><strong>Nominal:</strong> Rp ' . number_format((float)$data['amount'], 0, ',', '.') . '</span>';
            }
            if (!empty($data['reason'])) {
                $chips[] = '<span class="audit-chip"><strong>Alasan:</strong> ' . htmlspecialchars((string)$data['reason']) . '</span>';
            }
        } elseif ($action === 'FINE_PAID') {
            $chips[] = '<span class="audit-chip" style="background:rgba(16,185,129,0.15); border-color:rgba(16,185,129,0.3); color:#10b981;"><strong>Status:</strong> Lunas (Paid)</span>';
        } elseif ($action === 'WITHDRAWAL_STATUS_CHANGE') {
            $old = ucfirst((string)($data['old_status'] ?? '-'));
            $new = ucfirst((string)($data['new_status'] ?? '-'));
            $chips[] = "<span class=\"audit-chip\"><strong>Status:</strong> {$old} &rarr; {$new}</span>";
            if (!empty($data['amount'])) {
                $chips[] = '<span class="audit-chip"><strong>Nominal:</strong> Rp ' . number_format((float)$data['amount'], 0, ',', '.') . '</span>';
            }
        } elseif ($action === 'KYC_APPROVE') {
            $chips[] = '<span class="audit-chip" style="background:rgba(16,185,129,0.15); border-color:rgba(16,185,129,0.3); color:#10b981;"><strong>Keputusan:</strong> Disetujui</span>';
            if (!empty($data['full_name'])) {
                $chips[] = '<span class="audit-chip"><strong>Nama KTP:</strong> ' . htmlspecialchars((string)$data['full_name']) . '</span>';
            }
            if (!empty($data['nik_ktp'])) {
                $chips[] = '<span class="audit-chip"><strong>NIK:</strong> ' . htmlspecialchars((string)$data['nik_ktp']) . '</span>';
            }
        } elseif ($action === 'KYC_REJECT') {
            $chips[] = '<span class="audit-chip" style="background:rgba(239,68,68,0.15); border-color:rgba(239,68,68,0.3); color:#ef4444;"><strong>Keputusan:</strong> Ditolak</span>';
            if (!empty($data['rejection_reason'])) {
                $chips[] = '<span class="audit-chip"><strong>Alasan:</strong> ' . htmlspecialchars((string)$data['rejection_reason']) . '</span>';
            }
        } elseif ($action === 'KYC_SUBMIT') {
            if (!empty($data['full_name'])) {
                $chips[] = '<span class="audit-chip"><strong>Pengaju:</strong> ' . htmlspecialchars((string)$data['full_name']) . '</span>';
            }
            if (!empty($data['nik_masked'])) {
                $chips[] = '<span class="audit-chip"><strong>NIK:</strong> ' . htmlspecialchars((string)$data['nik_masked']) . '</span>';
            }
        } elseif (str_starts_with($action, 'ESCROW_')) {
            if (!empty($data['action'])) {
                $chips[] = '<span class="audit-chip"><strong>Aksi:</strong> ' . htmlspecialchars(ucfirst((string)$data['action'])) . '</span>';
            }
            if (!empty($data['amount_released'])) {
                $chips[] = '<span class="audit-chip"><strong>Nominal:</strong> Rp ' . number_format((float)$data['amount_released'], 0, ',', '.') . '</span>';
            }
            if (!empty($data['amount_refunded'])) {
                $chips[] = '<span class="audit-chip"><strong>Nominal:</strong> Rp ' . number_format((float)$data['amount_refunded'], 0, ',', '.') . '</span>';
            }
            if (!empty($data['reason'])) {
                $chips[] = '<span class="audit-chip"><strong>Alasan:</strong> ' . htmlspecialchars((string)$data['reason']) . '</span>';
            }
        } else {
            foreach ($data as $k => $v) {
                if (is_array($v)) $v = json_encode($v);
                $label = ucwords(str_replace(['_', '-'], ' ', (string)$k));
                $chips[] = '<span class="audit-chip"><strong>' . htmlspecialchars($label) . ':</strong> ' . htmlspecialchars((string)$v) . '</span>';
            }
        }

        return !empty($chips) ? implode(' ', $chips) : '<span style="color:var(--text-muted);">-</span>';
    }
}
