# CHANGELOG — 08pm 03-11-2026

> **Batch Rilis:** Tahap 01 (Persiapan Fondasi)  
> **Project:** CamHub – Camera Marketplace Platform  
> **Project Owner:** Yogi Ario  
> **Branch:** `tahap/01-persiapan-fondasi`  

---

### Ringkasan Perubahan (Tahap 01: Persiapan Fondasi)

1. **Sistem Kontrol Versi & Keamanan Git:**
   - Inisialisasi Git repository dan integrasi remote origin GitHub `https://github.com/useripx/camhub.git`.
   - Konfigurasi branch `main`, `develop`, dan `tahap/01-persiapan-fondasi`.
   - Penambahan `.gitignore` komprehensif untuk melindungi `.env`, credential, logs, dan upload file.
   - Penyediaan template `.env.example` dan file `.env` lokal untuk server Laragon.

2. **Arsitektur Modular PHP Native:**
   - Pembuatan struktur direktori berlapis: `app/Core/`, `app/Controllers/`, `app/Models/`, `app/Repositories/`, `app/Services/`, `app/Middleware/`, `app/Validators/`, `app/Helpers/`, `config/`, `database/`, `public/`, `resources/views/`, `routes/`, `storage/`, `tests/`.
   - Penyusunan entry point `public/index.php` sebagai Front Controller tunggal dengan autoloader native PSR-4.
   - Pembangunan native router `App\Core\Router` dengan dukungan dispatching URI dan parsing parameter.
   - Penambahan file `.htaccess` (di root dan di `public/`) untuk URL rewriting.

3. **Helper & Konfigurasi:**
   - Implementasi helper global `app/Helpers/functions.php`: `env()`, `view()`, `base_url()`, `asset_url()`, `format_rupiah()`, `json_response()`, `redirect()`.
   - File konfigurasi terpusat: `config/app.php` dan `config/database.php`.

4. **Tampilan & Verifikasi Visual:**
   - Pembuatan stylesheet dasar `public/assets/css/app.css` berdasar palet warna resmi prototype: Royal Blue (`#2563eb`), Deep Slate (`#0b192e`), dan Ice Gray (`#f8fafc`).
   - Halaman fondasi `resources/views/welcome.php` mengacu pada elemen hero mockup Beranda.

5. **Dokumentasi & Skema:**
   - Penyusunan diagram ERD 22 entitas dalam format Mermaid di `Docs/scema/ERD.md`.
   - Berkas rencana kerja terstruktur `Docs/tahap/01.persiapan-fondasi.md`.
   - Berkas pelacak progres `Docs/Task_Status.md` dan `Docs/Decision_Log.md`.

6. **Pengujian Mandiri (100% Automated Testing Gate):**
   - Pembuatan dan eksekusi test runner `tests/test_foundation.php`.
   - Hasil Uji: **7 / 7 LULUS (100% PASS, 0 GAGAL / 0 ERROR)**.

