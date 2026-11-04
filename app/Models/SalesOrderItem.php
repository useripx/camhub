<?php

declare(strict_types=1);

namespace App\Models;

use ArrayAccess;

class SalesOrderItem implements ArrayAccess
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

    public function getOrderId(): int
    {
        return (int)($this->data['order_id'] ?? 0);
    }

    public function getProductId(): int
    {
        return (int)($this->data['product_id'] ?? 0);
    }

    public function getQuantity(): int
    {
        return (int)($this->data['quantity'] ?? 1);
    }

    public function getUnitPrice(): float
    {
        return (float)($this->data['unit_price'] ?? 0);
    }

    public function getSubtotal(): float
    {
        return (float)($this->data['subtotal'] ?? 0);
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
