<?php

namespace App\Validators;

/**
 * Validator PartnerProductValidator
 * 
 * Melakukan validasi input data produk toko mitra, nomor seri unit fisik,
 * dan pengaturan profil lapak dalam Bahasa Indonesia.
 */
class PartnerProductValidator
{
    private array $errors = [];

    /**
     * Memvalidasi data produk baru atau pembaruan produk
     */
    public function validateProduct(array $data): bool
    {
        $this->errors = [];

        // 1. Validasi Nama Produk
        $name = trim($data['name'] ?? '');
        if (empty($name)) {
            $this->errors['name'] = 'Nama produk kamera atau lensa wajib diisi.';
        } elseif (mb_strlen($name) < 3) {
            $this->errors['name'] = 'Nama produk minimal terdiri dari 3 karakter.';
        } elseif (mb_strlen($name) > 200) {
            $this->errors['name'] = 'Nama produk maksimal 200 karakter.';
        }

        // 2. Validasi Kategori
        $categoryId = (int)($data['category_id'] ?? 0);
        if ($categoryId <= 0) {
            $this->errors['category_id'] = 'Pilih kategori produk yang sesuai.';
        }

        // 3. Validasi Opsi Rental / Jual
        $isRental = !empty($data['is_rental']) ? 1 : 0;
        $isSale = !empty($data['is_sale']) ? 1 : 0;
        if ($isRental === 0 && $isSale === 0) {
            $this->errors['is_rental'] = 'Pilih minimal satu peruntukan produk: Disewakan atau Dijual.';
        }

        // 4. Validasi Tarif Sewa Harian
        if ($isRental === 1) {
            $dailyPrice = filter_var($data['daily_price'] ?? 0, FILTER_VALIDATE_FLOAT);
            if ($dailyPrice === false || $dailyPrice <= 0) {
                $this->errors['daily_price'] = 'Tarif sewa harian harus diisi dengan angka positif yang valid.';
            } elseif ($dailyPrice < 10000) {
                $this->errors['daily_price'] = 'Tarif sewa harian minimal Rp 10.000.';
            }
        }

        // 5. Validasi Harga Jual
        if ($isSale === 1) {
            $salePrice = filter_var($data['sale_price'] ?? 0, FILTER_VALIDATE_FLOAT);
            if ($salePrice === false || $salePrice <= 0) {
                $this->errors['sale_price'] = 'Harga jual produk harus diisi dengan nominal yang valid.';
            }
        }

        return empty($this->errors);
    }

    /**
     * Memvalidasi nomor seri unit fisik
     */
    public function validateUnit(array $data): bool
    {
        $this->errors = [];

        $serial = trim($data['serial_number'] ?? '');
        if (empty($serial)) {
            $this->errors['serial_number'] = 'Nomor seri fisik (Serial Number) kamera wajib diisi.';
        } elseif (strlen($serial) < 3) {
            $this->errors['serial_number'] = 'Nomor seri minimal 3 karakter.';
        } elseif (strlen($serial) > 100) {
            $this->errors['serial_number'] = 'Nomor seri maksimal 100 karakter.';
        }

        $avail = $data['availability_status'] ?? 'available';
        if (!in_array($avail, ['available', 'rented', 'maintenance'], true)) {
            $this->errors['availability_status'] = 'Status ketersediaan tidak valid.';
        }

        return empty($this->errors);
    }

    /**
     * Memvalidasi informasi pengaturan toko
     */
    public function validateStore(array $data): bool
    {
        $this->errors = [];

        $storeName = trim($data['store_name'] ?? '');
        if (empty($storeName)) {
            $this->errors['store_name'] = 'Nama toko lapak wajib diisi.';
        } elseif (mb_strlen($storeName) < 3) {
            $this->errors['store_name'] = 'Nama toko minimal 3 karakter.';
        }

        $address = trim($data['address'] ?? '');
        if (empty($address)) {
            $this->errors['address'] = 'Alamat toko wajib diisi lengkap.';
        }

        $city = trim($data['city'] ?? '');
        if (empty($city)) {
            $this->errors['city'] = 'Kota lokasi toko wajib diisi.';
        }

        $phone = trim($data['phone'] ?? '');
        if (empty($phone)) {
            $this->errors['phone'] = 'Nomor kontak telepon atau WhatsApp toko wajib diisi.';
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
