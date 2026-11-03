<?php

declare(strict_types=1);

namespace App\Middleware;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Ensure user is authenticated
     */
    public function handle(?string $parameter = null): bool
    {
        if (!auth_check()) {
            flash('error', 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.');
            
            // If AJAX / JSON request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                json_response(['status' => 'error', 'message' => 'Unauthorized. Please login.'], 401);
            }

            redirect('/login');
            return false;
        }

        return true;
    }
}
