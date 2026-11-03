<?php

declare(strict_types=1);

namespace App\Models;

class KycSubmission
{
    public int $id;
    public int $user_id;
    public string $full_name;
    public string $nik_ktp;
    public string $birth_date;
    public string $gender;
    public string $ktp_image_path;
    public string $selfie_image_path;
    public string $status; // 'pending', 'approved', 'rejected'
    public ?string $rejection_reason = null;
    public ?int $verified_by = null;
    public ?string $verified_at = null;
    public string $created_at;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getStatusBadge(): array
    {
        return match ($this->status) {
            'approved' => ['label' => 'Terverifikasi (KYC Approved)', 'class' => 'badge-success', 'color' => '#10b981'],
            'rejected' => ['label' => 'Ditolak (Perlu Perbaikan)', 'class' => 'badge-danger', 'color' => '#ef4444'],
            default => ['label' => 'Menunggu Verifikasi (Pending)', 'class' => 'badge-warning', 'color' => '#f59e0b'],
        };
    }
}
