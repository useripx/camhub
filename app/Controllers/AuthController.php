<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Validators\AuthValidator;

class AuthController
{
    private AuthService $authService;
    private AuthValidator $validator;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->validator = new AuthValidator();
    }

    /**
     * Display the Login form
     */
    public function showLoginForm(): string
    {
        return view('auth.login', [
            'pageTitle' => 'Masuk Akun — CamHub',
            'errors' => flash('errors') ?? [],
            'error' => flash('error'),
            'success' => flash('success'),
        ]);
    }

    /**
     * Handle Login submission
     */
    public function login(): void
    {
        // 0. Rate Limiting Protection (Anti-Brute Force)
        $rateCheck = rate_limit_check('auth_login', 5, 60);
        if (!$rateCheck['allowed']) {
            flash('error', "Terlalu banyak percobaan masuk yang gagal. Silakan coba kembali dalam {$rateCheck['retry_after']} detik demi keamanan akun Anda.");
            redirect('/login');
            return;
        }

        // 1. Verify CSRF Token
        if (!verify_csrf()) {
            flash('error', 'Sesi formulir kedaluwarsa atau token keamanan tidak valid. Silakan coba lagi.');
            redirect('/login');
            return;
        }

        // 2. Validate Input
        $errors = $this->validator->validateLogin($_POST);
        if (!empty($errors)) {
            flash('errors', $errors);
            flash_input($_POST);
            redirect('/login');
            return;
        }

        $identifier = trim($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';

        // 3. Attempt Authentication
        $result = $this->authService->attempt($identifier, $password);

        if (!$result['success']) {
            rate_limit_increment('auth_login', 5, 60);
            flash('error', $result['message']);
            flash_input($_POST);
            redirect('/login');
            return;
        }

        rate_limit_clear('auth_login');
        clear_old_input();
        flash('success', "Selamat datang kembali, {$result['user']['full_name']}!");
        redirect($result['redirect']);
    }

    /**
     * Display the Registration form
     */
    public function showRegisterForm(): string
    {
        return view('auth.register', [
            'pageTitle' => 'Daftar Akun Baru — CamHub',
            'errors' => flash('errors') ?? [],
            'error' => flash('error'),
            'success' => flash('success'),
        ]);
    }

    /**
     * Handle Registration submission
     */
    public function register(): void
    {
        // 0. Rate Limiting Protection
        $rateCheck = rate_limit_check('auth_register', 10, 60);
        if (!$rateCheck['allowed']) {
            flash('error', "Terlalu banyak permintaan pendaftaran. Silakan tunggu {$rateCheck['retry_after']} detik sebelum mencoba kembali.");
            redirect('/register');
            return;
        }

        // 1. Verify CSRF Token
        if (!verify_csrf()) {
            flash('error', 'Sesi formulir kedaluwarsa atau token keamanan tidak valid. Silakan coba lagi.');
            redirect('/register');
            return;
        }

        // 2. Validate Input
        $errors = $this->validator->validateRegister($_POST);
        if (!empty($errors)) {
            flash('errors', $errors);
            flash_input($_POST);
            redirect('/register');
            return;
        }

        // 3. Create Account
        $result = $this->authService->register($_POST);

        if (!$result['success']) {
            rate_limit_increment('auth_register', 10, 60);
            flash('error', $result['message']);
            flash_input($_POST);
            redirect('/register');
            return;
        }

        rate_limit_clear('auth_register');
        clear_old_input();
        flash('success', 'Pendaftaran akun berhasil! Silakan masuk dengan kredensial baru Anda.');
        redirect('/login');
    }

    /**
     * Handle Logout
     */
    public function logout(): void
    {
        $this->authService->logout();
        flash('success', 'Anda telah berhasil keluar dari akun.');
        redirect('/login');
    }
}
