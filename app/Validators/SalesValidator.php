<?php

declare(strict_types=1);

namespace App\Validators;

class SalesValidator
{
    /**
     * Memvalidasi data input formulir checkout pembelian kamera
     *
     * @param array $data Data input dari POST
     * @return array Array berisi daftar pesan galat (jika ada)
     */
    public function validateCheckout(array $data): array
    {
        $errors = [];

        // 1. Nama Penerima
        $recipientName = trim($data['recipient_name'] ?? '');
        if ($recipientName === '') {
            $errors['recipient_name'] = 'Nama penerima barang wajib diisi.';
        } elseif (mb_strlen($recipientName) < 3) {
            $errors['recipient_name'] = 'Nama penerima minimal 3 karakter.';
        }

        // 2. Nomor HP Penerima
        $phone = trim($data['phone'] ?? '');
        if ($phone === '') {
            $errors['phone'] = 'Nomor HP penerima wajib diisi untuk koordinasi kurir pengiriman.';
        } elseif (!preg_match('/^(\+62|62|0)[0-9]{9,13}$/', $phone)) {
            $errors['phone'] = 'Format nomor HP tidak valid. Gunakan format Indonesia yang benar (contoh: 081234567890).';
        }

        // 3. Alamat Pengiriman
        $shippingAddress = trim($data['shipping_address'] ?? '');
        if ($shippingAddress === '') {
            $errors['shipping_address'] = 'Alamat lengkap pengiriman barang wajib diisi.';
        } elseif (mb_strlen($shippingAddress) < 10) {
            $errors['shipping_address'] = 'Alamat pengiriman harus lengkap (nama jalan, nomor rumah, RT/RW, kelurahan/kecamatan).';
        }

        // 4. Kota / Kabupaten Domisili
        $city = trim($data['city'] ?? '');
        if ($city === '') {
            $errors['city'] = 'Kota atau Kabupaten pengiriman wajib diisi.';
        }

        // 5. Metode Pembayaran
        $paymentMethod = $data['payment_method'] ?? 'escrow';
        $validMethods = ['escrow', 'bank_transfer', 'ewallet'];
        if (!in_array($paymentMethod, $validMethods, true)) {
            $errors['payment_method'] = 'Metode pembayaran yang dipilih tidak valid.';
        }

        return $errors;
    }
}
