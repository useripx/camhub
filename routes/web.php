<?php

use App\Core\Router;
use App\Controllers\AuthController;

/**
 * Web Routes - CamHub
 */

// 1. Halaman Utama / Fondasi Perdana
Router::get('/', function () {
    return view('welcome', [
        'title' => 'CamHub — Platform Marketplace Kamera Terpercaya',
        'appName' => 'CamHub',
        'status' => 'Fondasi Siap',
    ]);
});

// 2. Endpoint Health Check API
Router::get('/api/health', function () {
    json_response([
        'status' => 'OK',
        'app' => 'CamHub',
        'environment' => env('APP_ENV', 'local'),
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0.0',
    ]);
});

// 3. Autentikasi Pengguna (Tamu / Guest Only)
Router::get('/login', [AuthController::class, 'showLoginForm'], ['guest']);
Router::post('/login', [AuthController::class, 'login'], ['guest']);
Router::get('/register', [AuthController::class, 'showRegisterForm'], ['guest']);
Router::post('/register', [AuthController::class, 'register'], ['guest']);

// 4. Logout Pengguna (Wajib Terautentikasi / Auth)
Router::post('/logout', [AuthController::class, 'logout'], ['auth']);
Router::get('/logout', [AuthController::class, 'logout'], ['auth']);

// 5. Dashboard Peran Terproteksi (RBAC Route Guard)
Router::get('/dashboard/pelanggan', function () {
    return view('customer.dashboard');
}, ['auth', 'role:customer']);

Router::get('/dashboard/mitra', function () {
    return view('partner.dashboard');
}, ['auth', 'role:partner']);

Router::get('/dashboard/admin', function () {
    return view('admin.dashboard');
}, ['auth', 'role:admin']);
