<?php

declare(strict_types=1);

namespace App\Models;

class Consignment
{
    public int $id;
    public int $user_id;
    public int $store_id;
    public string $item_name;
    public string $item_description;
    public float $requested_price;
    public ?float $agreed_price;
    public float $commission_percent;
    public string $status;
    public ?string $approved_at;
    public string $created_at;

    public ?string $store_name = null;
    public ?string $user_name = null;

    public function __construct(array $data = [])
    {
        $this->id = (int) ($data['id'] ?? 0);
        $this->user_id = (int) ($data['user_id'] ?? 0);
        $this->store_id = (int) ($data['store_id'] ?? 0);
        $this->item_name = (string) ($data['item_name'] ?? '');
        $this->item_description = (string) ($data['item_description'] ?? '');
        $this->requested_price = (float) ($data['requested_price'] ?? 0.0);
        $this->agreed_price = isset($data['agreed_price']) ? (float) $data['agreed_price'] : null;
        $this->commission_percent = (float) ($data['commission_percent'] ?? 10.0);
        $this->status = (string) ($data['status'] ?? 'submitted');
        $this->approved_at = $data['approved_at'] ?? null;
        $this->created_at = (string) ($data['created_at'] ?? date('Y-m-d H:i:s'));

        $this->store_name = $data['store_name'] ?? null;
        $this->user_name = $data['user_name'] ?? null;
    }

    public function getFormattedRequestedPrice(): string
    {
        return 'Rp ' . number_format($this->requested_price, 0, ',', '.');
    }

    public function getFormattedAgreedPrice(): string
    {
        if ($this->agreed_price === null) {
            return 'Menunggu Kesepakatan';
        }
        return 'Rp ' . number_format($this->agreed_price, 0, ',', '.');
    }

    public function getEstimatedPayout(): float
    {
        $price = $this->agreed_price ?? $this->requested_price;
        $cut = $price * ($this->commission_percent / 100);
        return max(0, $price - $cut);
    }

    public function getFormattedEstimatedPayout(): string
    {
        return 'Rp ' . number_format($this->getEstimatedPayout(), 0, ',', '.');
    }

    public function getStatusBadge(): array
    {
        return match ($this->status) {
            'submitted' => [
                'label' => 'Menunggu Review Mitra',
                'bg' => '#fef3c7',
                'color' => '#d97706',
            ],
            'inspected' => [
                'label' => 'Dalam Inspeksi Fisik',
                'bg' => '#e0e7ff',
                'color' => '#4338ca',
            ],
            'approved' => [
                'label' => 'Harga Disetujui',
                'bg' => '#dcfce7',
                'color' => '#15803d',
            ],
            'listed' => [
                'label' => 'Tayang di Katalog',
                'bg' => '#dbeafe',
                'color' => '#1d4ed8',
            ],
            'sold' => [
                'label' => 'Terjual & Sukses',
                'bg' => '#d1fae5',
                'color' => '#065f46',
            ],
            'rejected' => [
                'label' => 'Pengajuan Ditolak',
                'bg' => '#fee2e2',
                'color' => '#b91c1c',
            ],
            default => [
                'label' => ucfirst($this->status),
                'bg' => '#f1f5f9',
                'color' => '#475569',
            ],
        };
    }
}
