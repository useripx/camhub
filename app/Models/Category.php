<?php

declare(strict_types=1);

namespace App\Models;

class Category
{
    public int $id;
    public string $name;
    public string $slug;
    public ?string $icon = null;
    public int $product_count = 0;
    public string $created_at;

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
}
