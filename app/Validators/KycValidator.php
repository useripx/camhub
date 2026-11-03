<?php

declare(strict_types=1);

namespace App\Validators;

class KycValidator
{
    private const MAX_FILE_SIZE = 2097152; // 2 MB in bytes
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Validate KYC application form and uploaded documents
     */
    public function validateSubmission(array $input, array $files): array
    {
        $errors = [];

        // 1. Full Name
        $fullName = trim($input['full_name'] ?? '');
        if ($fullName === '') {
            $errors['full_name'] = 'Nama lengkap sesuai KTP wajib diisi.';
        } elseif (mb_strlen($fullName) < 3) {
            $errors['full_name'] = 'Nama lengkap minimal 3 karakter.';
        }

        // 2. NIK KTP (16 Digits)
        $nik = trim($input['nik_ktp'] ?? '');
        if ($nik === '') {
            $errors['nik_ktp'] = 'Nomor NIK KTP wajib diisi.';
        } elseif (!preg_match('/^[0-9]{16}$/', $nik)) {
            $errors['nik_ktp'] = 'NIK KTP harus terdiri dari tepat 16 digit angka.';
        }

        // 3. Tanggal Lahir
        $birthDate = trim($input['birth_date'] ?? '');
        if ($birthDate === '') {
            $errors['birth_date'] = 'Tanggal lahir wajib diisi.';
        } else {
            $d = \DateTime::createFromFormat('Y-m-d', $birthDate);
            if (!$d || $d->format('Y-m-d') !== $birthDate) {
                $errors['birth_date'] = 'Format tanggal lahir tidak valid (gunakan YYYY-MM-DD).';
            } else {
                $today = new \DateTime();
                $age = $today->diff($d)->y;
                if ($age < 17) {
                    $errors['birth_date'] = 'Usia minimal untuk verifikasi identitas sewa adalah 17 tahun.';
                }
            }
        }

        // 4. Jenis Kelamin
        $gender = trim($input['gender'] ?? '');
        if (!in_array($gender, ['Laki-laki', 'Perempuan'], true)) {
            $errors['gender'] = 'Pilihan jenis kelamin tidak valid (pilih Laki-laki atau Perempuan).';
        }

        // 5. Persetujuan Pernyataan
        if (empty($input['agreement'])) {
            $errors['agreement'] = 'Anda wajib mencentang pernyataan keaslian dokumen identitas.';
        }

        // 6. Validasi Berkas Foto KTP
        $ktpError = $this->validateUploadedImage($files['ktp_file'] ?? null, 'Foto KTP Asli');
        if ($ktpError !== null) {
            $errors['ktp_file'] = $ktpError;
        }

        // 7. Validasi Berkas Foto Selfie dengan KTP
        $selfieError = $this->validateUploadedImage($files['selfie_file'] ?? null, 'Swafoto / Selfie Memegang KTP');
        if ($selfieError !== null) {
            $errors['selfie_file'] = $selfieError;
        }

        return $errors;
    }

    /**
     * Validate individual uploaded image file
     */
    public function validateUploadedImage(?array $file, string $fieldLabel): ?string
    {
        if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return "Berkas {$fieldLabel} wajib diunggah.";
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return "Terjadi kesalahan sistem saat mengunggah {$fieldLabel} (Error Code: {$file['error']}).";
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            $maxMb = self::MAX_FILE_SIZE / 1048576;
            return "Ukuran berkas {$fieldLabel} melebihi batas maksimal {$maxMb} MB.";
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return "Format berkas {$fieldLabel} tidak diizinkan. Gunakan JPG, PNG, atau WEBP.";
        }

        // Inspect real MIME type
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
                return "Tipe konten berkas {$fieldLabel} tidak valid ({$mimeType}). Unggah foto gambar asli.";
            }
        }

        return null;
    }

    /**
     * Validate customer profile edit form
     */
    public function validateProfile(array $input): array
    {
        $errors = [];

        $fullName = trim($input['full_name'] ?? '');
        if ($fullName === '') {
            $errors['full_name'] = 'Nama lengkap wajib diisi.';
        } elseif (mb_strlen($fullName) < 3) {
            $errors['full_name'] = 'Nama lengkap minimal 3 karakter.';
        }

        $phone = trim($input['phone'] ?? '');
        if ($phone === '') {
            $errors['phone'] = 'Nomor HP/WhatsApp wajib diisi.';
        } elseif (!preg_match('/^(\+62|62|0)[0-9]{8,14}$/', $phone)) {
            $errors['phone'] = 'Format nomor HP tidak valid (contoh: 08123456789).';
        }

        $city = trim($input['city'] ?? '');
        if ($city === '') {
            $errors['city'] = 'Kota domisili wajib diisi.';
        }

        $address = trim($input['address'] ?? '');
        if ($address === '') {
            $errors['address'] = 'Alamat domisili lengkap wajib diisi.';
        } elseif (mb_strlen($address) < 5) {
            $errors['address'] = 'Alamat terlalu singkat, mohon tuliskan alamat lengkap untuk keperluan pengiriman.';
        }

        return $errors;
    }
}

