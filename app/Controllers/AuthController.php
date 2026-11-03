<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Validators\AuthValidator;

class AuthController
{
    private AuthService $authService;
    private AuthValidator $validator;

    public function __construct(?AuthService $authService = null, ?AuthValidator $validator = null)
    {
        $this->authService = $authService ?? new AuthService();
        $this->validator = $validator ?? new AuthValidator();
    }

    /**
     * Display the Login form
     */
    public function showLoginForm(): string
    {
        return view('auth.login', [
            'pageTitle' => 'Masuk ke CamHub — Platform Marketplace Kamera',
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
            flash('error', $result['message']);
            flash_input($_POST);
            redirect('/login');
            return;
        }

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
            flash('error', $result['message']);
            flash_input($_POST);
            redirect('/register');
            return;
        }

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
