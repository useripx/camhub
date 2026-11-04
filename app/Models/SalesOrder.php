<?php

declare(strict_types=1);

namespace App\Models;

use ArrayAccess;

class SalesOrder implements ArrayAccess
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getId(): int
    {
        return (int)($this->data['id'] ?? 0);
    }

    public function getOrderNumber(): string
    {
        return (string)($this->data['order_number'] ?? '');
    }

    public function getSubtotal(): float
    {
        return (float)($this->data['subtotal'] ?? 0);
    }

    public function getAdminFee(): float
    {
        return (float)($this->data['admin_fee'] ?? 5000.00);
    }

    public function getTotalAmount(): float
    {
        return (float)($this->data['total_amount'] ?? 0);
    }

    public function getOrderStatus(): string
    {
        return (string)($this->data['order_status'] ?? 'pending');
    }

    public function getShippingAddress(): string
    {
        return (string)($this->data['shipping_address'] ?? '');
    }

    public function getFormattedTotal(): string
    {
        return 'Rp ' . number_format($this->getTotalAmount(), 0, ',', '.');
    }

    public function getFormattedSubtotal(): string
    {
        return 'Rp ' . number_format($this->getSubtotal(), 0, ',', '.');
    }

    public function getFormattedAdminFee(): string
    {
        return 'Rp ' . number_format($this->getAdminFee(), 0, ',', '.');
    }

    public function getStatusBadge(): string
    {
        $map = [
            'pending' => '<span class="badge badge-warning">Menunggu Pembayaran</span>',
            'paid' => '<span class="badge badge-info">Dibayar</span>',
            'processing' => '<span class="badge badge-primary">Diproses Penjual</span>',
            'shipped' => '<span class="badge badge-primary">Dalam Pengiriman</span>',
            'delivered' => '<span class="badge badge-success">Diterima</span>',
            'cancelled' => '<span class="badge badge-danger">Dibatalkan</span>',
        ];

        return $map[$this->getOrderStatus()] ?? '<span class="badge">' . htmlspecialchars($this->getOrderStatus()) . '</span>';
    }

    // ArrayAccess Implementation
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}
