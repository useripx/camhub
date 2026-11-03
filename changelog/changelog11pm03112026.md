# CHANGELOG — 11pm 03-11-2026

> **Batch Rilis:** Tahap 04 (Profil Pelanggan dan Verifikasi KYC)  
> **Project:** CamHub – Camera Marketplace Platform  
> **Tim Pengembang:** Kelompok 1  
> 1. Yogi Ario Pratama (Project Owner & Lead Developer)  
> 2. Muhammad Regieyanto (Backend & System Architect)  
> 3. Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)  
> 4. Krisna Mukti (Quality Assurance & Presentation Coordinator)  
> **Branch:** `tahap/04-profil-pelanggan-kyc` (Merged to `develop` & `main`)  

---

### Ringkasan Perubahan (Tahap 04: Profil Pelanggan & Verifikasi KYC)

1. **Model, Repository & Data Management:**
   - Pembuatan entitas data model `App\Models\KycSubmission` dengan properti pemetaan tabel `kyc_submissions`.
   - Pembuatan repository `App\Repositories\KycRepository` dengan metode pencarian berdasarkan user ID, pencarian pengajuan aktif, dan penyimpanan pengajuan identitas ber-ACID transaction.
   - Pemanfaatan `user_profiles` untuk sinkronisasi otomatis status verifikasi KYC (`unverified`, `pending`, `verified`, `rejected`).

2. **Lapisan Layanan & Bisnis (Service Layer):**
   - `App\Services\ProfileService`: Menangani alur pembacaan profil pengguna terautentikasi dan pembaruan data biodata (nama lengkap, tanggal lahir, jenis kelamin, kota, alamat lengkap, dan nomor kontak).
   - `App\Services\KycService`: Menangani alur verifikasi berkas identitas, enkapsulasi penyimpanan gambar aman, pencegahan pengajuan ganda (*duplicate pending submission prevention*), sinkronisasi status profil, serta pencatatan jejak audit (*audit logging*) ke tabel `audit_logs`.

3. **Keamanan Unggah Berkas & Validasi Server-Side:**
   - `App\Validators\KycValidator`: Memvalidasi kelengkapan form KYC dalam Bahasa Indonesia (format NIK KTP 16 digit angka, tanggal lahir, jenis kelamin, file foto KTP, dan file foto selfie bersama KTP).
   - Pengecekan MIME type menggunakan PHP `finfo` (hanya mengizinkan `image/jpeg`, `image/png`, dan `image/webp`).
   - Pembatasan ukuran berkas maksimal 2MB per gambar.
   - Sanitasi nama file berkas dengan random hex kriptografis (`random_bytes(16)`), kebal terhadap *file collision* dan serangan *path traversal*.
   - Proteksi direktori `public/uploads/kyc/.htaccess` untuk menonaktifkan eksekusi script PHP (`php_flag engine off` & `SetHandler none`).

4. **Antarmuka Pengguna (100% Mockup Prototype):**
   - Halaman Formulir Verifikasi Identitas KYC (`resources/views/customer/kyc.php`): Mengikuti desain `CamHub — Verifikasi Identitas KYC.png` dengan panduan foto KTP ber-checkmark, dropzone unggah foto KTP dan foto selfie, serta persetujuan keabsahan data.
   - Halaman Status Pengajuan KYC (`resources/views/customer/kyc_status.php`): Pelacak status pengajuan real-time (kartu menunggu verifikasi, lencana terverifikasi, atau catatan perbaikan jika ditolak).
   - Halaman Kelola Biodata Profil (`resources/views/customer/profile.php`): Formulir pembaruan biodata domisili pelanggan dilengkapi indikator status KYC.
   - Pembaruan Dashboard Pelanggan (`resources/views/customer/dashboard.php`): Penambahan banner notifikasi status KYC dan tombol aksi cepat verifikasi identitas.
   - Pembaruan Navigasi Utama (`resources/views/welcome.php`): Tombol Masuk dan Daftar pada header dan hero section telah tersambung secara mulus.

5. **Dokumentasi & Buku Panduan Tim Kelompok 1:**
   - **Buku Panduan Utama Setup Proyek:** [`Docs/buku/00 Tahap 0.md`](../Docs/buku/00%20Tahap%200.md) yang memuat panduan lengkap dari nol agar seluruh tim dapat menjalankan proyek di laptop masing-masing kapan pun.
   - **Buku Panduan Tahap 04 & Naskah Presentasi:** [`Docs/buku/04 Tahap 4.md`](../Docs/buku/04%20Tahap%204.md) berisi panduan teknis dan naskah pembagian presentasi untuk 4 anggota Kelompok 1.
   - **Rencana Implementasi Final:** Dicadangkan di [`Docs/tahapan/04.implementation_plan_Tahap4.md`](../Docs/tahapan/04.implementation_plan_Tahap4.md).
   - Pembaruan [`Docs/Task_Status.md`](../Docs/Task_Status.md) dan [`Docs/Decision_Log.md`](../Docs/Decision_Log.md) (DEC-015 s.d. DEC-017).

6. **Pengujian Mandiri & Verifikasi (100% PASS Gate):**
   - Skrip pengujian otomatis mandiri `php tests/test_kyc.php`: **10 / 10 LULUS (100% PASS)**.
   - Uji regresi seluruh tahap berjalan serentak:
     - `php tests/test_foundation.php` (7/7 PASS)
     - `php tests/test_database.php` (7/7 PASS)
     - `php tests/test_auth.php` (10/10 PASS)
     - `php tests/test_kyc.php` (10/10 PASS)
     - **Total: 34 / 34 Tests LULUS (100% PASS)**.
   - Verifikasi rute HTTP Apache Live:
     - `/profil` -> HTTP 200 (terotentikasi) / HTTP 302 (tamu)
     - `/kyc` -> HTTP 200 (terotentikasi) / HTTP 302 (tamu)
     - `/kyc/status` -> HTTP 200

