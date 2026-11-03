<?php

declare(strict_types=1);

namespace App\Middleware;

class RoleMiddleware implements MiddlewareInterface
{
    /**
     * Ensure authenticated user possesses the required role
     */
    public function handle(?string $parameter = null): bool
    {
        if (!auth_check()) {
            flash('error', 'Silakan masuk terlebih dahulu.');
            redirect('/login');
            return false;
        }

        $user = auth();
        $userRole = strtolower($user['role_name'] ?? '');
        $requiredRole = strtolower($parameter ?? '');

        if ($requiredRole !== '' && $userRole !== $requiredRole) {
            http_response_code(403);
            
            // If AJAX / JSON request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                json_response([
                    'status' => 'error',
                    'message' => "Akses ditolak. Halaman ini memerlukan hak akses [{$requiredRole}]."
                ], 403);
            }

            $userDashboard = match ($userRole) {
                'admin' => base_url('/dashboard/admin'),
                'partner' => base_url('/dashboard/mitra'),
                default => base_url('/dashboard/pelanggan'),
            };

            echo "<!DOCTYPE html><html lang='id'><head><meta charset='utf-8'><title>403 Terlarang - CamHub</title><style>body{font-family:system-ui,-apple-system,sans-serif;background:#0b192e;color:#fff;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}.card{background:#132644;padding:40px;border-radius:16px;max-width:480px;text-align:center;box-shadow:0 20px 40px rgba(0,0,0,0.4);border:1px solid #1e3a5f;}h1{color:#f59e0b;margin-top:0;}p{color:#94a3b8;line-height:1.6;}a{display:inline-block;margin-top:20px;background:#2563eb;color:#fff;text-decoration:none;padding:12px 24px;border-radius:8px;font-weight:600;}</style></head><body><div class='card'><h1>403 | Akses Dibatasi</h1><p>Maaf, peran akun Anda (<strong>" . htmlspecialchars($userRole, ENT_QUOTES, 'UTF-8') . "</strong>) tidak memiliki izin untuk mengakses area <strong>" . htmlspecialchars($requiredRole, ENT_QUOTES, 'UTF-8') . "</strong>.</p><a href='{$userDashboard}'>Kembali ke Dashboard Anda</a></div></body></html>";
            exit;
        }

        return true;
    }
}

