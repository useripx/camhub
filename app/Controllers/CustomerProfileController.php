<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ProfileService;
use App\Validators\KycValidator;

class CustomerProfileController
{
    private ProfileService $profileService;
    private KycValidator $validator;

    public function __construct(?ProfileService $profileService = null, ?KycValidator $validator = null)
    {
        $this->profileService = $profileService ?? new ProfileService();
        $this->validator = $validator ?? new KycValidator();
    }

    /**
     * Display Customer Profile Page
     */
    public function showProfile(): string
    {
        $user = auth();
        if (!$user) {
            redirect('/login');
        }

        $profile = $this->profileService->getProfile((int)$user['id']);

        return view('customer.profile', [
            'pageTitle' => 'Profil Saya & Pengaturan Akun — CamHub',
            'profile' => $profile,
            'user' => $user,
            'errors' => flash('errors') ?? [],
            'error' => flash('error'),
            'success' => flash('success'),
        ]);
    }

    /**
     * Handle Customer Profile Update submission
     */
    public function updateProfile(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Sesi formulir tidak valid. Silakan coba lagi.');
            redirect('/profil');
            return;
        }

        $user = auth();
        if (!$user) {
            redirect('/login');
            return;
        }

        $errors = $this->validator->validateProfile($_POST);
        if (!empty($errors)) {
            flash('errors', $errors);
            flash_input($_POST);
            redirect('/profil');
            return;
        }

        $result = $this->profileService->updateProfile((int)$user['id'], $_POST);

        if (!$result['success']) {
            flash('error', $result['message']);
            flash_input($_POST);
            redirect('/profil');
            return;
        }

        clear_old_input();
        flash('success', $result['message']);
        redirect('/profil');
    }
}

