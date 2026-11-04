<?php

declare(strict_types=1);

namespace App\Models;

use ArrayAccess;

class Category implements ArrayAccess
{
    public int $id = 0;
    public string $name = '';
    public string $slug = '';
    public ?string $icon = null;
    public int $product_count = 0;
    public string $created_at = '';

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'id' || $key === 'product_count') {
                    $this->{$key} = (int) $value;
                } else {
                    $this->{$key} = $value;
                }
            }
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, (string)$offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset} ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->{$offset});
    }
}
