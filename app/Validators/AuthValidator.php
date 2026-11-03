<?php

declare(strict_types=1);

namespace App\Validators;

use App\Repositories\UserRepository;

class AuthValidator
{
    private UserRepository $userRepo;

    public function __construct(?UserRepository $userRepo = null)
    {
        $this->userRepo = $userRepo ?? new UserRepository();
    }

    /**
     * Validate login input
     */
    public function validateLogin(array $input): array
    {
        $errors = [];

        $identifier = trim($input['identifier'] ?? '');
        $password = $input['password'] ?? '';

        if ($identifier === '') {
            $errors['identifier'] = 'Username, Email, atau No. HP wajib diisi.';
        }

        if ($password === '') {
            $errors['password'] = 'Password wajib diisi.';
        }

        return $errors;
    }

    /**
     * Validate registration input
     */
    public function validateRegister(array $input): array
    {
        $errors = [];

        // 1. Full Name
        $fullName = trim($input['full_name'] ?? '');
        if ($fullName === '') {
            $errors['full_name'] = 'Nama lengkap wajib diisi.';
        } elseif (mb_strlen($fullName) < 3) {
            $errors['full_name'] = 'Nama lengkap minimal 3 karakter.';
        }

        // 2. Username (optional or required)
        $username = trim($input['username'] ?? '');
        if ($username !== '') {
            if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
                $errors['username'] = 'Username hanya boleh berisi huruf, angka, dan garis bawah (3-30 karakter).';
            } elseif ($this->userRepo->findByUsername($username)) {
                $errors['username'] = 'Username ini sudah digunakan, silakan pilih yang lain.';
            }
        }

        // 3. Email
        $email = trim($input['email'] ?? '');
        if ($email === '') {
            $errors['email'] = 'Alamat email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format alamat email tidak valid.';
        } elseif ($this->userRepo->findByEmail($email)) {
            $errors['email'] = 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk.';
        }

        // 4. Phone
        $phone = trim($input['phone'] ?? '');
        if ($phone === '') {
            $errors['phone'] = 'Nomor HP/WhatsApp wajib diisi.';
        } elseif (!preg_match('/^(\+62|62|0)[0-9]{8,14}$/', $phone)) {
            $errors['phone'] = 'Format nomor HP tidak valid (contoh: 08123456789).';
        } elseif ($this->userRepo->findByPhone($phone)) {
            $errors['phone'] = 'Nomor HP ini sudah terdaftar pada akun lain.';
        }

        // 5. Password
        $password = $input['password'] ?? '';
        if ($password === '') {
            $errors['password'] = 'Password wajib diisi.';
        } elseif (strlen($password) < 4) {
            $errors['password'] = 'Password minimal terdiri dari 4 karakter.';
        }

        // 6. Password Confirmation
        $passwordConfirmation = $input['password_confirmation'] ?? '';
        if ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'Konfirmasi password tidak cocok.';
        }

        // 7. Role Selection
        $role = strtolower($input['role'] ?? 'customer');
        if (!in_array($role, ['customer', 'partner'], true)) {
            $errors['role'] = 'Pilihan peran tidak valid.';
        }

        return $errors;
    }
}

