<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\NotificationRepository;

class NotificationService
{
    private NotificationRepository $repo;

    public function __construct(?NotificationRepository $repo = null)
    {
        $this->repo = $repo ?? new NotificationRepository();
    }

    public function send(int $userId, string $title, string $message, ?string $actionUrl = null): int
    {
        return $this->repo->create($userId, $title, $message, $actionUrl);
    }

    public function getUserNotifications(int $userId, int $limit = 20): array
    {
        return $this->repo->getUserNotifications($userId, $limit);
    }

    public function getUnreadCount(int $userId): int
    {
        return $this->repo->getUnreadCount($userId);
    }

    public function markAsRead(int $notificationId, int $userId): bool
    {
        return $this->repo->markAsRead($notificationId, $userId);
    }

    public function markAllAsRead(int $userId): bool
    {
        return $this->repo->markAllAsRead($userId);
    }

    public function notifyRentalBookingCreated(int $bookingId, int $partnerUserId, int $customerId, string $productName): void
    {
        $code = '#RENT-' . str_pad((string)$bookingId, 5, '0', STR_PAD_LEFT);
        
        // Notifikasi untuk mitra pemilik toko
        $this->repo->create(
            $partnerUserId,
            'Pesanan Sewa Baru Masuk',
            "Pesanan {$code} untuk unit '{$productName}' telah dibayar dan siap disiapkan.",
            '/dashboard/mitra/jadwal'
        );

        // Notifikasi untuk penyewa
        $this->repo->create(
            $customerId,
            'Pemesanan Sewa Berhasil',
            "Pemesanan {$code} untuk '{$productName}' berhasil diverifikasi dan dana diamankan di Rekening Bersama Escrow.",
            '/pesanan-saya'
        );
    }

    public function notifyKycStatus(int $userId, string $status, ?string $reason = null): void
    {
        if ($status === 'approved') {
            $this->repo->create(
                $userId,
                'Verifikasi KYC Disetujui',
                'Selamat! Dokumen KTP Anda telah diverifikasi oleh Administrator. Anda kini dapat menyewa seluruh kamera di CamHub.',
                '/kyc/status'
            );
        } else {
            $note = $reason ? ": {$reason}" : '.';
            $this->repo->create(
                $userId,
                'Verifikasi KYC Ditolak',
                "Pengajuan dokumen identitas Anda belum disetujui{$note} Silakan unggah ulang dokumen yang jelas.",
                '/kyc/status'
            );
        }
    }

    public function notifyEscrowStatus(int $userId, string $status, string $itemTitle): void
    {
        if ($status === 'released') {
            $this->repo->create(
                $userId,
                'Dana Rekber Berhasil Dicairkan',
                "Pelanggan telah mengonfirmasi penyelesaian pesanan '{$itemTitle}'. Saldo telah ditambahkan ke dompet lapak Anda.",
                '/dashboard/mitra/saldo'
            );
        } elseif ($status === 'disputed') {
            $this->repo->create(
                $userId,
                'Sengketa Transaksi Diajukan',
                "Terdapat laporan sengketa untuk pesanan '{$itemTitle}'. Dana escrow sementara dibekukan untuk investigasi admin.",
                '/dashboard/mitra/transaksi'
            );
        } elseif ($status === 'refunded') {
            $this->repo->create(
                $userId,
                'Pengembalian Dana (Refund) Berhasil',
                "Dana pesanan '{$itemTitle}' telah dikembalikan penuh ke akun rekening Anda.",
                '/pesanan-saya'
            );
        }
    }

    public function notifyConsignmentInspected(int $customerId, string $itemName, float $agreedPrice): void
    {
        $priceFmt = 'Rp ' . number_format($agreedPrice, 0, ',', '.');
        $this->repo->create(
            $customerId,
            'Inspeksi Titip Jual Selesai',
            "Kamera titipan Anda '{$itemName}' telah diinspeksi mitra dengan taksiran harga kesepakatan {$priceFmt}.",
            '/titip-jual/saya'
        );
    }

    public function notifyWithdrawalProcessed(int $partnerUserId, float $amount, string $status): void
    {
        $amountFmt = 'Rp ' . number_format($amount, 0, ',', '.');
        if ($status === 'processed') {
            $this->repo->create(
                $partnerUserId,
                'Pencairan Saldo Berhasil',
                "Permohonan transfer dana sebesar {$amountFmt} telah diproses dan dikirim ke rekening bank Anda.",
                '/dashboard/mitra/saldo'
            );
        } else {
            $this->repo->create(
                $partnerUserId,
                'Pencairan Saldo Ditolak',
                "Permohonan transfer dana sebesar {$amountFmt} ditolak oleh Administrator. Saldo dikembalikan ke dompet toko.",
                '/dashboard/mitra/saldo'
            );
        }
    }
}
