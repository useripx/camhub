<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\EscrowService;

class EscrowController
{
    private EscrowService $service;

    public function __construct()
    {
        $this->service = new EscrowService();
    }

    /**
     * Pelanggan konfirmasi selesai sewa kamera & melepaskan escrow
     */
    public function confirmRental(int $bookingId): void
    {
        $userId = auth()['id'] ?? 0;
        $res = $this->service->releaseRentalEscrow($bookingId, $userId);

        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }

        redirect(base_url('/pesanan-saya'));
    }

    /**
     * Pembeli konfirmasi barang diterima & melepaskan escrow
     */
    public function confirmSale(int $orderId): void
    {
        $userId = auth()['id'] ?? 0;
        $res = $this->service->releaseSalesEscrow($orderId, $userId);

        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }

        redirect(base_url('/jual-beli-saya'));
    }

    /**
     * Pelanggan mengajukan sengketa/dispute
     */
    public function dispute(): void
    {
        $userId = auth()['id'] ?? 0;
        $type = $_POST['type'] ?? 'rental';
        $refId = (int) ($_POST['reference_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Komplain kondisi fisik / kerusakan');

        $res = $this->service->requestDispute($type, $refId, $userId, $reason);

        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }

        $redirectUrl = ($type === 'sale') ? '/jual-beli-saya' : '/pesanan-saya';
        redirect(base_url($redirectUrl));
    }
}
