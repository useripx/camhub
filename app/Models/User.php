<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    public int $id;
    public int $role_id;
    public ?string $username;
    public string $full_name;
    public string $email;
    public string $phone;
    public string $password_hash;
    public string $status;
    public ?string $role_name = null;
    public ?string $role_display_name = null;
    public string $created_at;
    public string $updated_at;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function isAdmin(): bool
    {
        return strtolower($this->role_name ?? '') === 'admin';
    }

    public function isPartner(): bool
    {
        return strtolower($this->role_name ?? '') === 'partner';
    }

    public function isCustomer(): bool
    {
        return strtolower($this->role_name ?? '') === 'customer';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'role_id' => $this->role_id,
            'role_name' => $this->role_name,
            'role_display_name' => $this->role_display_name,
            'username' => $this->username,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
        ];
    }
}

