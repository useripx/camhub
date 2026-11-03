# BUKU PANDUAN UTAMA — TAHAP 00: QUICKSTART & SETUP MASTER GUIDE

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner & Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI BUKU:** [`Docs/buku/00 Tahap 0.md`](00%20Tahap%200.md)  
> **REPOSITORY RESMI:** [https://github.com/useripx/camhub.git](https://github.com/useripx/camhub.git)  
> **TEKNOLOGI:** Pure PHP Native (Modular MVC Architecture) + PDO MySQL + Laragon

---

# 🚀 PANDUAN LENGKAP MENJALANKAN PROYEK DARI AWAL (STEP-BY-STEP)

Buku panduan ini dirancang untuk seluruh anggota **Kelompok 1**, dosen penguji, maupun pengembang baru agar dapat menjalankan proyek CamHub di laptop masing-masing dari nol hingga aplikasi dan pengujian otomatis berjalan 100%.

---

## 1. Prasyarat Sistem (Prerequisites)

Sebelum memulai, pastikan laptop Anda telah terpasang:
1. **Laragon** (Sangat direkomendasikan untuk Windows) atau stack Apache + PHP 8.1+ + MySQL 8.x.
2. **Git** untuk kontrol versi.
3. Web Browser modern (Google Chrome, Microsoft Edge, atau Mozilla Firefox).

---

## 2. Langkah-Langkah Instalasi & Setup Cepat (5 Menit)

### Langkah 1: Kloning Repository dari GitHub
Buka terminal (Git Bash, Command Prompt, atau PowerShell) di dalam folder root web server Laragon Anda (biasanya `C:\laragon\www`):

```bash
cd C:\laragon\www
git clone https://github.com/useripx/camhub.git
cd camhub
```

### Langkah 2: Jalankan Servis Laragon
1. Buka aplikasi **Laragon**.
2. Klik tombol **Start All** untuk menyalakan servis **Apache** dan **MySQL** (pastikan MySQL berjalan pada port `3306`).

### Langkah 3: Konfigurasi Lingkungan (`.env`)
Salin file template konfigurasi lingkungan `.env.example` menjadi `.env`:

```bash
# Windows PowerShell / Command Prompt:
copy .env.example .env

# Atau jika menggunakan Git Bash:
cp .env.example .env
```

*Pastikan isi berkas `.env` sesuai dengan konfigurasi Laragon standar:*
```ini
APP_NAME=CamHub
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/camhub/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=camhub_db
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 4: Inisialisasi Basis Data (Migrasi 22 Tabel Relasional)
Jalankan migrasi mandiri CamHub melalui CLI. Perintah ini akan otomatis membuat basis data `camhub_db` jika belum ada dan menyusun 22 tabel relasional:

```bash
php database/migrate.php
```

*Output yang diharapkan:*
```text
STATUS: SUKSES (22 / 22 TABEL TERVERIFIKASI)
```

Jalankan juga migrasi penambahan kolom:
```bash
php database/migrations/01_add_username_to_users.php
```

### Langkah 5: Pengisian Data Awal (Seeding Akun Cepat & Kamera Mockup)
Jalankan seeder untuk mengisi data master kategori, unit kamera prototype, dan akun siap pakai:

```bash
php database/seeders/initial_seed.php
```

*Data yang otomatis aktif:*
- **3 Peran:** `admin`, `partner`, `customer`.
- **5 Kategori:** Kamera Body, Lensa, Audio, Lighting, Aksesoris.
- **Akun Demo Instan (Siap Pakai untuk Presentasi):**
  - 🛡️ **Administrator:** username: `admin` | password: `admin` (atau email `admin@camhub.id`)
  - 👤 **Pelanggan (User):** username: `user` | password: `user` (atau email `user@camhub.id`)
  - 🏪 **Mitra Lapak:** username: `mitra` | password: `mitra` (atau email `mitra@camhub.id`)
- **6 Kamera Acuan Prototype:** 13 unit fisik dengan nomor seri unik dan 24 paket harga sewa harian.

---

## 3. Menjalankan Suite Pengujian Otomatis (100% Automated Testing Gate)

Proyek CamHub memegang teguh prinsip keandalan kualitas perangkat lunak. Sebelum membuka browser, Anda dapat memverifikasi seluruh komponen sistem melalui terminal dengan perintah:

```bash
# 1. Uji Fondasi Arsitektur (Tahap 01)
php tests/test_foundation.php

# 2. Uji Skema Database & Transaksi ACID (Tahap 02)
php tests/test_database.php

# 3. Uji Autentikasi Pengguna & RBAC Guard (Tahap 03)
php tests/test_auth.php
```

*Seluruh test suite di atas wajib menghasilkan output **100% PASS (0 GAGAL / 0 ERROR)**.*

---

## 4. Peta Rute & Tautan Pengujian di Browser

Buka browser Anda dan jelajahi endpoint berikut:

| No | Halaman / Fitur | URL Browser | Hak Akses | Keterangan |
|:---:|---|---|:---:|---|
| 1 | **Beranda Utama** | `http://localhost/camhub/public/` | Publik | Landing page dengan tombol navigasi Masuk & Daftar |
| 2 | **Status API Health** | `http://localhost/camhub/public/api/health` | Publik | Respon JSON kesehatan sistem |
| 3 | **Halaman Login** | `http://localhost/camhub/public/login` | Tamu (Guest) | Layout split-screen dengan tombol 1-klik akun demo |
| 4 | **Halaman Registrasi** | `http://localhost/camhub/public/register` | Tamu (Guest) | Pendaftaran akun baru (Pelanggan / Mitra) |
| 5 | **Dashboard Pelanggan** | `http://localhost/camhub/public/dashboard/pelanggan` | Wajib Login (Customer) | Ringkasan sewa, saldo escrow, dan status KYC |
| 6 | **Dashboard Mitra Lapak** | `http://localhost/camhub/public/dashboard/mitra` | Wajib Login (Partner) | Manajemen unit rental, etalase toko "Yogi Camera" |
| 7 | **Dashboard Administrator** | `http://localhost/camhub/public/dashboard/admin` | Wajib Login (Admin) | Pengawasan platform, verifikasi KYC, dan audit log |
| 8 | **Halaman Logout** | `http://localhost/camhub/public/logout` | Wajib Login | Keluar dari sesi dan pembersihan cookie aman |

---

## 5. Direktori & Dokumen Penting

- **[`Docs/buku/`](.)**: Kumpulan buku panduan dan naskah presentasi berurutan per tahapan:
  - `00 Tahap 0.md`: Panduan setup master dari awal (dokumen ini).
  - `01 Tahap 1.md`: Panduan & naskah presentasi Tahap 01 (Fondasi).
  - `02 Tahap 2.md`: Panduan & naskah presentasi Tahap 02 (Database 22 Tabel).
  - `03 Tahap 3.md`: Panduan & naskah presentasi Tahap 03 (Autentikasi & RBAC).
  - `04 Tahap 4.md`: Panduan & naskah presentasi Tahap 04 (Profil Pelanggan & KYC).
- **[`Docs/tahapan/`](../tahapan/)**: Berkas rencana implementasi resmi per tahapan (`01.implementation_plan_Tahap1.md`, dst.).
- **[`Docs/scema/`](../scema/)**: Kamus data basis data (`database_dictionary.md`) dan diagram ERD Mermaid (`ERD.md`).
- **[`template/`](../../template/)**: Prototype acuan UI/UX desainer (*READ-ONLY*).

---

## 6. Panduan Pemecahan Masalah (Troubleshooting)

#### Kendala 1: Halaman 404 saat membuka URL selain Beranda
- **Penyebab:** Modul Apache `mod_rewrite` belum aktif atau file `.htaccess` terlewati.
- **Solusi:** Di Laragon, klik kanan menu Laragon -> **Apache** -> **modules** -> pastikan **rewrite_module** dicentang, lalu restart Apache. Pastikan file `public/.htaccess` ada.

#### Kendala 2: Error "Access denied for user 'root'@'localhost'" saat migrasi
- **Penyebab:** Konfigurasi user atau password MySQL di file `.env` tidak sesuai dengan MySQL laptop Anda.
- **Solusi:** Buka berkas `.env` dan sesuaikan `DB_PASSWORD` (default Laragon biasanya kosong `DB_PASSWORD=`).

#### Kendala 3: Baris baru Git berubah (CRLF vs LF)
- **Solusi:** Jalankan di terminal:
  ```bash
  git config core.autocrlf true
  ```
