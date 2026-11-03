<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\CustomerProfileController;
use App\Controllers\KycController;
use App\Controllers\HomeController;
use App\Controllers\CatalogController;
use App\Controllers\ProductDetailController;

/**
 * Web Routes - CamHub
 */

// 1. Halaman Beranda Publik Dinamis (Tahap 05: Mockup Beranda.png)
Router::get('/', [HomeController::class, 'index']);

// 2. Katalog Produk & Filter Multi-Kriteria (Tahap 05: Mockup Katalog Produk.png)
Router::get('/katalog', [CatalogController::class, 'index']);

// 3. Detail Produk & Kalkulator Sewa Interaktif (Tahap 05: Mockup Detail Produk.png)
Router::get('/produk/{slug}', [ProductDetailController::class, 'show']);
Router::post('/api/rental/calculate', [ProductDetailController::class, 'calculateEstimate']);

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

// 6. Profil Pelanggan & Verifikasi KYC (Khusus Customer)
Router::get('/profil', [CustomerProfileController::class, 'showProfile'], ['auth', 'role:customer']);
Router::post('/profil/edit', [CustomerProfileController::class, 'updateProfile'], ['auth', 'role:customer']);

Router::get('/kyc', [KycController::class, 'showKycForm'], ['auth', 'role:customer']);
Router::post('/kyc/submit', [KycController::class, 'submit'], ['auth', 'role:customer']);
Router::get('/kyc/status', [KycController::class, 'showKycStatus'], ['auth', 'role:customer']);
