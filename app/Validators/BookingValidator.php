<?php

namespace App\Validators;

/**
 * Validator BookingValidator
 * 
 * Melakukan validasi data pemesanan sewa kamera dan penyewa
 * dengan pesan kesalahan terstandarisasi Bahasa Indonesia.
 */
class BookingValidator
{
    private array $errors = [];

    /**
     * Memvalidasi formulir pemesanan sewa kamera
     */
    public function validate(array $data): bool
    {
        $this->errors = [];

        // 1. Validasi Nama Lengkap
        $name = trim($data['full_name'] ?? '');
        if (empty($name)) {
            $this->errors['full_name'] = 'Nama lengkap penyewa wajib diisi.';
        } elseif (mb_strlen($name) < 3) {
            $this->errors['full_name'] = 'Nama lengkap minimal 3 karakter.';
        }

        // 2. Validasi Nomor HP
        $phone = trim($data['phone'] ?? '');
        if (empty($phone)) {
            $this->errors['phone'] = 'Nomor HP / WhatsApp wajib diisi.';
        } elseif (!preg_match('/^(\+62|62|0)[0-9]{8,14}$/', $phone)) {
            $this->errors['phone'] = 'Format nomor HP tidak valid (contoh: 081234567890).';
        }

        // 3. Validasi Alamat Pengambilan
        $address = trim($data['pickup_address'] ?? '');
        if (empty($address)) {
            $this->errors['pickup_address'] = 'Alamat pengambilan atau instruksi penjemputan wajib diisi.';
        } elseif (mb_strlen($address) < 5) {
            $this->errors['pickup_address'] = 'Alamat pengambilan minimal 5 karakter.';
        }

        // 4. Validasi Tanggal Sewa
        $startDate = trim($data['start_date'] ?? '');
        if (empty($startDate)) {
            $this->errors['start_date'] = 'Tanggal mulai sewa wajib dipilih.';
        } else {
            $d = \DateTime::createFromFormat('Y-m-d', $startDate);
            if (!$d || $d->format('Y-m-d') !== $startDate) {
                $this->errors['start_date'] = 'Format tanggal sewa tidak valid (Gunakan YYYY-MM-DD).';
            } else {
                $today = date('Y-m-d');
                if ($startDate < $today) {
                    $this->errors['start_date'] = 'Tanggal mulai sewa tidak boleh di masa lampau.';
                }
            }
        }

        // 5. Validasi Durasi Sewa
        $duration = (int)($data['duration_days'] ?? 0);
        if ($duration < 1) {
            $this->errors['duration_days'] = 'Durasi sewa minimal 1 hari.';
        } elseif ($duration > 30) {
            $this->errors['duration_days'] = 'Durasi sewa maksimal 30 hari untuk satu pemesanan.';
        }

        // 6. Validasi Metode Pembayaran
        $paymentMethod = $data['payment_method'] ?? 'escrow';
        if (!in_array($paymentMethod, ['escrow', 'bank_transfer', 'ewallet'], true)) {
            $this->errors['payment_method'] = 'Pilih metode pembayaran yang valid.';
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
