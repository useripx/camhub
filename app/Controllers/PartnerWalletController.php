<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\EscrowService;
use App\Services\ReportService;
use App\Repositories\PartnerRepository;

class PartnerWalletController
{
    private EscrowService $escrowService;
    private PartnerRepository $partnerRepo;
    private ReportService $reportService;

    public function __construct()
    {
        $this->escrowService = new EscrowService();
        $this->partnerRepo = new PartnerRepository();
        $this->reportService = new ReportService();
    }

    /**
     * Tampilkan antarmuka Saldo & Pencairan Dana Mitra (100% Mockup CamHub — Dashboard Mitra Lapak.png)
     */
    public function index(): string
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);

        if (!$partner) {
            redirect(base_url('/dashboard/mitra'));
            return '';
        }

        $walletData = $this->escrowService->getPartnerWalletData((int) $partner['partner_id']);

        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return view('partner/wallet', [
            'partner' => $partner,
            'metrics' => $walletData['metrics'],
            'transactions' => $walletData['transactions'],
            'withdrawals' => $walletData['withdrawals'],
            'success' => $success,
            'error' => $error,
            'activeMenu' => 'saldo'
        ]);
    }

    /**
     * Memproses pengajuan penarikan dana ke rekening bank mitra
     */
    public function withdraw(): void
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);

        if (!$partner) {
            redirect(base_url('/dashboard/mitra'));
            return;
        }

        $amount = (float) ($_POST['amount'] ?? 0);
        $res = $this->escrowService->requestWithdrawal((int) $partner['partner_id'], $amount, $_POST);

        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }

        redirect(base_url('/dashboard/mitra/saldo'));
    }

    /**
     * Ekspor mutasi saldo dan riwayat transaksi toko mitra ke format CSV
     */
    public function export(): void
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);

        if (!$partner) {
            redirect(base_url('/dashboard/mitra'));
            return;
        }

        $csvData = $this->reportService->generateCsvPartnerReport((int) $partner['partner_id']);
        $storeNameSanitized = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string)$partner['store_name']);
        $filename = 'Rekap_Saldo_' . $storeNameSanitized . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $csvData;
        exit;
    }
}
