<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\KycService;
use App\Validators\KycValidator;

class KycController
{
    private KycService $kycService;
    private KycValidator $validator;

    public function __construct(?KycService $kycService = null, ?KycValidator $validator = null)
    {
        $this->kycService = $kycService ?? new KycService();
        $this->validator = $validator ?? new KycValidator();
    }

    /**
     * Display KYC Submission Form (Multi-Step Mockup)
     */
    public function showKycForm(): string
    {
        $user = auth();
        if (!$user) {
            redirect('/login');
        }

        $latestKyc = $this->kycService->getKycStatus((int)$user['id']);

        // If user already has pending or approved submission, redirect to status page
        if ($latestKyc && in_array($latestKyc['status'], ['pending', 'approved'], true)) {
            redirect('/kyc/status');
        }

        return view('customer.kyc', [
            'pageTitle' => 'Verifikasi Identitas Resmi (KYC) — CamHub',
            'user' => $user,
            'latestKyc' => $latestKyc,
            'errors' => flash('errors') ?? [],
            'error' => flash('error'),
            'success' => flash('success'),
        ]);
    }

    /**
     * Handle KYC submission
     */
    public function submit(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Token keamanan CSRF tidak valid atau sesi berakhir. Silakan ulangi.');
            redirect('/kyc');
            return;
        }

        $user = auth();
        if (!$user) {
            redirect('/login');
            return;
        }

        $errors = $this->validator->validateSubmission($_POST, $_FILES);
        if (!empty($errors)) {
            flash('errors', $errors);
            flash_input($_POST);
            redirect('/kyc');
            return;
        }

        $result = $this->kycService->submitKyc((int)$user['id'], $_POST, $_FILES);

        if (!$result['success']) {
            flash('error', $result['message']);
            flash_input($_POST);
            redirect('/kyc');
            return;
        }

        clear_old_input();
        flash('success', $result['message']);
        redirect('/kyc/status');
    }

    /**
     * Display KYC Status & Submission Details
     */
    public function showKycStatus(): string
    {
        $user = auth();
        if (!$user) {
            redirect('/login');
        }

        $latestKyc = $this->kycService->getKycStatus((int)$user['id']);
        $history = $this->kycService->getSubmissionHistory((int)$user['id']);

        return view('customer.kyc_status', [
            'pageTitle' => 'Status Verifikasi KYC — CamHub',
            'user' => $user,
            'kyc' => $latestKyc,
            'history' => $history,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }
}

