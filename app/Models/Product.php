<?php

declare(strict_types=1);

namespace App\Models;

class Product
{
    public int $id;
    public int $store_id;
    public int $category_id;
    public string $name;
    public string $slug;
    public ?string $description = null;
    public ?string $condition_note = null;
    public bool $is_rental = true;
    public bool $is_sale = false;
    public bool $is_consignment = false;
    public float $sale_price = 0.0;
    public int $stock_quantity = 1;
    public float $rating_avg = 5.0;
    public int $review_count = 0;
    public string $created_at;
    public string $updated_at;

    // Joined fields from relations
    public ?string $category_name = null;
    public ?string $category_slug = null;
    public ?string $store_name = null;
    public ?string $store_city = null;
    public ?float $daily_price = null;
    public ?string $primary_image = null;
    public array $images = [];
    public array $units = [];
    public array $pricing = [];
    public array $reviews = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'is_rental' || $key === 'is_sale' || $key === 'is_consignment') {
                    $this->{$key} = (bool) $value;
                } elseif ($key === 'sale_price' || $key === 'rating_avg' || $key === 'daily_price') {
                    $this->{$key} = (float) $value;
                } elseif ($key === 'stock_quantity' || $key === 'review_count' || $key === 'id' || $key === 'store_id' || $key === 'category_id') {
                    $this->{$key} = (int) $value;
                } else {
                    $this->{$key} = $value;
                }
            }
        }
    }

    public function getFormattedDailyPrice(): string
    {
        $price = $this->daily_price ?? 0.0;
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    public function getFormattedSalePrice(): string
    {
        return 'Rp ' . number_format($this->sale_price, 0, ',', '.');
    }

    public function getStarRating(): string
    {
        return number_format($this->rating_avg, 1, '.', '');
    }

    public function isAvailableForRent(): bool
    {
        return $this->is_rental && $this->stock_quantity > 0;
    }

    public function getPrimaryImageUrl(): string
    {
        if (!empty($this->primary_image)) {
            if (str_starts_with($this->primary_image, 'http')) {
                return $this->primary_image;
            }
            return base_url($this->primary_image);
        }
        return 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=600&q=80';
    }
}
