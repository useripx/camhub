<?php

namespace App\Models;

/**
 * Model ProductUnit (Unit Fisik Barang Kamera/Lensa)
 * 
 * Merepresentasikan unit fisik inventaris bernomor seri unik.
 */
class ProductUnit
{
    public ?int $id = null;
    public int $productId;
    public string $serialNumber;
    public string $conditionStatus;
    public string $availabilityStatus;
    public ?string $createdAt = null;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->productId = (int)($data['product_id'] ?? 0);
        $this->serialNumber = (string)($data['serial_number'] ?? '');
        $this->conditionStatus = (string)($data['condition_status'] ?? 'Sangat Baik');
        $this->availabilityStatus = (string)($data['availability_status'] ?? 'available');
        $this->createdAt = $data['created_at'] ?? null;
    }

    public function isAvailable(): bool
    {
        return $this->availabilityStatus === 'available';
    }

    public function isRented(): bool
    {
        return $this->availabilityStatus === 'rented';
    }

    public function isInMaintenance(): bool
    {
        return $this->availabilityStatus === 'maintenance';
    }

    public function getStatusBadge(): string
    {
        return match ($this->availabilityStatus) {
            'available' => '<span class="badge badge-success">Tersedia</span>',
            'rented' => '<span class="badge badge-warning">Sedang Disewa</span>',
            'maintenance' => '<span class="badge badge-danger">Maintenance</span>',
            default => '<span class="badge badge-secondary">' . htmlspecialchars($this->availabilityStatus) . '</span>',
        };
    }
}
