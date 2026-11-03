<?php

declare(strict_types=1);

namespace App\Models;

class Store
{
    public int $id;
    public int $partner_id;
    public string $store_name;
    public string $address;
    public string $city;
    public string $phone;
    public float $rating_avg = 5.0;
    public int $total_products = 0;
    public string $created_at;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'id' || $key === 'partner_id' || $key === 'total_products') {
                    $this->{$key} = (int) $value;
                } elseif ($key === 'rating_avg') {
                    $this->{$key} = (float) $value;
                } else {
                    $this->{$key} = $value;
                }
            }
        }
    }
}
