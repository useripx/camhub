<?php

declare(strict_types=1);

namespace App\Validators;

class ConsignmentValidator
{
    private array $errors = [];

    public function validate(array $data): bool
    {
        $this->errors = [];

        // 1. Nama Kamera / Barang
        $itemName = trim($data['item_name'] ?? '');
        if ($itemName === '') {
            $this->errors['item_name'] = 'Nama kamera / barang titip jual wajib diisi.';
        } elseif (mb_strlen($itemName) < 5) {
            $this->errors['item_name'] = 'Nama kamera minimal 5 karakter lengkap (misal: Sony A7 III Body Only).';
        } elseif (mb_strlen($itemName) > 150) {
            $this->errors['item_name'] = 'Nama kamera maksimal 150 karakter.';
        }

        // 2. Toko Mitra Tujuan
        $storeId = (int) ($data['store_id'] ?? 0);
        if ($storeId <= 0) {
            $this->errors['store_id'] = 'Silakan pilih toko mitra lapak penampung titip jual.';
        }

        // 3. Deskripsi & Kelengkapan
        $desc = trim($data['item_description'] ?? '');
        if ($desc === '') {
            $this->errors['item_description'] = 'Deskripsi kondisi fisik dan kelengkapan unit wajib diisi.';
        } elseif (mb_strlen($desc) < 15) {
            $this->errors['item_description'] = 'Deskripsi kondisi minimal 15 karakter (sebutkan fisik, sensor, dan kelengkapan dus/baterai).';
        }

        // 4. Ekspektasi Harga Jual (Requested Price)
        $requestedPrice = filter_var($data['requested_price'] ?? null, FILTER_VALIDATE_FLOAT);
        if ($requestedPrice === false || $requestedPrice === null || $requestedPrice <= 0) {
            $this->errors['requested_price'] = 'Ekspektasi harga jual harus berupa angka valid.';
        } elseif ($requestedPrice < 100000) {
            $this->errors['requested_price'] = 'Ekspektasi harga jual minimal adalah Rp 100.000.';
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

