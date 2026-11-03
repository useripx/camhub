<?php

namespace App\Models;

/**
 * Model Partner (Mitra Lapak)
 * 
 * Merepresentasikan data entitas mitra bisnis penyedia kamera.
 */
class Partner
{
    public ?int $id = null;
    public int $userId;
    public string $businessName;
    public string $status;
    public float $commissionRate;
    public ?string $verifiedAt = null;
    public ?string $createdAt = null;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->userId = (int)($data['user_id'] ?? 0);
        $this->businessName = (string)($data['business_name'] ?? '');
        $this->status = (string)($data['status'] ?? 'active');
        $this->commissionRate = isset($data['commission_rate']) ? (float)$data['commission_rate'] : 10.0;
        $this->verifiedAt = $data['verified_at'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isVerified(): bool
    {
        return !empty($this->verifiedAt);
    }
}
