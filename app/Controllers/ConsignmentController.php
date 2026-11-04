<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ConsignmentService;
use App\Repositories\PartnerRepository;

class ConsignmentController
{
    private ConsignmentService $service;
    private PartnerRepository $partnerRepo;

    public function __construct()
    {
        $this->service = new ConsignmentService();
        $this->partnerRepo = new PartnerRepository();
    }

    /**
     * Menampilkan formulir pengajuan titip jual kamera bagi pelanggan
     */
    public function create(): string
    {
        $stores = $this->service->getActiveStores();
        $errors = $_SESSION['flash_errors'] ?? [];
        $old = $_SESSION['flash_old'] ?? [];
        unset($_SESSION['flash_errors'], $_SESSION['flash_old']);

        return view('customer/consignment_form', [
            'stores' => $stores,
            'errors' => $errors,
            'old' => $old,
            'user' => auth()
        ]);
    }

    /**
     * Memproses pengajuan formulir titip jual kamera
     */
    public function store(): void
    {
        $userId = auth()['id'] ?? 0;
        $result = $this->service->submitConsignment($_POST, $userId);

        if (!$result['success']) {
            $_SESSION['flash_errors'] = $result['errors'];
            $_SESSION['flash_old'] = $_POST;
            redirect(base_url('/titip-jual/ajukan'));
            return;
        }

        $_SESSION['flash_success'] = $result['message'];
        redirect(base_url('/titip-jual/saya'));
    }

    /**
     * Menampilkan daftar dan pelacak status titip jual pelanggan
     */
    public function customerConsignments(): string
    {
        $userId = auth()['id'] ?? 0;
        $consignments = $this->service->getUserConsignments($userId);
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        return view('customer/consignment_list', [
            'consignments' => $consignments,
            'success' => $success,
            'user' => auth()
        ]);
    }

    /**
     * Menampilkan daftar pengajuan titip jual masuk untuk toko mitra
     */
    public function partnerConsignments(): string
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);

        if (!$partner || empty($partner['store_id'])) {
            redirect(base_url('/dashboard/mitra'));
            return '';
        }

        $storeId = (int) $partner['store_id'];
        $consignments = $this->service->getStoreConsignments($storeId);
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        return view('partner/consignments', [
            'partner' => $partner,
            'consignments' => $consignments,
            'success' => $success
        ]);
    }

    /**
     * Mitra melakukan inspeksi & penetapan harga kesepakatan
     */
    public function partnerInspect(int $id): void
    {
        $agreedPrice = (float) ($_POST['agreed_price'] ?? 0);
        $commissionPercent = (float) ($_POST['commission_percent'] ?? 10.0);
        $action = $_POST['action'] ?? 'approve';

        if ($action === 'reject') {
            (new \App\Repositories\ConsignmentRepository())->markAsRejected($id);
            $_SESSION['flash_success'] = "Pengajuan titip jual #TJ-{$id} berhasil ditolak.";
        } else {
            if ($agreedPrice < 50000) {
                $agreedPrice = 100000;
            }
            $this->service->approveConsignment($id, $agreedPrice, $commissionPercent);
            $_SESSION['flash_success'] = "Hasil inspeksi dicatat dan harga kesepakatan Rp " . number_format($agreedPrice, 0, ',', '.') . " telah disetujui.";
        }

        redirect(base_url('/dashboard/mitra/titip-jual'));
    }

    /**
     * Mitra memublikasikan barang titip jual yang telah disetujui ke katalog publik
     */
    public function partnerApprove(int $id): void
    {
        try {
            $this->service->publishConsignment($id);
            $_SESSION['flash_success'] = "Unit titip jual #TJ-{$id} resmi diterbitkan ke katalog publik dengan badge 'Titip Jual'!";
        } catch (\Exception $e) {
            $_SESSION['flash_errors'] = ['publish' => $e->getMessage()];
        }

        redirect(base_url('/dashboard/mitra/titip-jual'));
    }
}

