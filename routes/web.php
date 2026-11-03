<?php

use App\Core\Router;

/**
 * Web Routes - CamHub
 */

// Halaman Utama / Fondasi Perdana
Router::get('/', function () {
    return view('welcome', [
        'title' => 'CamHub — Platform Marketplace Kamera Terpercaya',
        'appName' => 'CamHub',
        'status' => 'Fondasi Siap',
    ]);
});

// Endpoint Health Check API
Router::get('/api/health', function () {
    json_response([
        'status' => 'OK',
        'app' => 'CamHub',
        'environment' => env('APP_ENV', 'local'),
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0.0',
    ]);
});

