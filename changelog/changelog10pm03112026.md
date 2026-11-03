# CHANGELOG — 10pm 03-11-2026

> **Batch Rilis:** Tahap 03 (Autentikasi Pengguna & Role-Based Access Control)  
> **Project:** CamHub – Camera Marketplace Platform  
> **Tim Pengembang:** Kelompok 1  
> 1. Yogi Ario Pratama (Project Owner & Lead Developer)  
> 2. Muhammad Regieyanto (Backend & System Architect)  
> 3. Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)  
> 4. Krisna Mukti (Quality Assurance & Presentation Coordinator)  
> **Branch:** `tahap/03-autentikasi-pengguna` (Merged to `develop`)  

---

### Ringkasan Perubahan (Tahap 03: Autentikasi Pengguna & RBAC)

1. **Skema Basis Data & Akun Demo Default:**
   - Migrasi penambahan kolom `username VARCHAR(50) NULL UNIQUE` pada tabel `users`.
   - Seeding akun instan siap pakai untuk kebutuhan evaluasi dan presentasi tim:
     - Administrator: `admin` : `admin` (Role: `admin`)
     - Pelanggan (Customer): `user` : `user` (Role: `customer`)
     - Mitra Lapak: `mitra` : `mitra` (Role: `partner`)
   - Penyediaan tombol pill 1-klik pada antarmuka login untuk pengisian instan kredensial demo.

2. **Antarmuka Pengguna (100% Mockup Prototype):**
   - Halaman Login split-screen (`resources/views/auth/login.php`): Panel kiri Dark Navy `#0b192e` dengan tagline *"Sewa, Jual, Beli, Titip"*, 3 nilai keunggulan ber-checkmark (*Transaksi Aman Escrow*, *Verifikasi KYC*, *Pilihan Lengkap*), dan panel kanan form bersih.
   - Halaman Registrasi (`resources/views/auth/register.php`): Pemilihan peran akun (Pelanggan atau Mitra Lapak).
   - Tampilan Dashboard Peran Terproteksi: `customer/dashboard.php`, `partner/dashboard.php`, dan `admin/dashboard.php`.

3. **Keamanan & Manajemen Sesi:**
   - Password hashing BCRYPT via `password_hash()` dan `password_verify()`.
   - Pencegahan *Session Fixation* menggunakan `session_regenerate_id(true)` saat login sukses.
   - Proteksi Cross-Site Request Forgery (CSRF) via `csrf_token()`, `csrf_field()`, dan `verify_csrf()`.
   - Pencatatan seluruh aktivitas keamanan ke tabel `audit_logs` (LOGIN_SUCCESS, LOGIN_FAILED, REGISTER_SUCCESS, LOGOUT).

4. **Arsitektur Middleware & Routing:**
   - Perluasan `App\Core\Router` untuk mendukung chaining pipeline middleware dan metode HTTP `HEAD`.
   - `GuestMiddleware`: Membatasi pengunjung login mengakses form auth.
   - `AuthMiddleware`: Memproteksi rute privat dari akses tanpa login (HTTP 302 redirect ke `/login`).
   - `RoleMiddleware`: Membatasi akses dashboard berdasarkan hak peran (HTTP 403 Forbidden).

5. **Dokumentasi & Buku Panduan:**
   - Buku Panduan Setup & Naskah Presentasi 4 Orang Kelompok 1: `Docs/buku/03 Tahap 3.md`.
   - Rencana implementasi final dicadangkan di `Docs/tahapan/03.implementation_plan_Tahap3.md`.
   - Pembaruan `Docs/Task_Status.md` dan `Docs/Decision_Log.md` (DEC-010 s.d. DEC-014).

6. **Pengujian Mandiri & Verifikasi (100% PASS Gate):**
   - Skrip pengujian otomatis `php tests/test_auth.php`: **10 / 10 LULUS (100% PASS)**.
   - Uji regresi `tests/test_foundation.php`: **7 / 7 LULUS (100% PASS)**.
   - Uji regresi `tests/test_database.php`: **7 / 7 LULUS (100% PASS)**.
   - Verifikasi live Apache: HTTP 200 pada `/login` dan `/register`, HTTP 302 pada `/dashboard/admin`.
