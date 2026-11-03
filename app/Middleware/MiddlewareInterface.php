<?php

declare(strict_types=1);

namespace App\Middleware;

interface MiddlewareInterface
{
    /**
     * Handle an incoming request
     * 
     * @param string|null $parameter Optional parameter (e.g. role name 'admin', 'customer')
     * @return bool True if request can continue, False or redirect if halted
     */
    public function handle(?string $parameter = null): bool;
}
