<?php

declare(strict_types=1);

namespace App\Middleware;

class GuestMiddleware implements MiddlewareInterface
{
    /**
     * Ensure visitor is a guest (not authenticated)
     */
    public function handle(?string $parameter = null): bool
    {
        if (auth_check()) {
            $user = auth();
            $role = $user['role_name'] ?? 'customer';

            $target = match ($role) {
                'admin' => '/dashboard/admin',
                'partner' => '/dashboard/mitra',
                default => '/dashboard/pelanggan',
            };

            redirect($target);
            return false;
        }

        return true;
    }
}
