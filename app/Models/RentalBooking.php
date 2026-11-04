<?php

namespace App\Models;

/**
 * Model RentalBooking (Pemesanan Sewa Kamera)
 * 
 * Merepresentasikan transaksi penyewaan unit kamera oleh pelanggan.
 */
class RentalBooking
{
    public ?int $id = null;
    public int $userId;
    public int $productUnitId;
    public string $startDate;
    public string $endDate;
    public int $totalDays;
    public float $dailyPrice;
    public float $subtotal;
    public float $adminFee;
    public float $totalAmount;
    public string $paymentStatus;
    public string $rentalStatus;
    public ?string $createdAt = null;
    public ?string $updatedAt = null;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->userId = (int)($data['user_id'] ?? 0);
        $this->productUnitId = (int)($data['product_unit_id'] ?? 0);
        $this->startDate = (string)($data['start_date'] ?? '');
        $this->endDate = (string)($data['end_date'] ?? '');
        $this->totalDays = (int)($data['total_days'] ?? 1);
        $this->dailyPrice = (float)($data['daily_price'] ?? 0);
        $this->subtotal = (float)($data['subtotal'] ?? 0);
        $this->adminFee = (float)($data['admin_fee'] ?? 5000.0);
        $this->totalAmount = (float)($data['total_amount'] ?? 0);
        $this->paymentStatus = (string)($data['payment_status'] ?? 'unpaid');
        $this->rentalStatus = (string)($data['rental_status'] ?? 'pending');
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }

    public function getBookingCode(): string
    {
        return '#RENT-' . str_pad((string)($this->id ?? 0), 5, '0', STR_PAD_LEFT);
    }

    public function isPaid(): bool
    {
        return $this->paymentStatus === 'paid';
    }

    public function isActive(): bool
    {
        return $this->rentalStatus === 'active';
    }
}
