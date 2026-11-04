<?php

namespace App\Models;

/**
 * Model Payment (Pembayaran Transaksi)
 * 
 * Merepresentasikan pencatatan pembayaran sewa/beli melalui Escrow, Transfer Bank, atau E-Wallet.
 */
class Payment
{
    public ?int $id = null;
    public int $userId;
    public string $orderType;
    public int $orderId;
    public string $paymentMethod;
    public float $amount;
    public string $paymentStatus;
    public ?string $paidAt = null;
    public ?string $createdAt = null;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->userId = (int)($data['user_id'] ?? 0);
        $this->orderType = (string)($data['order_type'] ?? 'rental');
        $this->orderId = (int)($data['order_id'] ?? 0);
        $this->paymentMethod = (string)($data['payment_method'] ?? 'escrow');
        $this->amount = (float)($data['amount'] ?? 0);
        $this->paymentStatus = (string)($data['payment_status'] ?? 'pending');
        $this->paidAt = $data['paid_at'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
    }

    public function isSuccess(): bool
    {
        return $this->paymentStatus === 'success';
    }

    public function isEscrow(): bool
    {
        return $this->paymentMethod === 'escrow';
    }
}

