# BUKU PANDUAN LENGKAP PENGEMBANGAN SISTEM CAMHUB (TAHAP 00 — 13)

> **Proyek:** CamHub — Platform Marketplace & Rental Kamera Terintegrasi  
> **Teknologi:** Strict Modular Native PHP MVC (PDO) & MySQL InnoDB  
> **Tim Pengembang (Kelompok 1):**  
> 1. **Yogi Ario Pratama** (Project Owner & Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
> **Status Proyek:** 100% Selesai — 15 Test Suites (147 / 147 PASS - 100% Sukses Hijau)  

---

## DAFTAR ISI SELURUH TAHAPAN PENGEMBANGAN

0. [TAHAP 00: QUICKSTART & SETUP MASTER GUIDE](#tahap-00)
1. [TAHAP 01: PERSIAPAN FONDASI & ARSITEKTUR MVC](#tahap-01)
2. [TAHAP 02: PERANCANGAN DATABASE RELASIONAL & PDO](#tahap-02)
3. [TAHAP 03: AUTENTIKASI PENGGUNA & KEAMANAN SESI](#tahap-03)
4. [TAHAP 04: PROFIL PELANGGAN DAN VERIFIKASI KYC](#tahap-04)
5. [TAHAP 05: WEBSITE PUBLIK DAN KATALOG PRODUK](#tahap-05)
6. [TAHAP 06: DASHBOARD MITRA LAPAK & UNIT FISIK](#tahap-06)
7. [TAHAP 07: PENYEWAAN & PEMESANAN KAMERA (BOOKING & CHECKOUT)](#tahap-07)
8. [TAHAP 08: MARKETPLACE JUAL BELI KAMERA (SECOND & BARU)](#tahap-08)
9. [TAHAP 09: SISTEM TITIP JUAL KAMERA & DASHBOARD ADMIN](#tahap-09)
10. [TAHAP 10: INTEGRASI REKENING BERSAMA ESCROW & PENARIKAN DANA MITRA](#tahap-10)
11. [TAHAP 11: PUSAT KENDALI SUPER ADMINISTRATOR & AUDIT TRAIL](#tahap-11)
12. [TAHAP 12: IN-APP NOTIFICATION HUB & PELAPORAN FINANSIAL RESMI](#tahap-12)
13. [TAHAP 13: UJI KONKURENSI PESSIMISTIC LOCKING & NASKAH SIDANG AKHIR](#tahap-13)

---




--------------------------------------------------------------------------------
<div id="tahap-00"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 00: QUICKSTART & SETUP MASTER GUIDE
# Berkas Sumber Asli: Docs/buku/00 Tahap 0.md
# ==============================================================================
--------------------------------------------------------------------------------


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



--------------------------------------------------------------------------------
<div id="tahap-01"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 01: PERSIAPAN FONDASI & ARSITEKTUR MVC
# Berkas Sumber Asli: Docs/buku/01 Tahap 1.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 01: PERSIAPAN FONDASI

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner / Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>
> **LOKASI BUKU:** [`Docs/buku/01 Tahap 1.md`](01%20Tahap%201.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/01.implementation_plan_Tahap1.md`](../tahapan/01.implementation_plan_Tahap1.md)

---

# BAGIAN 1 — PANDUAN SETUP & INSTALASI TIM (STEP-BY-STEP)

Panduan ini disusun agar seluruh anggota Kelompok 1 dapat melakukan setup repositori dan menjalankan aplikasi di laptop masing-masing tanpa kendala.

### 1. Prasyarat Perangkat Lunak (Prerequisites)
Pastikan laptop Anda telah terpasang:
- **Laragon:** Versi Full (Apache 2.4+, MySQL 8.0+, PHP 8.1+).
- **Git:** Versi terbaru untuk Windows.
- **Web Browser:** Google Chrome, Microsoft Edge, atau Mozilla Firefox.

---

### 2. Langkah-Langkah Setup Lokal

#### Langkah 1: Kloning Repositori ke Direktori Laragon
Buka terminal (Git Bash atau PowerShell) dan jalankan:
```bash
cd C:\laragon\www
git clone https://github.com/useripx/camhub.git
cd camhub
```

#### Langkah 2: Pindah ke Branch Kerja Tahap 01
```bash
git checkout tahap/01-persiapan-fondasi
```

#### Langkah 3: Menyiapkan Berkas Environment (`.env`)
Salin template konfigurasi `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
*Catatan:* File `.env` sudah dikonfigurasi untuk lingkungan lokal Laragon (`DB_HOST=127.0.0.1`, `DB_USER=root`, `DB_PASS=`). Berkas ini terlindungi dan tidak akan ter-commit ke GitHub.

#### Langkah 4: Menjalankan Layanan Laragon
1. Buka aplikasi **Laragon**.
2. Klik tombol **Start All** (pastikan Apache dan MySQL berstatus running warna hijau).

#### Langkah 5: Menjalankan Pengujian Otomatis (Wajib 100% PASS)
Buka terminal di dalam folder proyek `C:\laragon\www\camhub` dan jalankan:
```bash
php tests/test_foundation.php
```
*Hasil yang diharapkan:*
```text
HASIL PENGUJIAN: 7 / 7 LULUS (100%)
STATUS: 100% PASS (0 GAGAL / 0 ERROR) — VERIFIKASI SELESAI
```

#### Langkah 6: Membuka Aplikasi di Browser
Buka browser dan akses alamat berikut:
- **URL Utama:** `http://localhost/camhub/public/` (atau `http://camhub.test/` jika Virtual Host aktif)
- **URL Health API:** `http://localhost/camhub/public/api/health`

---

# BAGIAN 2 — PENJELASAN TEKNIS ARSITEKTUR FONDASI

### 1. Mengapa Memilih Modular PHP Native?
- Memastikan pemahaman arsitektur fundamental yang kuat (*Separation of Concerns*) tanpa ketergantungan library pihak ketiga.
- Performa sangat ringan, waktu muat instan di server lokal Laragon, dan kontrol penuh atas alur request-response.

### 2. Pola Front Controller & URL Rewriting
- Seluruh request web dari pengguna diarahkan ke satu gerbang utama: [`public/index.php`](../../public/index.php).
- Didukung berkas [`.htaccess`](../../public/.htaccess) dengan modul `mod_rewrite` Apache untuk menghasilkan URL bersih (*Clean URL*).
- Komponen [`app/Core/Router.php`](../../app/Core/Router.php) bertindak sebagai dispatcher yang memetakan URI ke view atau controller.

### 3. Keamanan Tingkat Lingkungan (Zero Secret Leakage)
- Document root diisolasi pada subdirektori `public/`, sehingga kode inti aplikasi di folder `app/`, `config/`, dan file rahasia `.env` tidak dapat diakses secara langsung melalui URL publik browser.
- Berkas `.gitignore` dipasang sejak commit perdana untuk melindungi file kredensial.

---

# BAGIAN 3 — SCRIPT PRESENTASI KELOMPOK 1 (TAHAP 1)

Berikut adalah naskah pembagian bicara untuk 4 anggota kelompok dalam mempresentasikan hasil pengerjaan Tahap 01:

```text
DURASI TOTAL: ± 7-10 Menit
ALAT BANTU: Proyektor / Screen Share Browser & Terminal
SLIDE / TAMPILAN:
1. Slide Judul & Anggota Kelompok 1
2. Slide Arsitektur Modular PHP Native & Struktur Folder
3. Demo Terminal: Eksekusi Test Suite (100% PASS)
4. Demo Browser: Live Tampilan Beranda CamHub di Laragon
```

---

### 🎙 PEMBICARA 1: Yogi Ario Pratama (Project Owner & Lead Developer)
**Waktu:** 2 Menit  
**Fokus:** Pembukaan, Latar Belakang Proyek CamHub, dan Filosofi Pengembangan

> *"Selamat pagi/siang rekan-rekan dan Bapak/Ibu dosen penguji.*  
>
> *Kami dari **Kelompok 1**, yang beranggotakan saya sendiri **Yogi Ario Pratama**, rekan saya **Muhammad Regieyanto**, **Ivan Farrel Nanda Kristanto**, dan **Krisna Mukti**.*  
>
> *Pada kesempatan kali ini, kami mempresentasikan hasil implementasi **Tahap 01: Persiapan Fondasi** dari platform yang sedang kami bangun, yaitu **CamHub – Camera Marketplace Platform**.*  
>
> *CamHub dirancang sebagai ekosistem marketplace kamera terpadu di Indonesia yang melayani 4 pilar bisnis: penyewaan kamera anti-bentrok, jual-beli unit kamera, penitipan penjualan atau consignment, dan manajemen lapak mitra.*  
>
> *Di tahap pertama ini, visi utama kami adalah membangun fondasi sistem yang kokoh menggunakan **Modular PHP Native** di atas server web Laragon. Kami menerapkan prinsip tata kelola ketat: **No Plan, No Code**, di mana setiap baris kode harus didasari rencana kerja tertulis dan melewati gerbang pengujian otomatis 100% PASS.*  
>
> *Selanjutnya, rekan saya Muhammad Regieyanto akan menjelaskan struktur arsitektur modular yang telah kami bangun."*

---

### 🎙 PEMBICARA 2: Muhammad Regieyanto (Backend & System Architect)
**Waktu:** 2 Menit  
**Fokus:** Arsitektur Modular & Pemisahan Layer Sistem

> *"Terima kasih Yogi.*  
>
> *Saya **Muhammad Regieyanto**. Saya akan menjelaskan arsitektur teknis yang mendasari aplikasi CamHub.*  
>
> *Untuk menjaga agar kode PHP Native tetap rapi, mudah dirawat, dan tidak menjadi 'spaghetti code', kami membagi sistem menjadi lapisan-lapisan modular:*  
> 1. *Pertama, **Presentation Layer** di folder `resources/views/` dan `public/assets/`, di mana kode antarmuka dipisahkan total dari logika bisnis.*  
> 2. *Kedua, **Controller Layer** di `app/Controllers/` untuk menangani alur HTTP request.*  
> 3. *Ketiga, **Service Layer** di `app/Services/` yang nantinya akan memegang aturan bisnis kritis seperti perhitungan durasi sewa dan komisi.*  
> 4. *Keempat, **Repository Layer** di `app/Repositories/` untuk abstraksi basis data menggunakan PDO Prepared Statements.*  
>
> *Selain itu, kami menyiapkan autoloader native berstandar PSR-4 di `public/index.php`, sehingga setiap class di namespace `App\` dapat dipanggil secara otomatis tanpa perlu `require` manual berkali-kali.*  
>
> *Aspek keamanan routing dan lingkungan akan dijelaskan lebih detail oleh rekan saya, Ivan Farrel."*

---

### 🎙 PEMBICARA 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
**Waktu:** 2 Menit  
**Fokus:** Front Controller, Clean URL Routing, dan Keamanan Lingkungan

> *"Terima kasih Regi.*  
>
> *Halo semuanya, saya **Ivan Farrel Nanda Kristanto**.*  
>
> *Dua aspek penting yang kami selesaikan pada Tahap 01 ini adalah sistem routing dan perlindungan keamanan kredensial:*  
>
> *1. **Front Controller & Routing Native:** Kami membangun class `Router.php` di dalam `app/Core/`. Dengan bantuan file `.htaccess`, semua request yang masuk akan diproses secara terpusat oleh `public/index.php`. Sistem ini mendukung clean URL yang ramah SEO serta mampu memetakan endpoint dinamis dan API health check.*  
>
> *2. **Proteksi Dokumen Root & Kunci Rahasia:** Kami mengadopsi standar industri di mana document root web server diarahkan ke folder `public/`. Artinya, file konfigurasi rahasia seperti `.env`, file logika di folder `app/`, maupun data penyimpanan di `storage/` berada di luar jangkauan akses HTTP publik secara langsung. Ditambah dengan konfigurasi `.gitignore` sejak commit perdana, risiko kebocoran kunci API dan database lokal adalah nol (*Zero Secret Leakage*).*  
>
> *Selanjutnya, pembuktian hasil pengujian otomatis dan demo live aplikasi akan dibawakan oleh rekan saya, Krisna Mukti."*

---

### 🎙 PEMBICARA 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
**Waktu:** 2.5 Menit  
**Fokus:** Verifikasi Pengujian 100% PASS, Demo Tampilan Browser, dan Penutup

> *(Sambil membuka terminal dan menjalankan perintah uji)*  
>
> *"Terima kasih Ivan.*  
>
> *Saya **Krisna Mukti**, bertanggung jawab pada penjaminan mutu dan verifikasi pengujian.*  
>
> *Di Kelompok 1, kami memegang prinsip: **No Evidence, No Completion**. Suatu tahap tidak boleh dinyatakan selesai jika belum terbukti lulus uji 100%.*  
>
> *Di layar dapat kita lihat bersama, saya mengeksekusi test runner otomatis:*  
> `php tests/test_foundation.php`  
>
> *(Arahkan perhatian audiens ke terminal)*  
> *Sistem menguji 7 parameter kritis: mulai dari konektivitas Git remote, proteksi `.gitignore`, integritas 19 folder modular, kesiapan Router class, engine rendering template, konfigurasi aplikasi, hingga fungsi helper. Hasilnya: **7 dari 7 LULUS (100% PASS)** tanpa error.*  
>
> *(Buka browser ke alamat `http://localhost/camhub/public/`)*  
> *Sekarang mari kita lihat tampilan webnya di browser. Halaman beranda fondasi ini telah mengadopsi palet warna resmi dari mockup prototype CamHub: Deep Slate `#0b192e`, Royal Blue `#2563eb`, dan latar Ice Gray. Tampilan ini membuktikan server Apache Laragon, front controller, dan stylesheet CSS telah terintegrasi sempurna.*  
>
> *Kami juga menyediakan endpoint `http://localhost/camhub/public/api/health` yang mengembalikan respon JSON `status: OK` sebagai bukti kesiapan API.*  
>
> *Dengan ini, Tahap 01 Fondasi dinyatakan sukses 100% dan kami siap melangkah ke tahap berikutnya. Sekian presentasi dari Kelompok 1, kami membuka sesi tanya jawab jika ada masukan dari Bapak/Ibu dosen. Terima kasih."*

---

# BAGIAN 4 — PANDUAN MENJAWAB PERTANYAAN DOSEN (Q&A GUIDE)

| Kemungkinan Pertanyaan Dosen | Rekomendasi Jawaban dari Kelompok 1 |
|---|---|
| **Kenapa tidak memakai framework seperti Laravel?** | *"Sesuai ketentuan Project Owner dan rencana induk implementasi, kami menggunakan Modular PHP Native agar kami benar-benar menguasai fundamental arsitektur web, routing, dependency management, dan keamanan tanpa bergantung pada 'black box' framework."* |
| **Bagaimana cara mencegah pengguna mengakses file `.env` lewat browser?** | *"Melalui isolasi Document Root. Server web diarahkan hanya membaca folder `public/`. File `.env` berada satu tingkat di atas `public/` (di root folder), sehingga server web Apache menolak akses HTTP langsung ke berkas tersebut."* |
| **Apakah routing ini mendukung parameter URL seperti `/produk/{id}`?** | *"Ya. Router di `app/Core/Router.php` telah dirancang menggunakan regular expression regex matching (`preg_match`), sehingga siap menerima parameter dinamis pada tahapan katalog dan detail produk berikutnya."* |



--------------------------------------------------------------------------------
<div id="tahap-02"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 02: PERANCANGAN DATABASE RELASIONAL & PDO
# Berkas Sumber Asli: Docs/buku/02 Tahap 2.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 02: PERANCANGAN DATABASE

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner / Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>
> **LOKASI BUKU:** [`Docs/buku/02 Tahap 2.md`](02%20Tahap%202.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/02.implementation_plan_Tahap2.md`](../tahapan/02.implementation_plan_Tahap2.md)  
> **DIAGRAM ERD LENGKAP:** [`Docs/scema/ERD.md`](../scema/ERD.md)  
> **KAMUS DATA RESMI:** [`Docs/scema/database_dictionary.md`](../scema/database_dictionary.md)

---

# BAGIAN 1 — PANDUAN SETUP BASIS DATA TIM (STEP-BY-STEP)

Panduan langkah demi langkah bagi seluruh anggota Kelompok 1 untuk menginisialisasi basis data MySQL di server Laragon laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **MySQL** di Laragon aktif pada port **3306**.
- Berkas `.env` telah terkonfigurasi (`DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_NAME=camhub_db`, `DB_USER=root`, `DB_PASS=`).

---

### 2. Langkah-Langkah Inisialisasi Basis Data

#### Langkah 1: Masuk ke Branch Tahap 02
Buka terminal di `C:\laragon\www\camhub` dan jalankan:
```bash
git checkout tahap/02-perancangan-database
```

#### Langkah 2: Eksekusi Migrasi DDL (Otomatis Membuat 22 Tabel)
Jalankan runner migrasi mandiri CamHub:
```bash
php database/migrate.php
```
*Hasil yang diharapkan:*
- Database `camhub_db` dibuat otomatis jika belum ada.
- Seluruh berkas `database/schema.sql` dieksekusi.
- Terminal menampilkan output:
```text
STATUS: SUKSES (22 / 22 TABEL TERVERIFIKASI)
```

#### Langkah 3: Eksekusi Seeder Data Awal & Mockup
Jalankan pengisian data awal untuk mengisi data master dan kamera prototype:
```bash
php database/seeders/initial_seed.php
```
*Data yang otomatis terisi:*
1. 3 Peran: `admin`, `partner`, `customer`.
2. 5 Kategori: *Kamera Body, Lensa, Audio, Lighting, Aksesoris*.
3. Akun Administrator (`admin@camhub.id`), Akun Mitra (`yogi@camerastore.id` / Toko: "Yogi Camera"), dan Akun Customer (`regi@camhub.id` / Nama: "Mohammad Regieyanto"). Password default: `password123`.
4. 6 Kamera acuan mockup prototype lengkap dengan 13 unit fisik dan 24 skema harga sewa harian.

#### Langkah 4: Menjalankan Pengujian Basis Data (Wajib 100% PASS)
Jalankan test suite otomatis basis data:
```bash
php tests/test_database.php
```
*Hasil yang diharapkan:*
```text
HASIL PENGUJIAN: 7 / 7 LULUS (100%)
STATUS: 100% PASS (0 GAGAL / 0 ERROR) — VERIFIKASI SELESAI
```

#### Langkah 5: Memeriksa Basis Data via phpMyAdmin (Opsional)
Buka browser ke [http://localhost/phpmyadmin](http://localhost/phpmyadmin), pilih basis data `camhub_db`, dan periksa struktur 22 tabel serta relasi *Designer*-nya.

---

# BAGIAN 2 — PENJELASAN TEKNIS ARSITEKTUR BASIS DATA (22 TABEL)

### 1. Pembagian 8 Domain Relasional
Basis data CamHub dirancang tidak hanya untuk menampilkan data, melainkan memproses transaksi nyata secara aman:
1. **Otoritas & Identitas:** `roles`, `users`, `user_profiles`, `kyc_submissions` (mendukung verifikasi KTP/Selfie).
2. **Kemitraan Toko:** `partners`, `partner_stores` (mendukung mitra lapak independen).
3. **Katalog & Inventaris:** `categories`, `products`, `product_images`, `product_units`, `rental_pricing` (mendukung unit fisik bernomor seri unik).
4. **Transaksi Rental:** `rental_bookings` (menyimpan tanggal mulai, selesai, durasi, dan biaya admin).
5. **Transaksi Jual Beli:** `sales_orders`, `sales_order_items` (manajemen stok kamera).
6. **Penitipan Jual (Consignment):** `consignments` (alur komisi titip jual kamera).
7. **Keuangan & Rekening Bersama:** `payments`, `payment_transactions`, `escrow_transactions` (penahanan saldo sewa).
8. **Operasional & Audit:** `fines` (denda), `inspections` (inspeksi fisik unit), `notifications`, `audit_logs`.

### 2. Aturan Emas Anti-Bentrokan Jadwal Sewa (Rental Locking Rule)
Untuk mencegah unit fisik kamera yang sama disewa oleh dua orang di waktu bertubrukan:
$$\text{Overlap: } (\text{Start}_{\text{Baru}} \le \text{End}_{\text{Lama}}) \land (\text{End}_{\text{Baru}} \ge \text{Start}_{\text{Lama}})$$
Sistem menerapkan query *Pessimistic Locking* di dalam transaksi database:
```sql
SELECT id FROM rental_bookings 
WHERE product_unit_id = :unit_id 
  AND rental_status IN ('confirmed', 'active', 'pending_payment')
  AND NOT (end_date < :new_start OR start_date > :new_end)
FOR UPDATE;
```

### 3. Koneksi PDO Singleton & Standar Moneter
- Koneksi diatur terpusat di [`app/Core/Database.php`](../../app/Core/Database.php) menggunakan pola Singleton, menghemat penggunaan memori koneksi MySQL.
- Seluruh kolom nominal uang menggunakan `DECIMAL(15, 2)` guna mencegah galat presisi pecahan desimal (*floating-point issue*).

---

# BAGIAN 3 — SCRIPT PRESENTASI KELOMPOK 1 (TAHAP 2)

```text
DURASI TOTAL: ± 8-10 Menit
ALAT BANTU: Proyektor / Screen Share Terminal & phpMyAdmin / DBeaver
SLIDE / TAMPILAN:
1. Slide Judul & Anggota Kelompok 1
2. Diagram ERD 22 Entitas CamHub (Mermaid Diagram)
3. Demo Terminal: Eksekusi php database/migrate.php & initial_seed.php
4. Demo Uji: Eksekusi php tests/test_database.php (100% PASS)
```

---

### 🎙 PEMBICARA 1: Yogi Ario Pratama (Project Owner & Lead Developer)
**Waktu:** 2 Menit  
**Fokus:** Pembukaan, Gambaran Umum 22 Entitas, dan Alur Bisnis Data

> *"Selamat pagi/siang rekan-rekan dan Bapak/Ibu dosen penguji.*  
>
> *Melanjutkan keberhasilan Tahap 01, hari ini kami dari **Kelompok 1** yang beranggotakan saya sendiri **Yogi Ario Pratama**, bersama rekan saya **Muhammad Regieyanto**, **Ivan Farrel Nanda Kristanto**, dan **Krisna Mukti**, mempersembahkan hasil implementasi **Tahap 02: Perancangan & Implementasi Basis Data MySQL**.*  
>
> *Di tahap kedua ini, kami mewujudkan fondasi data nyata bagi platform CamHub. Kami tidak hanya membuat tabel sederhana, melainkan membangun arsitektur relasional komprehensif yang mencakup **22 entitas data**.*  
>
> *Ke-22 entitas ini dirancang presisi untuk memfasilitasi 4 model bisnis utama CamHub: mulai dari transaksi sewa kamera berbasis tanggal, penjualan produk, sistem titip jual barang bekas atau consignment, hingga pencatatan rekening bersama atau escrow.*  
>
> *Selanjutnya, rekan saya Muhammad Regieyanto akan membedah skema relasi dan aturan integritas data yang telah kami susun."*

---

### 🎙 PEMBICARA 2: Muhammad Regieyanto (Backend & System Architect)
**Waktu:** 2 Menit  
**Fokus:** Skema Relasional, 27 Foreign Keys, dan Pencegahan Double-Booking

> *(Tampilkan diagram ERD dari `Docs/scema/ERD.md`)*  
>
> *"Terima kasih Yogi.*  
>
> *Saya **Muhammad Regieyanto**. Saya akan menjelaskan bagaimana ke-22 entitas ini saling terhubung secara relasional.*  
>
> *Di layar kita melihat diagram ERD resmi CamHub. Terdapat **27 relasi Foreign Key** aktif yang kami kunci dengan constraint referensial ketat menggunakan engine **MySQL InnoDB**.*  
>
> *Salah satu keunggulan desain basis data kami adalah pemisahan antara entitas `products` (katalog umum) dan `product_units` (unit fisik riil). Sebagai contoh, toko 'Yogi Camera' memiliki produk Sony A7 III dengan stok 3 unit. Di basis data, terdapat 3 nomor seri berbeda di tabel `product_units`.*  
>
> *Hal ini krusial untuk fitur rental: sistem kami dapat melacak unit mana yang sedang disewa dan unit mana yang tersedia. Kami telah menyusun aturan indeks gabungan `idx_bookings_dates` dan query penguncian *Pessimistic Locking* `SELECT ... FOR UPDATE` untuk menjamin tidak akan pernah terjadi double-booking atau jadwal sewa yang bentrok.*  
>
> *Bagaimana koneksi dan mekanisme migrasi data ini dieksekusi akan dijelaskan oleh Ivan Farrel."*

---

### 🎙 PEMBICARA 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
**Waktu:** 2 Menit  
**Fokus:** Singleton PDO, Migration CLI Runner, dan Mockup Seeder

> *"Terima kasih Regi.*  
>
> *Saya **Ivan Farrel Nanda Kristanto**. Di sisi implementasi kode backend, kami menyelesaikan 3 komponen inti:*  
>
> *1. **Database Connection Manager (Singleton Pattern):** Di dalam `app/Core/Database.php`, kami membangun pengelola koneksi PDO berbasis Singleton. Desain ini memastikan aplikasi hanya membuka satu koneksi PDO per request, sangat hemat memori, dan mengaktifkan mode strict `PDO::ERRMODE_EXCEPTION` serta menonaktifkan emulasi prepared statements demi keamanan SQL injection tingkat tinggi.*  
>
> *2. **Migration Runner Mandiri:** Alih-alih menginstal library pihak ketiga yang membengkakkan proyek, kami membangun skrip CLI native `database/migrate.php`. Skrip ini mampu membaca DDL `database/schema.sql` dan membangun 22 tabel di Laragon secara otomatis dan idempotensial.*  
>
> *3. **Initial Data Seeder:** Kami menyediakan `database/seeders/initial_seed.php` yang secara otomatis mengisi data master: 3 peran, 5 kategori produk, akun toko mitra 'Yogi Camera', customer 'Regi', serta 6 kamera acuan dari mockup katalog (Sony A7 III, Canon R50, Fujifilm X-T30, dll.) lengkap dengan nomor seri unit dan paket harga sewa harian.*  
>
> *Selanjutnya, pembuktian eksekusi dan uji integritas transaksi ACID akan dipandu oleh Krisna Mukti."*

---

### 🎙 PEMBICARA 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
**Waktu:** 2.5 Menit  
**Fokus:** Demo Terminal Eksekusi, Pembuktian 100% PASS, dan Transaksi Rollback

> *(Membuka terminal dan bersiap menjalankan pengujian)*  
>
> *"Terima kasih Ivan.*  
>
> *Saya **Krisna Mukti** dari bagian Quality Assurance.*  
>
> *Sesuai prinsip pilar kami: **No Evidence, No Completion**, mari kita buktikan integritas basis data ini secara langsung di hadapan Bapak/Ibu dosen.*  
>
> *Pertama, saya jalankan test suite basis data:*  
> `php tests/test_database.php`  
>
> *(Arahkan perhatian audiens ke terminal)*  
> *Dapat kita lihat, sistem menjalankan 7 pengujian otomatis:*  
> 1. *Verifikasi koneksi PDO aktif.*  
> 2. *Pemeriksaan keberadaan 22 tabel lengkap.*  
> 3. *Pemeriksaan 27 foreign key constraints di information_schema.*  
> 4. *Pemeriksaan indeks performa pencarian.*  
> 5. *Integritas data master 3 role dan 5 kategori.*  
> 6. *Ketersediaan data mockup 6 kamera dan 13 unit fisik.*  
> 7. *Uji integritas transaksi ACID: sistem melakukan insert data lalu mengeksekusi `rollBack()` untuk memastikan data uji tidak mencemari basis data.*  
>
> *Hasilnya adalah **7 dari 7 LULUS (100% PASS)**.*  
>
> *Dan untuk membuktikan bahwa Tahap 02 tidak merusak pekerjaan Tahap 01 sebelumnya, saya jalankan uji regresi:*  
> `php tests/test_foundation.php`  
> *Hasilnya tetap **100% PASS**.*  
>
> *Dengan ini, basis data CamHub telah siap 100% untuk digunakan pada pengembangan modul autentikasi dan fungsional bisnis berikutnya. Terima kasih atas perhatiannya, kami menyambut pertanyaan dari Bapak/Ibu penguji."*

---

# BAGIAN 4 — PANDUAN MENJAWAB PERTANYAAN DOSEN (Q&A GUIDE)

| Kemungkinan Pertanyaan Dosen | Rekomendasi Jawaban dari Kelompok 1 |
|---|---|
| **Mengapa menggunakan tipe data `DECIMAL(15,2)` untuk harga, bukan `INT` atau `FLOAT`?** | *"Tipe `FLOAT` atau `DOUBLE` memiliki kelemahan binary floating-point representation yang dapat menghasilkan galat pembulatan sen. Tipe `DECIMAL` menjamin ketepatan nilai eksak hingga dua digit sen, standar baku pada aplikasi e-commerce dan perbankan."* |
| **Bagaimana cara menangani integritas data jika server mati di tengah proses checkout?** | *"Kami memanfaatkan engine InnoDB dengan fitur ACID Database Transactions. Di kelas `App\Core\Database`, kami menyediakan method `transaction(callable)`. Jika terjadi kegagalan sistem sebelum commit, seluruh perubahan otomatis dibatalkan (`rollBack()`), mencegah saldo terpotong tanpa adanya pesanan."* |
| **Apakah tabel `users` dipisah antara pembeli, penyewa, dan mitra?** | *"Tabel `users` kami rancang terpusat dengan relasi ke tabel `roles`. Pengguna yang menjadi mitra akan memiliki data relasional di tabel `partners` dan `partner_stores`. Desain ini memungkinkan satu akun memiliki peran yang fleksibel namun tetap terisolasi otoritasnya."* |



--------------------------------------------------------------------------------
<div id="tahap-03"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 03: AUTENTIKASI PENGGUNA & KEAMANAN SESI
# Berkas Sumber Asli: Docs/buku/03 Tahap 3.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 03: AUTENTIKASI PENGGUNA

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner / Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI BUKU:** [`Docs/buku/03 Tahap 3.md`](03%20Tahap%203.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/03.implementation_plan_Tahap3.md`](../tahapan/03.implementation_plan_Tahap3.md)  
> **ACUAN MOCKUP:** [`template/CamHub — Interactive UI UX Prototype/CamHub — Login & Register.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Login%20%26%20Register.png)  
> **TABEL DATABASE:** `users`, `roles`, `user_profiles`, `partners`, `partner_stores`, `audit_logs`

---

# BAGIAN 1 — PANDUAN SETUP & PENGUJIAN AUTENTIKASI TIM (STEP-BY-STEP)

Panduan langkah demi langkah bagi seluruh anggota Kelompok 1 untuk menjalankan dan mendemokan fitur autentikasi di server lokal Laragon laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **Apache** dan **MySQL** di Laragon aktif.
- Berkas `.env` telah memuat konfigurasi `camhub_db`.
- Database telah ter-migrate pada Tahap 02 (`php database/migrate.php`).

---

### 2. Langkah-Langkah Menjalankan di Laptop Masing-Masing

#### Langkah 1: Masuk ke Branch Tahap 03
Buka terminal di root `C:\laragon\www\camhub` dan jalankan:
```bash
git checkout tahap/03-autentikasi-pengguna
```

#### Langkah 2: Jalankan Migrasi Penambahan Kolom Username
Pastikan tabel `users` telah memiliki kolom `username` unik:
```bash
php database/migrations/01_add_username_to_users.php
```

#### Langkah 3: Eksekusi Seeder Akun Siap Pakai (Demo Credentials)
Jalankan pengisian akun demo default yang diminta Project Owner:
```bash
php database/seeders/initial_seed.php
```

*Daftar Kredensial Akun Siap Pakai untuk Demo/Dosen:*
| Peran (Role) | Username | Password | Email Alternatif | Tujuan Pengalihan Rute |
|---|---|---|---|---|
| **Administrator** | `admin` | `admin` | `admin@camhub.id` | `/dashboard/admin` |
| **Customer / User** | `user` | `user` | `user@camhub.id` | `/dashboard/pelanggan` |
| **Mitra Lapak** | `mitra` | `mitra` | `mitra@camhub.id` | `/dashboard/mitra` |

> 💡 **Fitur Kemudahan Presentasi:** Pada halaman login, terdapat kartu tombol cepat (1-klik) untuk langsung mengisi kredensial demo `admin:admin`, `user:user`, dan `mitra:mitra` secara otomatis tanpa perlu mengetik manual.

#### Langkah 4: Menjalankan Pengujian Otomatis (100% PASS Gate)
Jalankan test suite autentikasi:
```bash
php tests/test_auth.php
```
*Hasil yang diharapkan:*
```text
========================================================
HASIL PENGUJIAN TAHAP 03: 10/10 BERHASIL (100% PASS)
========================================================
```

Jalankan juga uji regresi tahapan sebelumnya:
```bash
php tests/test_foundation.php
php tests/test_database.php
```

#### Langkah 5: Pengujian Langsung di Browser
1. Buka browser ke alamat: [http://localhost/camhub/public/login](http://localhost/camhub/public/login).
2. Klik tombol pill **Admin (admin:admin)** lalu tekan **Masuk ke Akun** -> Anda akan langsung diarahkan ke Dashboard Administrator.
3. Klik tombol **Keluar (Logout)** di pojok kanan atas.
4. Coba masuk dengan **User (user:user)** -> Anda diarahkan ke Dashboard Pelanggan.
5. Coba langsung mengetik URL terlarang di address bar: `http://localhost/camhub/public/dashboard/admin` -> Sistem mendeteksi role customer dan menampilkan halaman **403 | Akses Dibatasi**.

---

# BAGIAN 2 — PENJELASAN TEKNIS ARSITEKTUR KEAMANAN & RBAC

### 1. Antarmuka Split-Screen Presisi Mockup (100% Designer Match)
- **Panel Kiri (Dark Navy `#0b192e`):** 
  - Logo CamHub dengan ikon kamera dan aksen oranye amber.
  - Tagline utama: *"Sewa, Jual, Beli, Titip. Semua Kebutuhan Fotografi."*
  - 3 Nilai Keunggulan ber-checkmark: **Transaksi Aman (Escrow)**, **Verifikasi Identitas Resmi (KYC)**, dan **Pilihan Kamera & Lensa Terlengkap**.
- **Panel Kanan (Formulir Putih Bersih):**
  - Mengakomodasi login fleksibel multi-identifier: **Username, Email, atau Nomor HP**.
  - Dilengkapi generator input tersembunyi `_token` untuk proteksi CSRF.

### 2. Pilar Keamanan Otentikasi
1. **Password Hashing BCRYPT:**
   - Semua password disimpan dalam bentuk satu arah menggunakan `password_hash($pass, PASSWORD_BCRYPT)`.
   - Tidak ada password plaintext yang tersimpan di database.
2. **Perlindungan Session Fixation Attack:**
   - Sesaat setelah password diverifikasi benar, sistem memanggil `session_regenerate_id(true)` untuk mengganti ID session lama dengan yang baru.
3. **Cross-Site Request Forgery (CSRF) Guard:**
   - Helper `csrf_token()` membangkitkan token acak 64 karakter heksadesimal berbasis `random_bytes(32)`.
   - Fungsi `verify_csrf()` memvalidasi token menggunakan `hash_equals()` untuk mencegah *timing attack*.
4. **Pencatatan Jejak Audit (Security Audit Trail):**
   - Setiap upaya login sukses (`LOGIN_SUCCESS`), login gagal (`LOGIN_FAILED`), registrasi (`REGISTER_SUCCESS`), dan logout (`LOGOUT`) dicatat otomatis ke tabel `audit_logs` lengkap dengan timestamp dan IP address pengunjung.

### 3. Pipeline Middleware pada Router Native
Router CamHub kini mendukung eksekusi pipeline middleware sebelum controller dipanggil:
- `GuestMiddleware`: Mencegah pengguna yang telah login mengakses kembali form login/register.
- `AuthMiddleware`: Memastikan request berasal dari sesi yang valid; jika belum login, otomatis redirect ke `/login` disertai pesan flash.
- `RoleMiddleware`: Memverifikasi kecocokan peran pengguna (`role:admin`, `role:partner`, `role:customer`). Akses silang peran ditolak dengan status HTTP 403.

---

# BAGIAN 3 — NASKAH SKENARIO PRESENTASI KELOMPOK 1 (4 ORANG)

Gunakan urutan berbicara ini saat presentasi progres Tahap 03 di depan dosen/auditor:

```
+-----------------------------------------------------------------------------------------+
|                                ALUR PRESENTASI TAHAP 03                                |
|                                                                                         |
|  [Yogi Ario Pratama]            --> Pembukaan, Filosofi Mockup & Demo Kredensial Cepat |
|  [Muhammad Regieyanto]          --> Arsitektur Backend, Model, Repository & BCRYPT      |
|  [Ivan Farrel Nanda Kristanto]  --> Pipeline Middleware, RBAC Guard & Proteksi CSRF    |
|  [Krisna Mukti]                 --> Quality Assurance, 100% PASS Test & Live Demo      |
+-----------------------------------------------------------------------------------------+
```

---

### 🎙️ PEMBICARA 1: Yogi Ario Pratama (Project Owner & Lead Developer)
**Peran:** Membuka presentasi, menjelaskan pencapaian antarmuka sesuai prototype, filosofi brand, dan kemudahan akses akun demo.

> *"Selamat pagi/siang Bapak/Ibu Dosen penguji dan rekan-rekan sekalian.*  
> *Saya **Yogi Ario Pratama**, mewakili **Kelompok 1**, selaku Project Owner dan Lead Developer proyek platform **CamHub**.*
>
> *Pada Tahap 03 ini, tim kami berfokus pada pembangunan fondasi keamanan terpenting dalam aplikasi marketplace, yaitu **Autentikasi Pengguna & Role-Based Access Control (RBAC)**.*
>
> *Sesuai komitmen awal kami terhadap dokumen spesifikasi dan acuan visual, tampilan halaman login dan registrasi telah kami bangun **100% presisi** mengikuti file rancangan desainer kami di `CamHub — Login & Register.png`.*
> *Kami menerapkan layout modern **Split-Screen**:*
> *1. Sisi kiri dengan panel Navy gelap menampilkan identitas CamHub, tagline 'Sewa, Jual, Beli, Titip', serta 3 jaminan utama: Transaksi Aman Escrow, Verifikasi Identitas KYC, dan Katalog Lengkap.*
> *2. Sisi kanan adalah form masuk yang sangat intuitif. Di sini, kami mendukung login multi-identifier: pengguna dapat masuk menggunakan **Username, Email, maupun Nomor HP**.*
>
> *Untuk mempermudah pengujian oleh dosen dan tim evaluator hari ini, kami juga telah menanamkan fitur **1-Click Quick Demo Credentials**: Anda cukup mengeklik tombol pill **Admin (admin:admin)**, **User (user:user)**, atau **Mitra (mitra:mitra)**, maka formulir akan terisi otomatis dan siap diuji.*
>
> *Selanjutnya, rekan saya Muhammad Regieyanto akan membedah arsitektur backend dan keamanan kata sandi yang mendasarinya. Silakan, Regi."*

---

### 🎙️ PEMBICARA 2: Muhammad Regieyanto (Backend & System Architect)
**Peran:** Menjelaskan struktur kode backend, pola Repository-Service, integritas model pengguna, dan keamanan enkripsi BCRYPT.

> *"Terima kasih, Yogi. Halo semuanya, saya **Muhammad Regieyanto**, Backend & System Architect Kelompok 1.*
>
> *Di balik tampilan antarmuka yang bersih, kami mengimplementasikan arsitektur modular berbasis **Separation of Concerns (SoC)** murni PHP Native tanpa bantuan framework pihak ketiga:*
> *1. **Model & Entitas (`App\Models\User`):** Mewakili representasi data pengguna dengan method pembantu seperti `isAdmin()`, `isPartner()`, dan `isCustomer()`.*
> *2. **Repository Layer (`App\Repositories\UserRepository`):** Seluruh query database dibungkus dalam PDO Prepared Statements murni. Kueri pencarian akun kami rancang cerdas sehingga mengenali identifier tunggal terhadap kolom `username`, `email`, atau `phone` secara aman.*
> *3. **Service Layer (`App\Services\AuthService`):** Mengelola alur logika bisnis login, pendaftaran akun, hingga pencatatan jejak audit.*
>
> *Terkait keamanan kata sandi, kami tidak pernah menyimpan password dalam bentuk teks polos. Kami menerapkan algoritma hashing standar industri **BCRYPT** melalui fungsi `password_hash()` dan verifikasi `password_verify()`. Dengan algoritma ini, data kredensial di database kebal terhadap serangan brute-force maupun rainbow table.*
>
> *Selain itu, setiap kali login sukses, sistem kami langsung mengeksekusi `session_regenerate_id(true)` untuk memitigasi risiko pembajakan sesi (Session Fixation Attack). Sesi pengguna disimpan dengan atribut cookie `HttpOnly` dan `SameSite=Lax`.*
>
> *Selanjutnya, rekan kami Ivan Farrel akan menjelaskan bagaimana rute dan akses peran dikendalikan melalui sistem Middleware native kami. Silakan, Ivan."*

---

### 🎙️ PEMBICARA 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
**Peran:** Menjelaskan perluasan Router Native, mekanisme Middleware (Auth, Guest, Role), pengalihan peran (Redirection), dan proteksi CSRF.

> *"Terima kasih, Regi. Selamat pagi/siang semuanya, saya **Ivan Farrel Nanda Kristanto**, Core Routing & Security Engineer Kelompok 1.*
>
> *Pada Tahap 03 ini, saya bertugas memperluas kemampuan **Router Native CamHub** agar mampu menjalankan **Pipeline Middleware** sebelum sebuah Controller dieksekusi. Kami membangun 3 Middleware utama yang bekerja secara deklaratif di `routes/web.php`:*
> *1. **`GuestMiddleware`:** Memastikan pengguna yang sudah memiliki sesi aktif tidak dapat mengakses kembali formulir login atau registrasi, melainkan langsung diarahkan ke dashboard peran mereka.*
> *2. **`AuthMiddleware`:** Bertindak sebagai penjaga gerbang (Route Guard). Rute privat seperti dashboard tidak akan pernah bisa dibuka tanpa session login yang sah. Pengunjung liar otomatis dialihkan ke halaman login dengan status 302.*
> *3. **`RoleMiddleware`:** Mengimplementasikan **Role-Based Access Control (RBAC)**. Middleware ini memeriksa apakah peran akun yang login cocok dengan izin rute (misalnya `role:admin`). Apabila akun Customer mencoba membuka dashboard Admin, sistem langsung menolak dengan kode HTTP **403 Forbidden** dan menampilkan halaman peringatan interaktif.*
>
> *Kami juga mengamankan seluruh form POST dengan **CSRF Token Guard**. Token unik di-generate di session dan divalidasi menggunakan perbandingan string aman `hash_equals()`. Jika ada pihak luar mencoba mengirimkan form palsu dari domain lain, request akan langsung ditolak.*
>
> *Untuk pembuktian kehandalan kode dan hasil pengujian otomatis, saya persilakan rekan kami Krisna Mukti."*

---

### 🎙️ PEMBICARA 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
**Peran:** Menampilkan laporan pengujian otomatis 100% PASS, uji regresi, mendemonstrasikan fitur di browser, dan menutup presentasi.

> *"Terima kasih, Ivan. Halo semuanya, saya **Krisna Mukti**, Quality Assurance & Presentation Coordinator Kelompok 1.*
>
> *Sebagai penanggung jawab kualitas, tugas saya adalah memastikan setiap baris kode yang ditulis telah teruji secara objektif dan tidak menimbulkan regresi terhadap tahapan sebelumnya.*
>
> *Kami membangun suite pengujian otomatis mandiri di file `tests/test_auth.php`. Pengujian ini mencakup **10 skenario krusial**, antara lain:*
> *1. Integritas render antarmuka mockup prototype.*
> *2. Validasi token CSRF (token asli diterima, token manipulasi ditolak).*
> *3. Form validator berbahasa Indonesia.*
> *4. Pendaftaran akun baru dengan enkripsi BCRYPT.*
> *5. Login instan akun Admin (`admin:admin`).*
> *6. Login instan akun Customer (`user:user`).*
> *7. Fleksibilitas login ganda via Email dan Nomor HP.*
> *8. Penolakan password salah dan pencatatan riwayat audit log.*
> *9. Proteksi Route Guard RBAC terhadap akses terlarang.*
> *10. Siklus penghancuran sesi saat Logout.*
>
> *Ketika dijalankan di terminal: **10/10 TEST BERHASIL DENGAN HASIL 100% PASS** tanpa satupun error. Tidak hanya itu, uji regresi terhadap **Tahap 01 (Foundation)** dan **Tahap 02 (Database)** tetap membukukan hasil **100% PASS**.*
>
> *(Demo Browser):*  
> *Sekarang mari kita lihat langsung di layar browser:*
> *- Kita buka `/login`, terlihat antarmuka split-screen yang sangat rapi.*
> *- Kita klik tombol pill demo **Admin**, sistem langsung membawa kita ke `/dashboard/admin`.*
> *- Kita klik **Logout**, sesi terhapus bersih.*
> *- Kita coba login dengan **User**, kita diarahkan ke `/dashboard/pelanggan`.*
>
> *Dengan seluruh pembuktian ini, kami menyatakan bahwa **Tahap 03: Autentikasi Pengguna & RBAC** telah **SELESAI 100%** dan siap dilanjutkan ke **Tahap 04: Profil Pelanggan dan KYC**.*
>
> *Terima kasih atas perhatian Bapak/Ibu Dosen dan rekan-rekan sekalian. Kami buka sesi tanya jawab jika ada masukan."*

---

# BAGIAN 4 — DAFTAR PERTANYAAN DOSEN & JAWABAN TEKNIS (Q&A CHEAT SHEET)

Berikut adalah panduan antisipasi pertanyaan teknis yang sering diajukan penguji:

#### Q1: Mengapa kalian tidak menggunakan framework seperti Laravel atau CodeIgniter untuk autentikasi?
**Jawaban (Yogi / Regi):**  
*"Sesuai aturan dan pedoman arsitektur proyek CamHub, kami menggunakan **Pure PHP Native dengan Arsitektur Modular MVC**. Tujuannya adalah memperkuat pemahaman mendalam tentang konsep dasar web security: cara kerja session fixation, hashing password, routing native, dan pipeline middleware tanpa bergantung pada 'magic' framework. Hasilnya kode kami sangat ringan, transparan, dan terstruktur rapi."*

#### Q2: Bagaimana cara kalian mencegah serangan brute-force pada password?
**Jawaban (Regi / Ivan):**  
*"Pertama, kami menggunakan hashing BCRYPT yang sengaja memiliki cost komputasi terukur untuk memperlambat upaya cracking offline. Kedua, setiap upaya login gagal dicatat secara presisi ke tabel `audit_logs` bersama alamat IP dan timestamp pengguna, sehingga siap dihubungkan dengan rate-limiting pada tahap penguatan security mendatang."*

#### Q3: Mengapa login bisa menggunakan Username, Email, sekaligus No. HP?
**Jawaban (Ivan / Yogi):**  
*"Di Indonesia, banyak pengguna lebih terbiasa login dengan nomor WhatsApp atau username singkat ketimbang mengetik alamat email yang panjang. Kami merancang kueri database pada `UserRepository` dengan klausa `WHERE username = :id1 OR email = :id2 OR phone = :id3` yang dieksekusi dengan PDO Prepared Statements, sehingga ramah pengguna lokal namun tetap 100% aman dari SQL Injection."*

#### Q4: Apa fungsi `session_regenerate_id(true)` saat login?
**Jawaban (Regi / Ivan):**  
*"Untuk mencegah serangan **Session Fixation**. Tanpa regenerasi ID, seorang penyerang bisa menyusupkan ID sesi tertentu ke browser korban sebelum login. Jika korban berhasil login dengan ID sesi tersebut, penyerang bisa mengambil alih akun korban. Dengan `session_regenerate_id(true)`, PHP langsung menghancurkan ID sesi lama dan menerbitkan ID sesi baru yang sepenuhnya acak begitu kredensial terbukti valid."*



--------------------------------------------------------------------------------
<div id="tahap-04"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 04: PROFIL PELANGGAN DAN VERIFIKASI KYC
# Berkas Sumber Asli: Docs/buku/04 Tahap 4.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 04: PROFIL PELANGGAN DAN VERIFIKASI KYC

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner / Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI BUKU:** [`Docs/buku/04 Tahap 4.md`](04%20Tahap%204.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/04.implementation_plan_Tahap4.md`](../tahapan/04.implementation_plan_Tahap4.md)  
> **ACUAN MOCKUP:** [`template/CamHub — Interactive UI UX Prototype/CamHub — Verifikasi Identitas KYC.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Verifikasi%20Identitas%20KYC.png)  
> **TABEL DATABASE:** `kyc_submissions`, `user_profiles`, `users`, `audit_logs`

---

# BAGIAN 1 — PANDUAN SETUP & PENGUJIAN FITUR KYC TIM (STEP-BY-STEP)

Panduan langkah demi langkah bagi seluruh anggota Kelompok 1 untuk menjalankan dan mendemokan fitur Profil Pelanggan dan Verifikasi KYC di laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **Apache** dan **MySQL** di Laragon aktif.
- Berkas `.env` telah terisi kredensial database `camhub_db`.
- Ekstensi PHP `fileinfo` aktif untuk pemeriksaan MIME type berkas.

---

### 2. Langkah-Langkah Menjalankan di Laptop Masing-Masing

#### Langkah 1: Masuk ke Branch Tahap 04
Buka terminal di `C:\laragon\www\camhub` dan jalankan:
```bash
git checkout tahap/04-profil-pelanggan-kyc
```

#### Langkah 2: Memastikan Folder Uploads & Keamanan Siap
Folder penyimpanan berkas unggahan telah terproteksi di:
`public/uploads/kyc/` lengkap dengan berkas konfigurasi `.htaccess` yang mematikan eksekusi script berbahaya (*execution disabled*).

#### Langkah 3: Menjalankan Pengujian Otomatis (100% PASS Gate)
Jalankan test runner mandiri modul KYC:
```bash
php tests/test_kyc.php
```
*Hasil yang diharapkan:*
```text
========================================================
HASIL PENGUJIAN TAHAP 04: 10/10 BERHASIL (100% PASS)
========================================================
```

Jalankan juga seluruh rangkaian pengujian dari Tahap 01 hingga 04:
```bash
php tests/test_foundation.php
php tests/test_database.php
php tests/test_auth.php
php tests/test_kyc.php
```
*(Seluruh 34 uji kasus terbukti LULUS 100%)*

#### Langkah 4: Pengujian Langsung di Browser
1. Buka browser ke alamat: [http://localhost/camhub/public/login](http://localhost/camhub/public/login).
2. Klik tombol pill **User (user:user)** lalu tekan **Masuk ke Akun**.
3. Di Dashboard Pelanggan:
   - Terlihat kartu **Status Verifikasi KYC**.
   - Klik tombol **🪪 Mulai Verifikasi KYC** (mengarah ke `/kyc`).
4. Pada Halaman Form KYC:
   - Terlihat panduan foto KTP ber-checkmark.
   - Isi Nama Lengkap sesuai KTP, NIK KTP (16 digit), Tanggal Lahir, dan Jenis Kelamin.
   - Unggah Foto KTP dan Swafoto memegang KTP (format JPG/PNG/WEBP, maksimal 2MB).
   - Centang kotak pernyataan keabsahan data.
   - Klik **Kirim Pengajuan Verifikasi KYC**.
5. Sistem otomatis mengalihkan ke halaman **Status KYC** (`/kyc/status`) dengan lencana kuning **Menunggu Tinjauan Admin (Pending)**.
6. Buka halaman **Profil Saya** (`/profil`): Anda dapat melihat lencana status KYC real-time serta memperbarui alamat domisili dan nomor telepon.

---

# BAGIAN 2 — PENJELASAN TEKNIS ARSITEKTUR PROFIL & KYC

### 1. Desain Visual Multi-Step Form (100% Sesuai Prototype)
Form verifikasi KYC dibangun mengacu pada prototype `CamHub — Verifikasi Identitas KYC.png`:
- **Indikator Stepper:** Menunjukkan tahapan pengisian: 1) Data Identitas KTP, 2) Unggah Berkas, dan 3) Pernyataan & Kirim.
- **Kotak Panduan KTP:** Memberikan edukasi visual kepada pelanggan cara memotret KTP yang benar (tidak terpotong, teks tajam, bukan fotokopi buram).
- **Dual Drag-and-Drop Dropzone:** Area pengunggahan berkas KTP dan Swafoto yang responsif dengan pratinjau nama dan ukuran file.

### 2. Standar Keamanan Unggah Berkas (Secure Upload Architecture)
Mengunggah file ke web server memiliki risiko keamanan tinggi (*Remote Code Execution*). CamHub menerapkan 4 lapis perlindungan:
1. **Pemeriksaan MIME Type Asli:** Tidak hanya memeriksa ekstensi nama file, melainkan membaca *magic bytes* berkas menggunakan PHP `finfo` (`image/jpeg`, `image/png`, `image/webp`).
2. **Pembatasan Ukuran:** Dibatasi maksimal **2 MB** per berkas untuk efisiensi penyimpanan server.
3. **Pengacakan Nama Berkas (Cryptographic Hash):** File disimpan dengan nama acak `ktp_{userId}_{hash}.jpg` menggunakan `random_bytes(16)`, sehingga mustahil ditebak penyerang (*Anti-Path Traversal & Anti-File Overwrite*).
4. **Isolasi Folder via `.htaccess`:** Folder `public/uploads/kyc/` dilengkapi aturan larangan eksekusi PHP sehingga berkas yang diunggah tidak akan pernah bisa dieksekusi sebagai skrip.

### 3. Logika Bisnis & Perlindungan Pengajuan Ganda
- Pelanggan yang telah memiliki pengajuan berstatus `pending` diblokir dari mengajukan ulang secara bertubi-tubi guna mencegah beban antrean pada administrator (*Rate Limiting Guard*).
- Jika pengajuan berstatus `approved`, tombol sewa terbuka penuh. Jika `rejected`, pelanggan diberikan catatan revisi (*rejection reason*) dan tombol ajukan ulang.
- Seluruh mutasi data dicatat secara transparan pada tabel `audit_logs` dengan aksi `KYC_SUBMIT` dan `PROFILE_UPDATE`.

---

# BAGIAN 3 — NASKAH SKENARIO PRESENTASI KELOMPOK 1 (4 ORANG)

Gunakan urutan berbicara ini saat presentasi progres Tahap 04 di depan dosen/auditor:

```
+-----------------------------------------------------------------------------------------+
|                                ALUR PRESENTASI TAHAP 04                                |
|                                                                                         |
|  [Yogi Ario Pratama]            --> Pembukaan, Filosofi Keamanan KYC & Presisi Mockup   |
|  [Muhammad Regieyanto]          --> Arsitektur Backend, Entitas KYC, & Upload Hashing   |
|  [Ivan Farrel Nanda Kristanto]  --> Sistem Validasi NIK, Duplicate Guard & Audit Trail  |
|  [Krisna Mukti]                 --> QA, 10/10 PASS Test Suite, Demo Browser & Penutup   |
+-----------------------------------------------------------------------------------------+
```

---

### 🎙️ PEMBICARA 1: Yogi Ario Pratama (Project Owner & Lead Developer)
**Peran:** Membuka presentasi, menjelaskan pentingnya verifikasi identitas di industri rental kamera, dan presisi mockup visual.

> *"Selamat pagi/siang Bapak/Ibu Dosen penguji dan rekan-rekan sekalian.*  
> *Saya **Yogi Ario Pratama**, mewakili **Kelompok 1**, selaku Project Owner platform **CamHub**.*
>
> *Pada Tahap 04 ini, tim kami mengembangkan modul krusial yang menjadi benteng pertahanan utama bisnis rental kamera, yaitu **Profil Pelanggan dan Verifikasi Identitas Resmi (Know Your Customer / KYC)**.*
>
> *Dalam industri penyewaan kamera, unit yang dipinjamkan bernilai puluhan juta rupiah—seperti Sony A7 III atau Canon R50. Tanpa verifikasi identitas resmi, platform rentan terhadap penipuan dan penggelapan unit. Oleh karena itu, modul KYC ini menjadi prasyarat mutlak sebelum seorang pelanggan dapat menyewa kamera.*
>
> *Kami membangun antarmuka pengajuan KYC **100% presisi** mengikuti file rancangan desainer kami di `CamHub — Verifikasi Identitas KYC.png`:*
> *1. Tersedia indikator tahapan (Stepper) yang jelas.*
> *2. Dilengkapi panduan visual pengambilan foto KTP agar pelanggan tidak salah memotret.*
> *3. Dua area unggah terpisah untuk Foto KTP Asli dan Swafoto memegang KTP.*
> *4. Pernyataan integritas hukum yang wajib disetujui pelanggan.*
>
> *Selanjutnya, rekan saya Muhammad Regieyanto akan membedah arsitektur backend dan sistem pengunggahan berkas yang aman. Silakan, Regi."*

---

### 🎙️ PEMBICARA 2: Muhammad Regieyanto (Backend & System Architect)
**Peran:** Menjelaskan struktur database `kyc_submissions`, pola Repository-Service, dan arsitektur pengunggahan berkas berkeamanan tinggi.

> *"Terima kasih, Yogi. Halo semuanya, saya **Muhammad Regieyanto**, Backend & System Architect Kelompok 1.*
>
> *Pada lapisan backend, modul ini bertumpu pada dua entitas utama: `user_profiles` dan `kyc_submissions`:*
> *1. **Model & Repository Layer (`App\Repositories\KycRepository`):** Mengelola relasi data antara pengguna, profil alamat pengiriman, dan riwayat dokumen pengajuan KYC menggunakan PDO Prepared Statements.*
> *2. **Service Layer (`App\Services\KycService`):** Mengatur alur pemindahan berkas, pengecekan status terkini, dan pencatatan jejak audit.*
>
> *Tantangan terbesar dalam fitur KYC adalah **keamanan unggah berkas (File Upload Security)**. Bila tidak diamankan dengan benar, penyerang bisa mengunggah skrip web shell PHP berbahaya. Untuk mencegah hal tersebut, kami menerapkan 4 lapisan proteksi:*
> *- Pertama, kami memeriksa **MIME type asli berkas** menggunakan `finfo` langsung pada magic header file, bukan sekadar melihat ekstensi nama.*
> *- Kedua, membatasi ukuran file maksimal 2 Megabyte.*
> *- Ketiga, seluruh file yang tersimpan di server diacak namanya menggunakan **Cryptographic Random Hash** `bin2hex(random_bytes(16))`, sehingga nama file asli pelanggan tidak dapat ditebak dan kebal terhadap bentrokan nama.*
> *- Keempat, folder upload diisolasi dengan konfigurasi `.htaccess` yang menonaktifkan mesin eksekusi skrip PHP.*
>
> *Selanjutnya, rekan kami Ivan Farrel akan menjelaskan sistem validasi NIK dan proteksi alur pengajuannya. Silakan, Ivan."*

---

### 🎙️ PEMBICARA 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
**Peran:** Menjelaskan validasi NIK KTP Indonesia, pencegahan pengajuan ganda, proteksi Route Guard, dan audit logging.

> *"Terima kasih, Regi. Selamat pagi/siang semuanya, saya **Ivan Farrel Nanda Kristanto**, Core Routing & Security Engineer Kelompok 1.*
>
> *Pada modul KYC ini, saya memastikan bahwa seluruh data yang masuk tervalidasi secara ketat di sisi server:*
> *1. **Validasi NIK KTP Indonesia:** Sistem memeriksa pola NIK menggunakan ekspresi reguler tepat 16 digit numerik serta memvalidasi usia minimal peminjam yaitu 17 tahun berdasarkan tanggal lahir.*
> *2. **Pencegahan Pengajuan Ganda (Duplicate Submission Guard):** Pelanggan yang telah memiliki berkas berstatus `pending` secara otomatis dicegah mengajukan form baru sebelum ditinjau oleh admin, mencegah penumpukan data spam di server.*
> *3. **Proteksi Route Guard:** Seluruh rute `/profil`, `/profil/edit`, `/kyc`, dan `/kyc/status` diproteksi secara ganda oleh `AuthMiddleware` dan `RoleMiddleware` khusus peran `customer`. Pengguna luar atau peran lain tidak dapat mengakses endpoint ini secara ilegal.*
> *4. **Audit Trail Keamanan:** Setiap aktivitas pengiriman KYC dan perubahan biodata profil dicatat otomatis ke tabel `audit_logs` bersama alamat IP dan rincian perubahan yang telah disanitasi.*
>
> *Selanjutnya, rekan kami Krisna Mukti akan memaparkan bukti hasil pengujian otomatis dan demo langsung di browser. Silakan, Krisna."*

---

### 🎙️ PEMBICARA 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
**Peran:** Menampilkan laporan pengujian otomatis 100% PASS, uji regresi 34 test case, live demo di browser, dan menutup presentasi.

> *"Terima kasih, Ivan. Halo semuanya, saya **Krisna Mukti**, Quality Assurance & Presentation Coordinator Kelompok 1.*
>
> *Untuk membuktikan bahwa sistem berjalan tanpa cela, kami menyusun suite pengujian otomatis mandiri di file `tests/test_kyc.php`. Pengujian ini mencakup **10 skenario ketat**, antara lain:*
> *1. Render antarmuka formulir KYC sesuai mockup.*
> *2. Penolakan NIK yang tidak 16 digit.*
> *3. Penolakan berkas bukan gambar atau file yang melebihi batas 2MB.*
> *4. Pembuktian penyimpanan berkas dengan nama acak yang aman.*
> *5. Penyimpanan entri pengajuan KYC ke MySQL berstatus 'pending'.*
> *6. Pemblokiran pengajuan ganda saat status masih pending.*
> *7. Pembaruan biodata domisili profil pelanggan.*
> *8. Sinkronisasi lencana status KYC real-time di profil dan dashboard.*
> *9. Pencatatan audit log `KYC_SUBMIT`.*
> *10. Pengujian proteksi route guard otorisasi.*
>
> *Ketika dijalankan di terminal: **10/10 TEST TAHAP 04 LULUS 100% PASS**. Kami juga menjalankan pengujian regresi seluruh tahapan sebelumnya:*
> *- Tahap 01 (Fondasi): 7/7 LULUS.*
> *- Tahap 02 (Database): 7/7 LULUS.*
> *- Tahap 03 (Autentikasi): 10/10 LULUS.*
> *- Tahap 04 (Profil & KYC): 10/10 LULUS.*
> *Total **34 dari 34 Test Case Lulus 100% tanpa satupun error**.*
>
> *(Demo Browser):*  
> *- Kita login dengan akun `user:user`.*
> *- Di dashboard, terlihat status KYC 'Belum Diajukan'.*
> *- Kita klik tombol KYC, mengisi NIK dan mengunggah berkas KTP & Selfie.*
> *- Begitu dikirim, halaman langsung berpindah ke status 'Menunggu Tinjauan Admin' dengan lencana kuning.*
> *- Status di Dashboard Pelanggan dan Profil Saya otomatis tersinkronisasi secara real-time.*
>
> *Dengan ini, kami menyatakan bahwa **Tahap 04: Profil Pelanggan dan Verifikasi KYC** telah **SELESAI 100%** dan siap dilanjutkan ke **Tahap 05: Website Publik dan Katalog Produk**.*
>
> *Terima kasih atas perhatian Bapak/Ibu Dosen sekalian. Kami persilakan jika ada pertanyaan."*

---

# BAGIAN 4 — DAFTAR PERTANYAAN DOSEN & JAWABAN TEKNIS (Q&A CHEAT SHEET)

#### Q1: Mengapa verifikasi KYC dilakukan secara manual oleh admin, bukan menggunakan OCR otomatis?
**Jawaban (Yogi / Regi):**  
*"Sesuai dokumen rencana kerja induk CamHub, verifikasi KTP dilakukan secara native dan akurat oleh tim kurasi internal platform untuk memastikan keabsahan dokumen tanpa ketergantungan biaya API pihak ketiga per request. Selain itu, alur approval massal oleh administrator telah disiapkan untuk tahap 11 (Dashboard Administrator)."*

#### Q2: Apa yang terjadi jika pengguna mencoba mengunggah skrip PHP berbahaya yang dinamai `foto.php` atau `foto.jpg.php`?
**Jawaban (Regi / Ivan):**  
*"Sistem kami menerapkan dua lapis pertahanan: Pertama, fungsi `validateUploadedImage` memeriksa konten file biner asli menggunakan `finfo(FILEINFO_MIME_TYPE)`. File PHP akan terdeteksi sebagai `text/x-php` dan langsung ditolak. Kedua, berkas yang lolos pun akan diubah namanya menjadi nama hash acak berakhiran `.jpg` dan disimpan di direktori yang telah dilengkapi konfigurasi `.htaccess` penonaktifan mesin skrip PHP."*

#### Q3: Mengapa pelanggan tidak boleh mengedit alamat email pada form edit profil?
**Jawaban (Ivan / Regi):**  
*"Email adalah identitas unik login dan akun keuangan rekening bersama (escrow). Mengubah email secara sembarangan membuka celah pembajakan akun (Account Takeover). Jika pengguna ingin mengganti email, hal tersebut membutuhkan alur verifikasi OTP terpisah di masa mendatang, sehingga pada profil pelanggan saat ini alamat email dikunci (*read-only*) demi keamanan."*



--------------------------------------------------------------------------------
<div id="tahap-05"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 05: WEBSITE PUBLIK DAN KATALOG PRODUK
# Berkas Sumber Asli: Docs/buku/05 Tahap 5.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 05: WEBSITE PUBLIK DAN KATALOG PRODUK

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner / Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI BUKU:** [`Docs/buku/05 Tahap 5.md`](05%20Tahap%205.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/05.implementation_plan_Tahap5.md`](../tahapan/05.implementation_plan_Tahap5.md)  
> **ACUAN MOCKUP:**  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Beranda.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Beranda.png)  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Katalog Produk.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Katalog%20Produk.png)  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Detail Produk.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Detail%20Produk.png)  
> **TABEL DATABASE:** `products`, `product_images`, `product_units`, `categories`, `partner_stores`, `rental_pricing`, `reviews`

---

# BAGIAN 1 — PANDUAN SETUP & PENGUJIAN FITUR KATALOG TIM (STEP-BY-STEP)

Panduan langkah demi langkah bagi seluruh anggota Kelompok 1 untuk menjalankan dan mendemokan fitur Website Publik dan Katalog Produk di laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **Apache** dan **MySQL** di Laragon aktif.
- Basis data `camhub_db` telah dimigrasi dan di-seed data kamera (`php database/seeders/initial_seed.php`).

---

### 2. Langkah-Langkah Menjalankan di Laptop Masing-Masing

#### Langkah 1: Pindah ke Branch Tahap 05
Buka terminal di root project (`C:\laragon\www\camhub`):
```bash
git checkout tahap/05-website-publik-katalog
```

#### Langkah 2: Jalankan Pengujian Otomatis Mandiri (100% PASS Gate)
Jalankan test runner modul katalog:
```bash
php tests/test_catalog.php
```
*Hasil yang diharapkan:*
```text
========================================================
   CAMHUB TEST SUITE — TAHAP 05: KATALOG & DETAIL PRODUK 
========================================================
 [PASS] TEST-CAT-01: Pembacaan Master Kategori Fotografi & Hitungan Produk Aktif
 [PASS] TEST-CAT-02: Pengambilan Produk Unggulan Beranda (Populer Disewa) dengan Relasi Toko
 [PASS] TEST-CAT-03: Fungsionalitas Pencarian Kata Kunci Kamera (Search Keyword)
 [PASS] TEST-CAT-04: Filter Katalog Berdasarkan Kategori (Category Slug Filtering)
 [PASS] TEST-CAT-05: Filter Katalog Berdasarkan Rentang Harga Sewa Harian (Min & Max Price)
 [PASS] TEST-CAT-06: Pengurutan Produk (Sorting): Harga Termurah & Termahal
 [PASS] TEST-CAT-07: Fungsionalitas Pagination Katalog (Limit, Offset, Total Pages)
 [PASS] TEST-CAT-08: Pengambilan Detail Produk Berdasarkan Slug beserta Galeri & Toko
 [PASS] TEST-CAT-09: Pemeriksaan Ketersediaan Unit Fisik Kamera (Units Availability Status)
 [PASS] TEST-CAT-10: Kalkulasi Logika Tarif Sewa & Diskon Durasi Bertingkat + Escrow
========================================================
HASIL PENGUJIAN TAHAP 05: 10/10 BERHASIL (100% PASS)
========================================================
```

#### Langkah 3: Menjalankan Seluruh Uji Regresi Platform (56 / 56 PASS)
Pastikan tidak ada regresi pada modul fondasi, basis data, autentikasi, KYC, dan peramban:
```bash
php tests/test_foundation.php; php tests/test_database.php; php tests/test_auth.php; php tests/test_kyc.php; php tests/test_browser_e2e.php; php tests/test_catalog.php
```

#### Langkah 4: Uji Coba Langsung di Browser
Buka browser (Google Chrome) dan akses URL berikut:
1. **Beranda Publik:** `http://localhost/camhub/public/`  
   *Perhatikan banner hero, kategori kamera, kartu produk sewa terpopuler, dan footer identitas Kelompok 1.*
2. **Katalog Produk & Filter:** `http://localhost/camhub/public/katalog`  
   *Uji fitur pencarian nama kamera (misal: "Sony"), filter kategori, filter tarif harian, dan sorting harga.*
3. **Detail Produk & Kalkulator Sewa:** `http://localhost/camhub/public/produk/canon-eos-r50`  
   *Klik chip pilihan durasi (1 Hari, 3 Hari - Diskon 5%, 7 Hari - Diskon 10%, 14 Hari - Diskon 15%), perhatikan perhitungan subtotal, diskon, biaya escrow Rp 5.000, dan tombol booking yang terbarui otomatis.*

---

# BAGIAN 2 — NASKAH PRESENTASI RESMI KELOMPOK 1 (TAHAP 05)

Naskah ini disusun terstruktur untuk 4 pembicara secara berurutan, siap dipraktikkan di hadapan dosen penguji.

```
+-----------------------------------------------------------------------------------------+
| URUTAN PEMBICARA PRESENTASI TAHAP 05:                                                   |
| 1. Yogi Ario Pratama            -> Pembukaan, Visi Etalase Publik & Demo Mockup Visual |
| 2. Muhammad Regieyanto          -> Arsitektur Repositori SQL, Query Filter & Performa   |
| 3. Ivan Farrel Nanda Kristanto  -> Clean Routing, Logika Bisnis & Kalkulator Sewa       |
| 4. Krisna Mukti                 -> Pengujian Otomatis 10 DoD, Uji Regresi & Penutup     |
+-----------------------------------------------------------------------------------------+
```

---

### Pembicara 1: Yogi Ario Pratama (Project Owner & Lead Developer)
**Peran:** Membuka presentasi, menjelaskan tujuan bisnis Tahap 05, dan mendemokan kesesuaian antarmuka dengan 3 prototipe acuan.

> *"Selamat pagi/siang kepada Bapak/Ibu Dosen Penguji dan rekan-rekan sekalian.*
> 
> *Saya **Yogi Ario Pratama**, mewakili Kelompok 1 sebagai Project Owner dan Lead Developer proyek platform **CamHub – Camera Marketplace Platform**.*
> 
> *Setelah pada empat tahap sebelumnya kami sukses meletakkan fondasi modular, merancang 22 tabel relasional MySQL, membangun sistem autentikasi multi-peran, serta mengamankan identitas penyewa melalui verifikasi KYC, pada hari ini kami dengan bangga mempresentasikan pencapaian **Tahap 05: Website Publik dan Katalog Produk**.*
> 
> *Tahap 05 adalah etalase utama yang menghubungkan calon penyewa dengan seluruh inventaris kamera di platform kami. Pada tahap ini, kami mengimplementasikan 3 halaman inti yang 100% presisi mengacu pada berkas prototipe desain yang telah disetujui:*
> 1. *Pertama, **Halaman Beranda (`/`)**: Menghadirkan hero banner bernuansa Dark Navy dengan tagline 'Sewa kamera, abadikan cerita', kotak pencarian cepat lintas kategori, grid kategori fotografi interaktif, serta etalase kamera terpopuler disewa yang diambil langsung dari database.*
> 2. *Kedua, **Halaman Katalog (`/katalog`)**: Menyediakan navigasi penjelajahan komprehensif dengan bilah filter multidimensi di sisi kiri dan grid kartu produk responsif di sisi kanan lengkap dengan informasi tarif sewa harian dan rating ulasan.*
> 3. *Ketiga, **Halaman Detail Produk (`/produk/{slug}`)**: Menampilkan galeri visual interaktif, spesifikasi teknis unit, tab kelengkapan sewa, data toko mitra terverifikasi, serta kartu kalkulator estimasi sewa.*
> 
> *Selanjutnya, rekan saya Muhammad Regieyanto akan membedah arsitektur data dan strategi kueri dinamis yang menopang katalog ini. Kepada Regi, saya persilakan."*

---

### Pembicara 2: Muhammad Regieyanto (Backend & System Architect)
**Peran:** Menjelaskan desain arsitektur data, relasi tabel `products`, `partner_stores`, `categories`, dan optimasi kueri filter dinamis.

> *"Terima kasih kepada Saudara Yogi. Salam sejahtera bagi kita semua, perkenalkan saya **Muhammad Regieyanto**, Backend & System Architect Kelompok 1.*
> 
> *Tantangan teknis utama dalam membangun katalog marketplace adalah efisiensi pemrosesan data ketika pengguna menerapkan banyak filter secara bersamaan—mulai dari pencarian kata kunci, kategori, tipe sewa atau beli, rentang harga, hingga pengurutan data.*
> 
> *Pada Tahap 05 ini, kami menerapkan arsitektur repository pattern melalui kelas `ProductRepository` dan `CategoryRepository` murni menggunakan PDO PHP Native tanpa framework:*
> 1. *Untuk kueri beranda dan katalog, kami melakukan relasi `JOIN` terindeks antara tabel `products`, `categories`, dan `partner_stores`. Tarif sewa harian dihitung secara modular dari subquery `rental_pricing` untuk durasi 1 hari.*
> 2. *Pada metode `getCatalogFiltered()`, penyusunan klausul `WHERE` dilakukan secara modular berbasis array prepared statement. Parameter kata kunci diamankan dengan binding `:kw1` dan `:kw2`, kategori dengan `:cat_id` atau `:cat_slug`, dan rentang harga melalui evaluasi HAVING.*
> 3. *Paginasi katalog diatur secara presisi dengan klausul `LIMIT :limit OFFSET :offset`, di mana total data dihitung efisien melalui kueri pembungkus `COUNT(*)`.*
> 4. *Seluruh data yang disajikan terhubung langsung dengan 13 unit fisik terdaftar pada tabel `product_units`, sehingga ketersediaan kamera di lapak dapat dipantau secara real-time.*
> 
> *Berikutnya, arsitektur routing bersih dan logika kalkulator estimasi sewa akan dijelaskan oleh rekan saya, Ivan Farrel. Silakan Ivan."*

---

### Pembicara 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
**Peran:** Memaparkan konfigurasi rute web, sanitasi parameter, dan logika bisnis kalkulasi sewa bertingkat.

> *"Terima kasih Saudara Regi. Selamat pagi/siang Bapak/Ibu Dosen, nama saya **Ivan Farrel Nanda Kristanto**, bertindak sebagai Core Routing & Security Engineer Kelompok 1.*
> 
> *Fokus saya pada Tahap 05 berpusat pada dua aspek krusial: perutean URL yang ramah SEO (*Clean SEO-Friendly URLs*) dan integritas kalkulasi finansial tarif sewa:*
> 1. *Pada berkas `routes/web.php`, kami mendaftarkan rute publik bersih:*
>    - *Rute root `/` ditangani oleh `HomeController@index`.*
>    - *Rute katalog `/katalog` ditangani oleh `CatalogController@index`.*
>    - *Rute detail produk `/produk/{slug}` ditangani oleh `ProductDetailController@show` menggunakan wildcard slug dinamis yang diekstrak oleh router native buatan kami.*
> 2. *Dari segi keamanan, seluruh parameter query string yang masuk dari formulir filter melalui proses sanitasi ketat di `ProductCatalogService` menggunakan `htmlspecialchars()` dan casting tipe numerik untuk mencegah serangan Cross-Site Scripting (XSS).*
> 3. *Untuk logika pemesanan, kami merancang `RentalCalculatorService`:*
>    - *Pengguna dapat memilih durasi cepat atau memilih tanggal ambil dan tanggal kembali.*
>    - *Sistem menerapkan aturan diskon durasi sewa bertingkat: sewa 3 hari ke atas mendapat diskon 5%, sewa 7 hari ke atas mendapat diskon 10%, dan sewa 14 hari ke atas mendapat diskon 15%.*
>    - *Di akhir, biaya layanan rekening bersama escrow sebesar Rp 5.000 ditambahkan secara transparan ke dalam total estimasi biaya.*
>    - *Logika ini diimplementasikan ganda: di sisi frontend menggunakan JavaScript interaktif untuk kenyamanan visual calon penyewa, dan di sisi backend untuk validasi sebelum pemesanan disimpan.*
> 
> *Selanjutnya, rekan kami Krisna Mukti akan memaparkan bukti pengujian otomatis dan hasil verifikasi kualitas sistem. Kepada Krisna, dipersilakan."*

---

### Pembicara 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
**Peran:** Memaparkan metodologi pengujian, laporan eksekusi 10 DoD Tahap 05, verifikasi uji regresi 56/56 PASS, dan menutup presentasi.

> *"Terima kasih Saudara Ivan. Salam hormat kepada Bapak/Ibu Dosen Penguji, perkenalkan saya **Krisna Mukti**, Quality Assurance & Presentation Coordinator Kelompok 1.*
> 
> *Sesuai prinsip utama tata kelola proyek kami: 'No Approval, No Code. No Evidence, No Completion', setiap tahapan wajib dibuktikan dengan pengujian otomatis yang dapat diverifikasi ulang kapan saja.*
> 
> *Untuk Tahap 05, kami telah menyusun skrip pengujian otomatis mandiri di `tests/test_catalog.php` yang mencakup **10 parameter Definition of Done (DoD)**:*
> - *Mulai dari pengujian pembacaan master kategori, kueri produk unggulan beranda, pencarian kata kunci, filter kategori dan harga, sorting multi-kriteria, pembagian halaman paginasi, pembacaan detail produk slug, ketersediaan unit fisik, hingga ketepatan matematis kalkulator diskon durasi dan biaya escrow.*
> - *Hasil eksekusi `php tests/test_catalog.php` menunjukkan angka sempurna: **10 dari 10 pengujian LULUS (100% PASS)**.*
> 
> *Lebih dari itu, kami juga menjalankan uji regresi menyeluruh dari Tahap 01 hingga Tahap 05:*
> - *Tahap 01 Fondasi: 7/7 PASS*
> - *Tahap 02 Database 22 Tabel: 7/7 PASS*
> - *Tahap 03 Autentikasi & RBAC: 10/10 PASS*
> - *Tahap 04 Profil Pelanggan & KYC: 10/10 PASS*
> - *Uji Peramban Otomatis (E2E Browser): 12/12 PASS*
> - *Tahap 05 Katalog & Detail Produk: 10/10 PASS*
> - *Sehingga total ada **56 skenario pengujian aktif** di platform CamHub dengan tingkat kelulusan **100% PASS tanpa satupun error**.*
> 
> *Demikian presentasi dari Kelompok 1 untuk Tahap 05. Kami siap membuka sesi tanya jawab dan mendemokan fitur interaktif secara langsung kepada Bapak/Ibu Dosen Penguji. Terima kasih."*

---

# BAGIAN 3 — SIMULASI TANYA JAWAB DOSEN PENGUJI (Q&A)

Berikut panduan jawaban atas pertanyaan yang paling sering diajukan dosen mengenai fitur katalog dan pencarian produk:

#### Pertanyaan 1: "Bagaimana cara sistem mencegah SQL Injection pada fitur pencarian dan filter rentang harga katalog?"
**Jawaban (dijawab oleh Muhammad Regieyanto):**
> *"Kami tidak pernah menggabungkan variabel input mentah secara langsung ke dalam string SQL (*tidak ada string concatenation*). Seluruh parameter pencarian (`q`), kategori (`category`), dan harga (`min_price`, `max_price`) diikat menggunakan **PDO Prepared Statements** dengan parameter named placeholders (seperti `:kw1`, `:cat_id`, `:min_price`). Selain itu, input teks telah disanitasi menggunakan `htmlspecialchars()` dan nilai numerik di-casting secara eksplisit menggunakan `(float)` atau `(int)`."*

#### Pertanyaan 2: "Mengapa kalkulator estimasi sewa perlu diimplementasikan di dua sisi (JavaScript dan PHP Service)?"
**Jawaban (dijawab oleh Ivan Farrel Nanda Kristanto):**
> *"Di sisi frontend (JavaScript), kalkulasi interaktif diperlukan untuk User Experience (UX) yang responsif, sehingga calon penyewa langsung melihat perubahan subtotal dan diskon saat mengganti durasi hari tanpa reload halaman. Namun, dari sisi keamanan, input frontend tidak boleh dipercaya secara mentah. Oleh karena itu, kami menyediakan `RentalCalculatorService` di backend yang memvalidasi ulang seluruh perhitungan tarif saat formulir booking disubmit, sehingga manipulasi harga via inspect element tidak akan berdampak pada nilai transaksi di database."*

#### Pertanyaan 3: "Bagaimana sistem menjamin bahwa unit kamera yang tampil di detail produk benar-benar tersedia secara fisik?"
**Jawaban (dijawab oleh Yogi Ario Pratama):**
> *"Pada tabel `product_units`, setiap unit fisik memiliki nomor seri (*serial number*) unik dan kolom status ketersediaan (`availability_status`). Saat halaman detail dibuka, sistem mengeksekusi `countAvailableUnits()` yang hanya menghitung unit fisik yang berstatus `'available'`. Jika seluruh unit fisik suatu kamera sedang disewa, status lencana pada antarmuka secara otomatis berubah menjadi 'Sedang Disewa' dan tombol sewa dibatasi demi mencegah bentrokan jadwal sewa."*



--------------------------------------------------------------------------------
<div id="tahap-06"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 06: DASHBOARD MITRA LAPAK & UNIT FISIK
# Berkas Sumber Asli: Docs/buku/06 Tahap 6.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 06: DASHBOARD MITRA LAPAK

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner & Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI BUKU:** [`Docs/buku/06 Tahap 6.md`](06%20Tahap%206.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/06.implementation_plan_Tahap6.md`](../tahapan/06.implementation_plan_Tahap6.md)  
> **ACUAN PROTOTYPE RESMI:** [`template/CamHub — Interactive UI UX Prototype/CamHub — Dashboard Mitra Lapak.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Dashboard%20Mitra%20Lapak.png)  
> **TABEL TERKAIT:** `partners`, `partner_stores`, `products`, `product_units`, `rental_pricing`, `rental_bookings`, `sales_orders`, `audit_logs`

---

# BAGIAN 1 — PANDUAN SETUP & PENGUJIAN FITUR MITRA LAPAK (STEP-BY-STEP)

Panduan langkah demi langkah bagi seluruh anggota Kelompok 1 untuk menjalankan dan mendemokan fitur Dashboard Mitra Lapak di laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **Apache** dan **MySQL** di Laragon aktif.
- Basis data `camhub_db` telah dimigrasi dan di-seed data default (`mitra:mitra` / *Yogi Camera*).

---

### 2. Langkah-Langkah Menjalankan di Laptop Masing-Masing

#### Langkah 1: Pindah ke Branch Tahap 06
Buka terminal di root project (`C:\laragon\www\camhub`):
```bash
git checkout tahap/06-dashboard-mitra
```

#### Langkah 2: Jalankan Pengujian Otomatis Mandiri (100% PASS Gate)
Jalankan test runner modul dashboard mitra:
```bash
php tests/test_partner_dashboard.php
```
*Hasil yang diharapkan:*
```text
========================================================
   CAMHUB TEST SUITE — TAHAP 06: DASHBOARD MITRA LAPAK  
========================================================
 [PASS] TEST-MITRA-01: Proteksi Hak Akses RBAC Mitra
 [PASS] TEST-MITRA-02: Resolusi Toko Mitra (Yogi Camera)
 [PASS] TEST-MITRA-03: Kalkulasi Metrik Agregat Lapak
 [PASS] TEST-MITRA-04: Daftar 'Produk Saya' Berhasil Dimuat
 [PASS] TEST-MITRA-05: Penambahan Kamera Baru oleh Mitra
 [PASS] TEST-MITRA-06: Manajemen Unit Fisik Kamera
 [PASS] TEST-MITRA-07: Proteksi Anti-IDOR Terverifikasi
 [PASS] TEST-MITRA-08: Pemantauan Jadwal Sewa Unit
 [PASS] TEST-MITRA-09: Ringkasan Saldo Dompet Mitra
 [PASS] TEST-MITRA-10: Pembaruan Profil Toko Mitra
========================================================
HASIL PENGUJIAN TAHAP 06: 10/10 BERHASIL (100% PASS)
========================================================
```

#### Langkah 3: Jalankan Uji Peramban Otomatis (Browser E2E)
```bash
php tests/test_browser_e2e.php
```
*Hasil yang diharapkan: 17/17 BERHASIL (100% PASS)*

#### Langkah 4: Uji Coba Langsung di Browser
1. Buka browser: `http://localhost/camhub/public/login`
2. Klik tombol pill demo **Mitra (`mitra:mitra`)** atau ketik username `mitra` dan password `mitra`.
3. Tekan **Masuk ke Akun**.
4. Browser akan otomatis mengarahkan ke `http://localhost/camhub/public/dashboard/mitra`.
5. Anda akan melihat tampilan yang **100% persis prototype** `CamHub — Dashboard Mitra Lapak.png`:
   - Sidebar navy gelap dengan teks **CamHub** dan nama toko **Yogi Camera**.
   - 3 Kartu metrik: **Total Produk**, **Pesanan Aktif**, dan **Saldo Tersedia (Rp 2.450.000)**.
   - Daftar horizontal **Produk Saya** (Sony A7 III, Canon EOS R50, Fujifilm X-T30) dengan badge hijau **Tersedia**.
   - Tombol **+ Tambah Produk** untuk mendaftarkan kamera baru.
   - Halaman **Unit Fisik** untuk mendaftarkan nomor seri unik kamera.

---

# BAGIAN 2 — NASKAH & SKENARIO PRESENTASI (PEMBAGIAN PERAN 4 ANGGOTA)

Gunakan pembagian peran ini saat mempresentasikan Tahap 06 di hadapan dosen penguji atau audiens.

### 🎤 1. Pembicara 1: Yogi Ario Pratama (Project Owner & Lead Developer)
- **Tugas:** Pembukaan, Gambaran Umum Bisnis Mitra, dan Demo Antarmuka.
- **Naskah:**
  > *"Selamat pagi/siang Bapak/Ibu dosen dan rekan-rekan sekalian. Saya Yogi Ario Pratama selaku Project Owner dan Lead Developer dari Kelompok 1.*
  >
  > *Pada Tahap 06 ini, kami menyelesaikan pilar vital bagi penyedia inventaris di platform CamHub, yaitu **Dashboard Mitra Lapak**. Dashboard ini dibangun 100% presisi mengacu pada prototype resmi `CamHub — Dashboard Mitra Lapak.png`.*
  >
  > *Seperti yang dapat Bapak/Ibu saksikan pada layar, saat mitra kami 'Yogi Camera' login, sistem langsung menyajikan tiga kartu metrik utama: Total Produk aktif di etalase, Pesanan Sewa yang sedang berjalan, dan Saldo Dompet yang siap dicairkan sebesar Rp 2.450.000.*
  >
  > *Di bawahnya, etalase 'Produk Saya' menampilkan daftar unit sewa kamera secara rapi dan modular. Selanjutnya rekan saya, Muhammad Regieyanto, akan menjelaskan arsitektur backend dan pengolahan datanya."*

---

### 🎤 2. Pembicara 2: Muhammad Regieyanto (Backend & System Architect)
- **Tugas:** Arsitektur Basis Data, Relasi Multi-Table, dan Logika Agregasi.
- **Naskah:**
  > *"Terima kasih Yogi. Saya Muhammad Regieyanto, Backend & System Architect Kelompok 1.*
  >
  > *Di sisi backend, dashboard ini mengintegrasikan lima tabel inti relasional MySQL: `partners`, `partner_stores`, `products`, `product_units`, dan `rental_pricing`.*
  >
  > *Tantangan utama yang kami selesaikan pada tahap ini adalah pengelolaan unit fisik kamera. Satu induk produk (misalnya Canon EOS R50) dapat memiliki beberapa unit fisik nyata dengan nomor seri berbeda (`serial_number`). Dengan tabel `product_units`, mitra dapat melacak kondisi dan ketersediaan tiap unit apakah berstatus 'available', 'rented', atau 'maintenance'.*
  >
  > *Seluruh kueri agregasi dioptimalkan menggunakan indexing komposit pada `store_id` dan `availability_status` sehingga waktu kalkulasi tetap berada di bawah 5 milidetik. Selanjutnya, Ivan Farrel akan menjelaskan aspek keamanan dan kontrol akses routing."*

---

### 3. Pembicara 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
- **Tugas:** Keamanan Hak Akses (RBAC), Pipeline Middleware, dan Proteksi Anti-IDOR.
- **Naskah:**
  > *"Terima kasih Regie. Saya Ivan Farrel Nanda Kristanto, Core Routing & Security Engineer Kelompok 1.*
  >
  > *Fokus utama saya pada Tahap 06 adalah memastikan isolasi data dan keamanan tingkat tinggi bagi para mitra. Kami menerapkan dua lapisan proteksi:*
  >
  > *Pertama, **RBAC Guard Pipeline**: Rute `/dashboard/mitra` diproteksi oleh middleware `auth` dan `role:partner`. Jika akun pelanggan (customer) mencoba mengakses rute ini, sistem secara instan memblokir dengan status HTTP 403 Forbidden.*
  >
  > *Kedua, **Proteksi Anti-IDOR (Insecure Direct Object Reference)**: Saat mitra mengedit atau menghapus produk maupun unit fisik, `PartnerRepository` memverifikasi kepemilikan toko secara berlapis menggunakan klausa `WHERE id = :id AND store_id = :store_id`. Toko Mitra A secara absolut tidak dapat melihat atau mengubah barang milik Toko Mitra B.*
  >
  > *Selanjutnya, Krisna Mukti akan memaparkan hasil pengujian kualitas dan verifikasi otomatisnya."*

---

### 4. Pembicara 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
- **Tugas:** Pembuktian Uji Otomatis (DoD), Uji Regresi Platform, dan Penutup.
- **Naskah:**
  > *"Terima kasih Ivan. Saya Krisna Mukti, Quality Assurance & Presentation Coordinator Kelompok 1.*
  >
  > *Sesuai prinsip ketat kami: 'No Approval, No Code. No Evidence, No Completion', seluruh fitur Tahap 06 telah melalui verifikasi pengujian otomatis:*
  >
  > *1. **Test Suite Mandiri Tahap 06 (`test_partner_dashboard.php`):** 10 dari 10 pengujian LULUS 100%. Mulai dari validasi RBAC, kueri metrik, operasi CRUD kamera, pendaftaran serial number, hingga proteksi anti-IDOR.*
  > *2. **Browser E2E Automated Suite (`test_browser_e2e.php`):** 17 dari 17 skenario peramban LULUS 100%.*
  > *3. **Uji Regresi Kumulatif:** Seluruh 7 test suite platform kami jalankan serentak, dan menghasilkan **71 dari 71 test LULUS (100% PASS)** tanpa regresi sama sekali.*
  >
  > *Dengan demikian, Tahap 06 Dashboard Mitra Lapak resmi kami nyatakan selesai dengan sempurna dan siap dilanjutkan ke Tahap 07: Penyewaan & Pemesanan Kamera. Terima kasih atas perhatian Bapak/Ibu sekalian."*

---

# BAGIAN 3 — CHECKLIST VERIFIKASI SEBELUM SIDANG / PRESENTASI
- [x] Pastikan Apache dan MySQL di Laragon aktif.
- [x] Jalankan `php tests/test_partner_dashboard.php` (Wajib 10/10 PASS).
- [x] Jalankan `php tests/test_browser_e2e.php` (Wajib 17/17 PASS).
- [x] Siapkan tab browser di `http://localhost/camhub/public/login` dan gunakan akun `mitra:mitra`.



--------------------------------------------------------------------------------
<div id="tahap-07"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 07: PENYEWAAN & PEMESANAN KAMERA (BOOKING & CHECKOUT)
# Berkas Sumber Asli: Docs/buku/07 Tahap 7.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 07: PENYEWAAN & PEMESANAN KAMERA (BOOKING & CHECKOUT)

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner & Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI BUKU:** [`Docs/buku/07 Tahap 7.md`](07%20Tahap%207.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/07.implementation_plan_Tahap7.md`](../tahapan/07.implementation_plan_Tahap7.md)  
> **ACUAN PROTOTYPE RESMI:** [`template/CamHub — Interactive UI UX Prototype/CamHub — Booking & Checkout.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Booking%20&%20Checkout.png)  
> **TABEL TERKAIT:** `rental_bookings`, `payments`, `escrow_transactions`, `product_units`, `products`, `partner_stores`, `users`, `audit_logs`

---

# BAGIAN 1 — PANDUAN SETUP & PENGUJIAN FITUR CHECKOUT (STEP-BY-STEP)

Panduan langkah demi langkah bagi seluruh anggota Kelompok 1 untuk menjalankan dan mendemokan fitur Booking & Checkout di laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **Apache** dan **MySQL** di Laragon aktif.
- Basis data `camhub_db` telah termigrasi dan berisi data seed kamera & unit fisik.
- Pengguna demo terdaftar: `user:user` (Pelanggan) dan `mitra:mitra` (Toko Mitra).

---

### 2. Langkah-Langkah Menjalankan di Laptop Masing-Masing

#### Langkah 1: Pindah ke Branch Tahap 07
Buka terminal di root project (`C:\laragon\www\camhub`):
```bash
git checkout tahap/07-penyewaan-pemesanan
```

#### Langkah 2: Jalankan Pengujian Otomatis Modul Booking (100% PASS Gate)
Jalankan test runner mandiri modul penyewaan:
```bash
php tests/test_booking.php
```
*Hasil yang diharapkan:*
```text
========================================================
   CAMHUB TEST SUITE — TAHAP 07: BOOKING & CHECKOUT     
========================================================
 [PASS] TEST-BOOK-01: Proteksi Hak Akses Checkout
 [PASS] TEST-BOOK-02: Auto-fill Profil Checkout
 [PASS] TEST-BOOK-03: Validasi Formulir Server-Side
 [PASS] TEST-BOOK-04: Alokasi Unit Fisik Bebas Benturan
 [PASS] TEST-BOOK-05: Penolakan Booking Saat Unit Penuh
 [PASS] TEST-BOOK-06: Presisi Matematis Kalkulasi Sewa
 [PASS] TEST-BOOK-07: Integritas Transaksi ACID MySQL
 [PASS] TEST-BOOK-08: Pencatatan Rekening Bersama Escrow
 [PASS] TEST-BOOK-09: Tanda Terima Pesanan (Receipt)
 [PASS] TEST-BOOK-10: Sinkronisasi Riwayat Pesanan
========================================================
HASIL PENGUJIAN TAHAP 07: 10/10 BERHASIL (100% PASS)
========================================================
```

#### Langkah 3: Jalankan Uji Regresi Menyeluruh Platform (81/81 PASS)
```bash
php tests/test_foundation.php
php tests/test_database.php
php tests/test_auth.php
php tests/test_kyc.php
php tests/test_catalog.php
php tests/test_partner_dashboard.php
php tests/test_booking.php
php tests/test_browser_e2e.php
```
*Hasil yang diharapkan: Seluruh 81 test LULUS 100% (81/81 PASS).*

#### Langkah 4: Uji Coba Langsung Interaktif di Google Chrome
1. Buka browser: `http://localhost/camhub/public/login`
2. Klik tombol pill **CUSTOMER (`user:user`)** lalu klik **Masuk ke Akun**.
3. Akses halaman checkout Sony A7 III: `http://localhost/camhub/public/checkout/sony-a7-iii?duration=3`
4. Perhatikan kesesuaian layout 2 kolom persis prototype:
   - **Kolom Kiri ("Informasi Penyewa"):** Formulir terisi otomatis dengan nama dan nomor HP user, opsi Tanggal Sewa, Durasi Sewa dropdown, dan metode pembayaran Rekening Bersama (Escrow) terseleksi default.
   - **Kolom Kanan ("Ringkasan Pesanan"):** Box sticky dengan thumbnail kamera `📷 Sony A7 III · 3 hari`, Subtotal Rp 712.500, Biaya admin Rp 5.000, dan Total Pembayaran Rp 717.500 berwarna biru bold 32px.
5. Klik tombol **Lanjutkan Pembayaran**.
6. Sistem memproses transaksi ACID:
   - Mengalokasikan nomor seri unit fisik yang kosong (misal: `SONYA7III-001` atau `SONYA7III-002`).
   - Menyimpan transaksi ke `rental_bookings`.
   - Mengamankan pembayaran ke tabel `payments` dan `escrow_transactions` dengan status dana `held`.
   - Menghasilkan kode booking resmi (misal: `#RENT-00003`).
7. Browser otomatis mengarah ke halaman Nota Tanda Terima (`/booking/{id}/sukses`).
8. Klik tombol **Lihat Pesanan Saya** (`/pesanan-saya`) untuk memverifikasi riwayat rental aktif.

---

# BAGIAN 2 — NASKAH & SKENARIO PRESENTASI (PEMBAGIAN PERAN 4 ANGGOTA)

Gunakan pembagian peran ini saat mempresentasikan Tahap 07 di hadapan dosen penguji atau audiens.

### 🎤 1. Pembicara 1: Yogi Ario Pratama (Project Owner & Lead Developer)
- **Tugas:** Pembukaan, Nilai Bisnis Alur Penyewaan, dan Demo Antarmuka Checkout.
- **Naskah:**
  > *"Selamat pagi/siang Bapak/Ibu dosen dan rekan-rekan sekalian. Saya Yogi Ario Pratama selaku Project Owner dan Lead Developer Kelompok 1.*
  >
  > *Pada Tahap 07 ini, kami merealisasikan core transaction flow dari CamHub, yaitu **Modul Penyewaan & Pemesanan Kamera (Booking & Checkout)**. Modul ini dirancang 100% presisi mengikuti prototype resmi `CamHub — Booking & Checkout.png`.*
  >
  > *Sebagai platform rental kamera terpercaya, kenyamanan dan transparansi adalah prioritas kami. Saat penyewa memilih durasi sewa pada halaman katalog, sistem langsung membawa mereka ke halaman checkout dua kolom ini.*
  >
  > *Data penyewa terisi secara otomatis dari profil KYC mereka untuk mempercepat konversi, sementara di sebelah kanan, kartu Ringkasan Pesanan menyajikan rincian biaya secara transparan: subtotal sewa, biaya administrasi Rp 5.000, serta penegasan garansi penahanan dana Rekening Bersama Escrow.*
  >
  > *Selanjutnya rekan saya Muhammad Regieyanto akan menguraikan arsitektur backend, algoritma alokasi unit bebas benturan, dan mekanisme ACID transaksinya."*

---

### 🎤 2. Pembicara 2: Muhammad Regieyanto (Backend & System Architect)
- **Tugas:** Algoritma Alokasi Unit Fisik Bebas Benturan, Transaksi ACID MySQL, dan Arsitektur Escrow.
- **Naskah:**
  > *"Terima kasih Yogi. Saya Muhammad Regieyanto, Backend & System Architect Kelompok 1.*
  >
  > *Di balik tampilan antarmuka yang elegan, modul ini menyelesaikan tantangan paling kompleks dalam bisnis rental kamera: **Unit Allocation & Collision-Free Scheduling**.*
  >
  > *Sebuah model kamera di katalog dapat memiliki banyak unit fisik riil dengan nomor seri unik (`serial_number`). Ketika pelanggan memesan pada rentang tanggal tertentu, `BookingRepository` menjalankan kueri anti-tabrakan jadwal dengan logika:*
  > *Unit dialokasikan HANYA JIKA unit tersebut tidak memiliki jadwal sewa aktif lain yang tumpang-tindih pada rentang tanggal yang diminta.*
  >
  > *Jika seluruh unit penuh, sistem secara cerdas menolak pemesanan tanpa merusak data.*
  >
  > *Selain itu, kami menjamin integritas finansial dengan **ACID Transaction**: dalam satu transaksi MySQL yang terkunci atomic, sistem secara bersamaan mencatat order ke `rental_bookings`, mengunci tagihan ke `payments`, menahan dana penyewa ke `escrow_transactions` dengan status `held`, dan merekam ke `audit_logs`.*
  >
  > *Selanjutnya, Ivan Farrel akan menjelaskan aspek pengamanan routing, otorisasi sesi, dan validasi inputnya."*

---

### 🎤 3. Pembicara 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
- **Tugas:** Validasi Formulir Server-Side, Proteksi Route Checkout & Receipt, serta Keamanan Parameter URL.
- **Naskah:**
  > *"Terima kasih Regie. Saya Ivan Farrel Nanda Kristanto, Core Routing & Security Engineer Kelompok 1.*
  >
  > *Dalam pengerjaan Tahap 07, fokus utama saya adalah memastikan transaksi terlindungi secara ketat dari potensi eksploitasi dan manipulasi data:*
  >
  > *1. **Proteksi Hak Akses (AuthMiddleware):** Rute `/checkout/{slug}`, `/checkout/{slug}/proses`, dan `/pesanan-saya` diproteksi ketat. Pengguna anonim langsung diredirect ke halaman login dengan pesan peringatan.*
  > *2. **Server-Side Validation:** Kami menerapkan `BookingValidator` yang memvalidasi nama pemesan, nomor HP Indonesia yang sah, memastikan tanggal mulai sewa tidak lampau (tidak boleh sebelum hari ini), dan membatasi durasi sewa maksimal 30 hari.*
  > *3. **Otorisasi Nota & Anti-Tampering:** Nota tanda terima pada rute `/booking/{id}/sukses` dilengkapi otorisasi kepemilikan (`$userId`). Pengguna lain tidak diizinkan mengintip nota pemesanan milik penyewa lain.*
  > *4. **Routing Engine PHP 8:** Kami juga menyempurnakan `Router::executeAction` dengan penanganan argumen posisi (`array_values`) untuk menjamin kompatibilitas parameter dinamis pada seluruh rute sistem.*
  >
  > *Selanjutnya, Krisna Mukti akan memaparkan bukti hasil pengujian dan verifikasi otomatisnya."*

---

### 🎤 4. Pembicara 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
- **Tugas:** Laporan Hasil Pengujian Otomatis (DoD), Verifikasi Live Browser di Chrome, dan Penutup.
- **Naskah:**
  > *"Terima kasih Ivan. Saya Krisna Mukti, Quality Assurance & Presentation Coordinator Kelompok 1.*
  >
  > *Mengacu pada komitmen teguh kami: 'No Approval, No Code. No Evidence, No Completion', seluruh fitur Tahap 07 telah diverifikasi secara komprehensif:*
  >
  > *1. **Automated Unit & Integration Test (`test_booking.php`):** 10 dari 10 Definition of Done LULUS 100%. Mencakup validasi hak akses, alokasi unit fisik bebas benturan, penolakan saat unit penuh, presisi formula biaya admin Rp 5.000, transaksi ACID, pencatatan status escrow 'held', hingga sinkronisasi riwayat pesanan.*
  > *2. **Live Foreground Chrome Testing:** Pengujian dilakukan secara langsung di peramban Google Chrome menggunakan Chrome DevTools Protocol. Kami menguji alur login pelanggan, pembukaan form checkout, verifikasi rincian harga presisi prototype, simulasi pemesanan, dan penerbitan nota tanda terima #RENT-00003 secara real-time.*
  > *3. **Uji Regresi Kumulatif Seluruh Tahap:** Kami menjalankan 8 test suite platform secara serentak (Tahap 01 s.d Tahap 07 + Browser E2E). Hasilnya: **81 dari 81 skenario pengujian LULUS 100% (81/81 PASS)**.*
  >
  > *Dengan bukti ini, Tahap 07 Penyewaan & Pemesanan Kamera resmi kami nyatakan SELESAI dengan sempurna. Terima kasih atas perhatian Bapak/Ibu sekalian."*

---

# BAGIAN 3 — CHECKLIST VERIFIKASI SEBELUM SIDANG / PRESENTASI
- [x] Pastikan Apache dan MySQL di Laragon dalam kondisi aktif.
- [x] Jalankan `php tests/test_booking.php` (Wajib 10/10 PASS).
- [x] Jalankan seluruh test suite platform (Wajib 81/81 PASS).
- [x] Buka Google Chrome di `http://localhost/camhub/public/login` dan demonstrasikan pemesanan Sony A7 III dengan durasi 3 hari.
- [x] Perlihatkan nota tanda terima `#RENT-XXXXX` dengan rincian rekening bersama Escrow.



--------------------------------------------------------------------------------
<div id="tahap-08"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 08: MARKETPLACE JUAL BELI KAMERA (SECOND & BARU)
# Berkas Sumber Asli: Docs/buku/08 Tahap 8.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 08: MARKETPLACE JUAL BELI KAMERA (SECOND & BARU)

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner & Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI BUKU:** [`Docs/buku/08 Tahap 8.md`](08%20Tahap%208.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/08.implementation_plan_Tahap8.md`](../tahapan/08.implementation_plan_Tahap8.md)  
> **ACUAN PROTOTYPE RESMI:**  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Detail Produk.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Detail%20Produk.png)  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Booking & Checkout.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Booking%20&%20Checkout.png)  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Dashboard Pelanggan.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Dashboard%20Pelanggan.png)  
>  
> **TABEL TERKAIT:** `sales_orders`, `sales_order_items`, `payments`, `escrow_transactions`, `products`, `partner_stores`, `users`, `audit_logs`

---

# BAGIAN 1 — PANDUAN SETUP & PENGUJIAN FITUR JUAL BELI (STEP-BY-STEP)

Panduan langkah demi langkah bagi seluruh anggota Kelompok 1 untuk menjalankan dan mendemokan fitur Marketplace Jual Beli Kamera di laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **Apache** dan **MySQL** di Laragon dalam kondisi aktif.
- Basis data `camhub_db` telah termigrasi (`database/migrations/02_allow_sales_escrow.php` sudah dieksekusi).
- Akun demo siap pakai: `user:user` (Pelanggan) dan `mitra:mitra` (Toko Mitra).

---

### 2. Langkah-Langkah Menjalankan di Laptop Masing-Masing

#### Langkah 1: Pindah ke Branch Tahap 08
Buka terminal di root project (`C:\laragon\www\camhub`):
```bash
git checkout tahap/08-marketplace-jual-beli
```

#### Langkah 2: Jalankan Pengujian Otomatis Modul Marketplace (100% PASS Gate)
Jalankan test runner mandiri modul jual beli:
```bash
php tests/test_marketplace.php
```
*Hasil yang diharapkan:*
```text
========================================================
   CAMHUB TEST SUITE — TAHAP 08: MARKETPLACE JUAL BELI  
========================================================
 [PASS] TEST-SALE-01: Proteksi Hak Akses Checkout
 [PASS] TEST-SALE-02: Validasi Kamera Tersedia Dijual
 [PASS] TEST-SALE-03: Auto-fill Profil Pembeli
 [PASS] TEST-SALE-04: Validasi Formulir Server-Side
 [PASS] TEST-SALE-05: Presisi Matematis Kalkulasi
 [PASS] TEST-SALE-06: Transaksi Finansial ACID MySQL
 [PASS] TEST-SALE-07: Integritas sales_order_items
 [PASS] TEST-SALE-08: Penahanan Dana Escrow
 [PASS] TEST-SALE-09: Otorisasi Nota Tanda Terima
 [PASS] TEST-SALE-10: Sinkronisasi Riwayat Pelanggan
========================================================
HASIL PENGUJIAN TAHAP 08: 10/10 BERHASIL (100% PASS)
========================================================
```

#### Langkah 3: Jalankan Uji Regresi Menyeluruh Platform (91/91 PASS)
```bash
php tests/test_foundation.php
php tests/test_database.php
php tests/test_auth.php
php tests/test_kyc.php
php tests/test_catalog.php
php tests/test_partner_dashboard.php
php tests/test_booking.php
php tests/test_marketplace.php
php tests/test_browser_e2e.php
```
*Hasil yang diharapkan: Seluruh 9 test suite LULUS 100% (91/91 PASS).*

#### Langkah 4: Uji Coba Langsung Interaktif di Google Chrome
1. Buka browser: `http://localhost/camhub/public/login`
2. Klik tombol pill **CUSTOMER (`user:user`)** lalu klik **Masuk ke Akun**.
3. Akses halaman detail Sony A7 III: `http://localhost/camhub/public/produk/sony-a7-iii`
4. Perhatikan pada card kanan terdapat tombol sekunder: **Beli Unit Ini (Rp 24.500.000)**.
5. Klik tombol tersebut, browser otomatis membuka halaman checkout pembelian `http://localhost/camhub/public/beli/sony-a7-iii`:
   - **Kolom Kiri:** Formulir *Informasi Pengiriman Barang* terisi otomatis dengan data profil domisili pembeli, alamat lengkap, kota, catatan kurir opsional, dan opsi Rekening Bersama Escrow CamHub terpilih default.
   - **Kolom Kanan:** Kartu sticky *Ringkasan Pesanan* menghitung: Harga Kamera Rp 24.500.000 + Ongkir Berasuransi Rp 25.000 + Biaya Layanan Rekber Rp 5.000 = Total Pembayaran Rp 24.530.000 dalam teks biru tebal 32px.
6. Klik tombol **Lanjutkan Pembayaran**.
7. Sistem mengeksekusi transaksi ACID MySQL:
   - Membuat record pesanan di `sales_orders` dengan nomor invoice resmi (misal: `#BUY-00002`).
   - Menyimpan item barang di `sales_order_items`.
   - Menahan dana pembayaran di `escrow_transactions` dengan status `held`.
   - Mencatat aktivitas ke `audit_logs`.
8. Browser otomatis diredirect ke halaman nota konfirmasi pembelian (`/pesanan/beli/{id}/sukses`).
9. Klik tombol **Lihat Jual Beli Saya** untuk memverifikasi pesanan langsung masuk ke tab transaksi pelanggan (`/pesanan-saya?type=sale`).

---

# BAGIAN 2 — NASKAH & SKENARIO PRESENTASI (PEMBAGIAN PERAN 4 ANGGOTA)

Gunakan pembagian peran ini saat mempresentasikan Tahap 08 di hadapan dosen penguji atau audiens.

### 🎤 1. Pembicara 1: Yogi Ario Pratama (Project Owner & Lead Developer)
- **Tugas:** Pembukaan, Nilai Bisnis Marketplace Jual Beli, dan Demo Alur Pembelian.
- **Naskah:**
  > *"Selamat pagi/siang Bapak/Ibu dosen dan rekan-rekan sekalian. Saya Yogi Ario Pratama selaku Project Owner dan Lead Developer Kelompok 1.*
  >
  > *Pada Tahap 08 ini, kami resmi meluncurkan pilar transaksi kedua di CamHub, yaitu **Marketplace Jual Beli Kamera (Second Terverifikasi & Baru)**.*
  >
  > *Selama ini, transaksi jual beli kamera bekas di media sosial sangat rawan penipuan, barang rusak tidak sesuai deskripsi, atau dana dibawa kabur penjual. CamHub memecahkan masalah ini dengan menghadirkan etalase jual beli terpercaya.*
  >
  > *Ketika pengguna membuka detail kamera yang bertanda siap dijual, tersedia tombol 'Beli Unit Ini'. Saat diklik, sistem menyajikan antarmuka checkout 2 kolom yang bersih dan modern. Data penerima terisi otomatis dari profil KYC mereka, rincian ongkir berasuransi Rp 25.000 dan proteksi rekening bersama Rp 5.000 disajikan transparan.*
  >
  > *Selanjutnya rekan saya Muhammad Regieyanto akan menguraikan arsitektur backend transaksi ACID dan pengamanan pembukuannya."*

---

### 🎤 2. Pembicara 2: Muhammad Regieyanto (Backend & System Architect)
- **Tugas:** Arsitektur Basis Data, Transaksi ACID Sales Orders & Escrow Marketplace.
- **Naskah:**
  > *"Terima kasih Yogi. Saya Muhammad Regieyanto, Backend & System Architect Kelompok 1.*
  >
  > *Di sisi backend, modul Marketplace Jual Beli ini mengintegrasikan lima tabel inti MySQL: `sales_orders`, `sales_order_items`, `payments`, `escrow_transactions`, dan `audit_logs`.*
  >
  > *Tantangan arsitektur yang kami selesaikan pada tahap ini adalah unifikasi sistem Rekening Bersama Escrow. Melalui migrasi `02_allow_sales_escrow`, tabel `escrow_transactions` kini secara modular dapat menahan dana baik untuk penyewaan (`booking_id`) maupun untuk pembelian marketplace (`sales_order_id`).*
  >
  > *Seluruh proses checkout dibungkus dalam **ACID Transaction**: pembentukan invoice resmi `#BUY-XXXXX`, pencatatan kuantitas dan harga satuan barang, penguncian status pembayaran 'pending', penahanan dana seharga barang di escrow dengan status 'held', dan audit log terekam secara atomic tanpa risiko data terpecah.*
  >
  > *Selanjutnya, Ivan Farrel akan menjelaskan aspek validasi formulir dan pengamanan rute sistem."*

---

### 🎤 3. Pembicara 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
- **Tugas:** Validasi Pengiriman Server-Side, Proteksi Route Pembelian, dan Keamanan Nota Invoice.
- **Naskah:**
  > *"Terima kasih Regie. Saya Ivan Farrel Nanda Kristanto, Core Routing & Security Engineer Kelompok 1.*
  >
  > *Dalam Tahap 08 ini, fokus utama rekayasa keamanan meliputi:*
  >
  > *1. **Proteksi Hak Akses (AuthMiddleware):** Rute `/beli/{slug}`, `/beli/{slug}/proses`, dan `/jual-beli-saya` diproteksi ketat. Pengguna tamu langsung diarahkan ke form login.*
  > *2. **Server-Side Validation:** `SalesValidator` memvalidasi nama penerima minimal 3 karakter, nomor HP berformat sah Indonesia, alamat pengiriman terperinci minimal 10 karakter, serta kota tujuan.*
  > *3. **Otorisasi Nota & Anti-IDOR:** Halaman tanda terima `/pesanan/beli/{id}/sukses` diproteksi klausa kepemilikan user ID. Pelanggan lain secara absolut tidak dapat mengintip invoice pembeli lain.*
  > *4. **Penyelarasan Tampilan Dashboard Pelanggan:** Kami juga menuntaskan perombakan dashboard pelanggan menjadi layout sidebar navy rapi persis prototype `CamHub — Dashboard Pelanggan.png`, meniadakan tombol-tombol ganda yang sebelumnya tercecer.*
  >
  > *Selanjutnya, Krisna Mukti akan memaparkan bukti hasil pengujian otomatis dan verifikasinya."*

---

### 🎤 4. Pembicara 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
- **Tugas:** Laporan Hasil Pengujian Otomatis (DoD), Verifikasi Live Browser di Chrome, dan Penutup.
- **Naskah:**
  > *"Terima kasih Ivan. Saya Krisna Mukti, Quality Assurance & Presentation Coordinator Kelompok 1.*
  >
  > *Mengacu pada prinsip ketat kami: 'No Approval, No Code. No Evidence, No Completion', seluruh fitur Tahap 08 telah melalui verifikasi pengujian otomatis:*
  >
  > *1. **Test Suite Mandiri Tahap 08 (`test_marketplace.php`):** 10 dari 10 Definition of Done LULUS 100%. Mencakup validasi hak akses, verifikasi harga jual kamera, auto-fill profil, validasi alamat kurir, presisi formula total belanja, transaksi ACID, pencatatan item pesanan, penahanan dana escrow, hingga otorisasi invoice.*
  > *2. **Live Foreground Chrome Testing:** Kami menguji alur secara langsung di peramban Google Chrome via DevTools: membuka detail Sony A7 III, mengklik beli unit, memeriksa auto-fill form checkout, submit pesanan, melihat nota resmi #BUY-00002, dan memverifikasi tab 'Jual Beli Saya' pada dashboard pelanggan.*
  > *3. **Uji Regresi Kumulatif Seluruh Tahap:** 9 test suite platform dijalankan serentak (Tahap 01 s.d Tahap 08 + Browser E2E). Hasilnya: **91 dari 91 skenario pengujian LULUS 100% (91/91 PASS)**.*
  >
  > *Dengan demikian, Tahap 08 Marketplace Jual Beli resmi kami nyatakan SELESAI dengan sempurna. Terima kasih atas perhatian Bapak/Ibu sekalian."*

---

# BAGIAN 3 — CHECKLIST VERIFIKASI SEBELUM SIDANG / PRESENTASI
- [x] Pastikan Apache dan MySQL di Laragon aktif.
- [x] Jalankan `php tests/test_marketplace.php` (Wajib 10/10 PASS).
- [x] Jalankan seluruh test suite platform (Wajib 91/91 PASS).
- [x] Buka Google Chrome di `http://localhost/camhub/public/produk/sony-a7-iii` dan demonstrasikan pembelian unit Sony A7 III seharga Rp 24.500.000.
- [x] Perlihatkan nota tanda terima `#BUY-XXXXX` dengan status rekening bersama Escrow.
- [x] Tunjukkan tab *Jual Beli Saya* pada dashboard pelanggan yang rapi ber-sidebar.



--------------------------------------------------------------------------------
<div id="tahap-09"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 09: SISTEM TITIP JUAL KAMERA & DASHBOARD ADMIN
# Berkas Sumber Asli: Docs/buku/09 Tahap 9.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 09: SISTEM TITIP JUAL KAMERA (CONSIGNMENT) & DASHBOARD ADMIN PRESISI

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner & Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI BUKU:** [`Docs/buku/09 Tahap 9.md`](09%20Tahap%209.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/09.implementation_plan_Tahap9.md`](../tahapan/09.implementation_plan_Tahap9.md)  
> **ACUAN PROTOTYPE RESMI:**  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Dashboard Admin.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Dashboard%20Admin.png)  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Dashboard Pelanggan.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Dashboard%20Pelanggan.png)  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Dashboard Mitra Lapak.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Dashboard%20Mitra%20Lapak.png)  
>  
> **TABEL TERKAIT:** `consignments`, `products`, `product_units`, `inspections`, `fines`, `payments`, `kyc_submissions`, `users`, `audit_logs`

---

# BAGIAN 1 — PANDUAN SETUP & PENGUJIAN FITUR TAHAP 09 (STEP-BY-STEP)

Panduan praktis bagi seluruh anggota Kelompok 1 untuk menjalankan, mendemokan, dan memverifikasi fitur Tahap 09 di laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **Apache** dan **MySQL** di Laragon dalam kondisi aktif (*running*).
- Basis data `camhub_db` telah memiliki tabel `consignments` aktif.
- Akun uji siap pakai:
  - `admin:admin` (Super Admin)
  - `user:user` (Pelanggan)
  - `mitra:mitra` (Toko Mitra Lapak)

---

### 2. Langkah Menjalankan di Laptop Masing-Masing

#### Langkah 1: Pindah ke Branch Tahap 09
```bash
git checkout tahap/09-sistem-titip-jual
```

#### Langkah 2: Jalankan Pengujian Otomatis Mandiri (100% PASS Gate)
```bash
php tests/test_consignment.php
```
*Hasil yang diharapkan:*
```text
========================================================
   CAMHUB TEST SUITE — TAHAP 09: SISTEM TITIP JUAL & ADMIN
========================================================
 [PASS] TEST-CNS-01: Akses Dashboard Admin Baru & Struktur Sidebar
 [PASS] TEST-CNS-02: Integritas 4 Kartu Metrik Admin (2x2 Grid)
 [PASS] TEST-CNS-03: Visualisasi Bar Chart 7 Hari Terakhir
 [PASS] TEST-CNS-04: Daftar Toko Mitra untuk Formulir Titip Jual
 [PASS] TEST-CNS-05: Validasi Data Server-Side Titip Jual
 [PASS] TEST-CNS-06: Perekaman Pengajuan Konsinyasi ke Database
 [PASS] TEST-CNS-07: Pelacakan Status & Kalkulasi Payout Pelanggan
 [PASS] TEST-CNS-08: Inspeksi Fisik & Persetujuan Harga Mitra
 [PASS] TEST-CNS-09: Publikasi Otomatis ke Katalog Publik
 [PASS] TEST-CNS-10: Siklus Selesai Terjual & Tombol Logout Mitra
========================================================
HASIL PENGUJIAN TAHAP 09: 10/10 BERHASIL (100% PASS)
========================================================
```

---

### 3. Skenario Uji Visual di Browser

1. **Pengujian Dashboard Admin Baru (Presisi 100% Mockup):**
   - Buka browser ke: `http://localhost/camhub/public/login`
   - Login dengan kredensial: `admin` / `admin`.
   - Sistem langsung mengarahkan ke `http://localhost/camhub/public/dashboard/admin`.
   - **Perhatikan Tampilan:**
     - Sidebar gelap navy `#0c192c` di sisi kiri dengan brand `CamHub` dan 7 menu rapi.
     - 4 Kartu metrik 2x2 putih bersih: *Menunggu Verifikasi KYC*, *Unit Inspeksi*, *Transaksi Hari Ini*, dan *Denda Pending*.
     - Kartu grafik batang *Transaksi 7 Hari Terakhir* berwarna biru modern (`#2563eb`) dari hari `Sen` hingga `Min`.
     - Tombol *Keluar Sesi* yang terintegrasi aman di bawah sidebar.

2. **Pengujian Alur Titip Jual Pelanggan:**
   - Logout dari admin, login sebagai pelanggan: `user` / `user`.
   - Masuk ke menu sidebar **Titip Jual Saya** (`/titip-jual/saya`).
   - Klik tombol **+ Ajukan Titip Jual Baru** (`/titip-jual/ajukan`).
   - Isi nama kamera, pilih toko mitra tujuan, masukkan ekspektasi harga jual, dan deskripsi kondisi fisik.
   - Klik **Kirim Pengajuan Titip Jual** -> Muncul status pelacak *Menunggu Review Mitra* dengan stepper visual.

3. **Pengujian Inspeksi & Kesepakatan Toko Mitra:**
   - Logout, login sebagai mitra lapak: `mitra` / `mitra`.
   - Buka menu **Kelola Titip Jual** (`/dashboard/mitra/titip-jual`).
   - Masukkan harga kesepakatan final, komisi toko 10%, lalu klik **✓ Lolos Inspeksi & Setujui Harga**.
   - Setelah disetujui, klik **📢 Terbitkan ke Katalog Publik**.
   - Buka Katalog Publik (`/katalog`): Unit kamera titip jual otomatis muncul dengan badge khusus **🏷️ Titip Jual**.

---

# BAGIAN 2 — SCRIPT PRESENTASI KELOMPOK 1 (ROLEPLAY)

Skenario pembagian bicara anggota Kelompok 1 untuk presentasi dosen / evaluasi proyek:

### 1. Yogi Ario Pratama (Project Owner & Lead Developer)
> *"Selamat pagi Bapak/Ibu dosen dan rekan-rekan. Pada Tahap 09 ini, kami berfokus pada dua pencapaian krusial: pertama, perbaikan total tampilan Dashboard Admin agar 100% presisi dengan prototype resmi, dan kedua, peluncuran pilar ketiga bisnis CamHub: Sistem Titip Jual Kamera atau Konsinyasi.*  
> *Sebelumnya antarmuka admin masih menggunakan draf lama. Sekarang, Anda dapat melihat di layar bahwa layout telah berubah 100% menjadi sidebar gelap navy dengan 4 kartu metrik presisi dan visualisasi grafik batang 7 hari terakhir yang interaktif."*

### 2. Muhammad Regieyanto (Backend & System Architect)
> *"Dari sisi arsitektur data dan backend, kami merancang siklus hidup konsinyasi yang komprehensif pada tabel `consignments`. Siklus ini mencakup tahapan `submitted`, `inspected`, `approved`, `listed`, hingga `sold`.*  
> *Ketika toko mitra menyetujui unit yang dititipkan, sistem secara otomatis mengeksekusi transaksi ACID untuk menerbitkan kamera ke tabel `products` lengkap dengan flag `is_consignment = 1` dan mengalkulasikan bagi hasil komisi 10% secara transparan bagi pemilik kamera."*

### 3. Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
> *"Dari aspek keamanan dan perutean sistem, seluruh endpoint admin terisolasi ketat di `AdminDashboardController` dengan perlindungan RBAC guard `role:admin`. Mitra dan pelanggan tidak dapat menembus rute satu sama lain.*  
> *Seluruh form mutasi—mulai dari pengajuan barang, inspeksi mitra, penerbitan katalog, hingga tombol logout yang kami tambahkan pada dashboard mitra—terlindungi penuh dari serangan CSRF dan manipulasi status IDOR."*

### 4. Krisna Mukti (Quality Assurance & Presentation Coordinator)
> *"Dari sisi pengujian kualitas, modul Tahap 09 telah diuji melalui berkas `tests/test_consignment.php` dengan hasil 10 dari 10 skenario lulus 100%.*  
> *Dengan tambahan 10 tes ini, total pengujian otomatis platform CamHub kini mencapai 101 skenario pengujian dengan status 100% PASS tanpa regresi. Seluruh bukti verifikasi, diagram alur, dan kode sumber telah tersinkronisasi rapi pada branch resmi `tahap/09-sistem-titip-jual`."*



--------------------------------------------------------------------------------
<div id="tahap-10"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 10: INTEGRASI REKENING BERSAMA ESCROW & PENARIKAN DANA MITRA
# Berkas Sumber Asli: Docs/buku/10 Tahap 10.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN PENGUJIAN & DOKUMENTASI RESMI TAHAP 10
## INTEGRASI PEMBAYARAN, REKENING BERSAMA (ESCROW GATEWAY), & PENCAIRAN SALDO MITRA
### Platform Marketplace & Rental Kamera CamHub

> **Kredit Tim Pengembang (Kelompok 1):**  
> 1. **Yogi Ario Pratama** — *Project Owner & Lead Developer*  
> 2. **Muhammad Regieyanto** — *Backend & System Architect*  
> 3. **Ivan Farrel Nanda Kristanto** — *Core Routing & Security Engineer*  
> 4. **Krisna Mukti** — *Quality Assurance & Presentation Coordinator*  
>  
> **Tanggal Rilis:** 04 November 2026  
> **Target Status Mutu:** **100% LULUS (111 / 111 PENGUJIAN OTOMATIS PLATFORM)**  
> **Branch Git:** `tahap/10-pembayaran-escrow` -> `develop`

---

## 1. Ringkasan Eksekutif Tahap 10

Tahap 10 menandai tonggak krusial dalam siklus hidup finansial platform **CamHub**. Pada tahap ini, platform menyelesaikan seluruh siklus transaksi moneter dengan menerapkan prinsip *Zero-Trust Financial Gateway* melalui Rekening Bersama (Escrow):
1. **Penyelesaian Cacat Visual Dashboard Mitra:** Menghapus seluruh komentar HTML yang sebelumnya bocor (`-->`, `PERSIS MOCKUP`, `3 KARTU METRIK`, `SEKSI PRODUK SAYA`, `KONTEN UTAMA`) serta merapikan duplikasi menu `Kelola Titip Jual` sehingga tepat berjumlah 1.
2. **Siklus Hidup Lengkap Rekening Bersama (Escrow Lifecycle):**
   - **`held`**: Dana pelanggan terkunci di rekening bersama saat pembayaran sewa atau pembelian kamera diverifikasi.
   - **`released`**: Pelanggan menekan tombol konfirmasi *"Selesai Sewa"* atau *"Terima Barang"*, memicu pelepasan dana secara instan ke saldo toko mitra lapak.
   - **`refunded`**: Transaksi yang dibatalkan mengembalikan dana penuh ke pelanggan tanpa memotong saldo mitra.
   - **`disputed`**: Pelanggan yang mengajukan komplain atas kondisi barang mengunci status menjadi sengketa, menangguhkan pelepasan dana hingga peninjauan admin selesai.
3. **Dompet & Pencairan Dana Mitra (`/dashboard/mitra/saldo`):**
   - Menghitung secara *real-time* saldo tersedia (siap ditarik), saldo tertahan di rekening bersama, dan total dana yang pernah ditarik.
   - Formulir penarikan dana ke rekening bank mitra (BCA, Mandiri, BRI, BNI, BSI) dengan validasi minimum Rp 50.000 dan proteksi anti-overdraft.
   - Tabel riwayat penarikan dan mutasi dana masuk rekening bersama secara transparan.
4. **Perekaman Audit Trail Finansial ACID:** Setiap mutasi finansial (`ESCROW_RELEASE`, `ESCROW_REFUND`, `ESCROW_DISPUTE`) dicatat pada tabel `audit_logs` dengan dukungan rollback transaksi basis data MySQL InnoDB.

---

## 2. Arsitektur Komponen & Basis Data

### A. Tabel Finansial Terkait
- **`escrow_transactions`**: Menyimpan relasi transaksi (`booking_id`, `sales_order_id`, `payment_id`), nominal yang ditahan (`amount_held`), status (`held`, `released`, `refunded`, `disputed`), serta stempel waktu (`held_at`, `released_at`).
- **`partner_withdrawals`**: Mencatat pengajuan penarikan dana toko mitra lapak ke bank (`partner_id`, `amount`, `bank_name`, `account_number`, `account_holder`, `status`, `created_at`).
- **`payments`**: Mencatat rincian metode pembayaran, kode referensi, status pembayaran (`pending`, `success`, `failed`), dan `paid_at`.
- **`audit_logs`**: Mencatat rekam jejak setiap aksi finansial pengguna secara permanen.

### B. Daftar Modul Backend
1. **`App\Repositories\EscrowRepository`**:
   - `getEscrowByBookingId(int $bookingId)`: Mengambil data escrow untuk sewa.
   - `getEscrowByOrderId(int $orderId)`: Mengambil data escrow untuk pembelian.
   - `releaseEscrow(int $escrowId)`: Memperbarui status menjadi `released`.
   - `refundEscrow(int $escrowId, string $notes)`: Memperbarui status menjadi `refunded`.
   - `disputeEscrow(int $escrowId, string $reason)`: Memperbarui status menjadi `disputed`.
   - `getPartnerBalanceMetrics(int $partnerId)`: Menghitung saldo tersedia vs saldo tertahan toko mitra.
   - `createWithdrawal(...)`: Menyimpan pengajuan penarikan dana ke database.
   - `getPartnerEscrowTransactions(int $partnerId)`: Mengambil mutasi riwayat dana masuk escrow.
2. **`App\Services\EscrowService`**:
   - Melindungi integritas transaksi dengan `$db->beginTransaction()` dan `$db->rollBack()`.
   - Memastikan otorisasi kepemilikan pesanan sebelum pelepasan dana dapat dieksekusi.
3. **`App\Controllers\EscrowController`**:
   - Menangani endpoint POST pelanggan untuk pelepasan dana sewa, beli, dan pengajuan komplain sengketa.
4. **`App\Controllers\PartnerWalletController`**:
   - Menampilkan antarmuka saldo lapak dan menangani formulir pengajuan penarikan dana mitra.

---

## 3. Matriks Hasil Pengujian Mutu (Definition of Done)

Pengujian dijalankan melalui [`tests/test_escrow_payment.php`](file:///c:/laragon/www/camhub/tests/test_escrow_payment.php):

| Kode Uji | Skenario Pengujian | Hasil | Keterangan Verifikasi |
|:---:|:---|:---:|:---|
| `TEST-ESC-01` | Pembersihan Cacat Visual Dashboard Mitra | **PASS** | Tag komentar `-->` bocor tereliminasi bersih, menu `Kelola Titip Jual` tepat berjumlah 1. |
| `TEST-ESC-02` | Integritas Record Escrow pada Booking Sewa | **PASS** | Booking sewa tersambung ke rekening bersama dengan status `held` dan nominal valid. |
| `TEST-ESC-03` | Integritas Record Escrow pada Order Jual Beli | **PASS** | Order jual beli mengunci dana pada `escrow_transactions` dengan isolasi `sales_order_id`. |
| `TEST-ESC-04` | Simulasi Callback Pembayaran Sukses | **PASS** | Callback mengubah status pembayaran menjadi `success` dan mencatat stempel waktu `paid_at`. |
| `TEST-ESC-05` | Konfirmasi Pelanggan & Pelepasan Dana (Release) | **PASS** | Pelanggan klik konfirmasi selesai -> status escrow berubah `released` & booking `completed`. |
| `TEST-ESC-06` | Kalkulasi Saldo Tersedia Toko Mitra | **PASS** | Saldo siap ditarik mitra bertambah secara akurat sesuai nilai transaksi yang di-release. |
| `TEST-ESC-07` | Mekanisme Pembatalan & Pengembalian Dana (Refund) | **PASS** | Transaksi dibatalkan -> status escrow berubah `refunded` dan dana tidak cair ke saldo mitra. |
| `TEST-ESC-08` | Mekanisme Pengajuan Sengketa (Dispute) | **PASS** | Pelanggan komplain -> status escrow terkunci `disputed`, pelepasan dana langsung diblokir. |
| `TEST-ESC-09` | Validasi Formulir Penarikan Saldo Mitra | **PASS** | Menolak nominal melebihi saldo, menolak < Rp 50.000, menolak bank kosong, dan sukses mencatat ID penarikan. |
| `TEST-ESC-10` | Perekaman Audit Trail Finansial & Transaksi ACID | **PASS** | Mutasi finansial tercatat aman di `audit_logs` dan dilindungi rollback basis data. |

**Status Akumulatif Pengujian Platform CamHub:**
- 10 Berkas Test Suite: **111 / 111 LULUS (100% SUCCESS, 0 GAGAL / 0 ERROR)**

---

## 4. Panduan Demonstrasi Sidang & Simulasi Peran Tim

Berikut adalah naskah roleplay resmi saat demonstrasi di hadapan Dosen Penguji / Asisten Praktikum:

### **Pemeran 1: Yogi Ario Pratama (Project Owner & Lead Developer)**
> *"Selamat pagi/siang Bapak/Ibu Dosen Penguji. Pada Tahap 10 ini, kami fokus mengintegrasikan gerbang penyelesaian transaksi moneter dan Rekening Bersama Escrow. Pertama, kami telah menindaklanjuti perbaikan tampilan Dashboard Mitra: seluruh teks komentar visual yang sebelumnya bocor telah dibersihkan 100%, dan navigasi 'Kelola Titip Jual' telah kami konsolidasikan menjadi satu entri yang rapi. Mari kita saksikan perubahannya di peramban secara langsung."*

### **Pemeran 2: Muhammad Regieyanto (Backend & System Architect)**
> *"Dari sisi arsitektur backend, kami merancang `EscrowService` dengan prinsip Zero-Trust Financial Protection. Dana dari pelanggan tidak pernah langsung disalurkan ke toko sebelum transaksi dinyatakan tuntas. Ketika pelanggan menyewa atau membeli kamera, dana ditahan di status `held`. Hanya ketika pelanggan mengonfirmasi penerimaan barang atau selesainya masa sewa di `/pesanan-saya`, dana dilepaskan menjadi `released` dan langsung menambah 'Saldo Tersedia' toko mitra lapak secara matematis presisi."*

### **Pemeran 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)**
> *"Untuk aspek keamanan dan proteksi transaksi, rute pelepasan dana (`/pesanan/sewa/{id}/konfirmasi-selesai` dan `/pesanan/beli/{id}/konfirmasi-terima`) dilindungi oleh pemeriksaan Anti-IDOR: hanya pelanggan pemilik pesanan sah yang diizinkan mengonfirmasi. Selain itu, jika pelanggan menerima unit yang tidak sesuai spesifikasi atau rusak, mereka dapat menekan tombol komplain sengketa sehingga dana otomatis berstatus `disputed` dan seluruh proses pencairan dibekukan secara aman. Seluruh peristiwa ini diaudit ke tabel `audit_logs`."*

### **Pemeran 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)**
> *"Seluruh skenario telah kami buktikan dengan rangkaian pengujian otomatis. Hari ini kami menjalankan 10 test suite komprehensif mulai dari Fondasi, Database, Autentikasi, KYC, Katalog, Dashboard Mitra, Booking, Marketplace, Titip Jual, hingga Integrasi Escrow Tahap 10. Hasilnya adalah 111 dari 111 pengujian dinyatakan 100% PASS tanpa error. Platform CamHub siap melangkah ke Tahap 11: Dashboard Super Administrator."*

---

## 5. Cara Menjalankan Uji Otomatis

Buka terminal PowerShell pada direktori root proyek (`c:\laragon\www\camhub`):
```powershell
# Jalankan pengujian mandiri Tahap 10
php tests/test_escrow_payment.php

# Jalankan pengujian browser end-to-end
php tests/test_browser_e2e.php

# Jalankan seluruh rangkaian uji akumulatif platform (111 Tests)
php tests/test_foundation.php; php tests/test_database.php; php tests/test_auth.php; php tests/test_kyc.php; php tests/test_catalog.php; php tests/test_partner_dashboard.php; php tests/test_booking.php; php tests/test_marketplace.php; php tests/test_consignment.php; php tests/test_escrow_payment.php
```



--------------------------------------------------------------------------------
<div id="tahap-11"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 11: PUSAT KENDALI SUPER ADMINISTRATOR & AUDIT TRAIL
# Berkas Sumber Asli: Docs/buku/11 Tahap 11.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN PENGUJIAN & DOKUMENTASI RESMI TAHAP 11
## DASHBOARD SUPER ADMINISTRATOR (PUSAT KENDALI PENGGUNA, VERIFIKASI KYC DOKUMEN, MODERASI SENGKETA ESCROW, & AUDIT KEAMANAN PLATFORM)
### Platform Marketplace & Rental Kamera CamHub

> **Kredit Tim Pengembang (Kelompok 1):**  
> 1. **Yogi Ario Pratama** — *Project Owner & Lead Developer*  
> 2. **Muhammad Regieyanto** — *Backend & System Architect*  
> 3. **Ivan Farrel Nanda Kristanto** — *Core Routing & Security Engineer*  
> 4. **Krisna Mukti** — *Quality Assurance & Presentation Coordinator*  
>  
> **Tanggal Rilis:** 04 November 2026  
> **Target Status Mutu:** **100% LULUS (121 / 121 PENGUJIAN OTOMATIS PLATFORM)**  
> **Branch Git:** `tahap/11-dashboard-administrator` -> `develop`

---

## 1. Ringkasan Eksekutif Tahap 11

Tahap 11 menghadirkan pusat komando tertinggi sistem (**Super Administrator Control Center**) untuk platform CamHub. Seluruh alur moderasi platform, kepatuhan identitas pengguna, pengawasan unit fisik kamera, arbitrasi finansial rekening bersama, hingga penegakan sanksi denda operasional kini dikendalikan secara tersentralisasi, aman, dan transparan:

1. **Penyelarasan Tampilan 100% Mengacu Mockup Resmi:**
   Antarmuka dashboard admin dirombak menyeluruh mengacu pada prototype [`template/CamHub — Interactive UI UX Prototype/CamHub — Dashboard Admin.png`](file:///c:/laragon/www/camhub/template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Dashboard%20Admin.png):
   - Sidebar vertikal navy gelap (`#0c192c`) dengan logo CamHub, badge pengguna, navigasi modul dinamis, dan tombol keluar sesi terisolasi.
   - 4 Kartu Metrik Ringkas: **Menunggu Verifikasi KYC**, **Unit Inspeksi**, **Transaksi Hari Ini**, dan **Denda Pending**.
   - Grafik batang visual **Transaksi 7 Hari Terakhir** berwarna biru `#2563eb`.

2. **Pusat Verifikasi Dokumen KYC (`/dashboard/admin/kyc`):**
   - Peninjauan berkas KTP dan foto selfie wajah pengguna secara visual dengan zoom modal.
   - Filter tab status: *Semua Pengajuan*, *Menunggu Verifikasi*, *Disetujui*, dan *Ditolak*.
   - Aksi 1-klik Persetujuan (*Approve*) yang mengaktifkan hak sewa pelanggan secara instan (`kyc_verified = 1`).
   - Aksi Penolakan (*Reject*) dengan alasan penolakan wajib untuk panduan perbaikan pengguna.

3. **Pusat Transaksi, Arbitrasi Sengketa Escrow, & Pencairan Dana Mitra (`/dashboard/admin/transaksi`):**
   - **Moderasi Sengketa Escrow (Disputed Resolution):** Admin memiliki wewenang mengakhiri status sengketa melalui 2 keputusan adil:
     - `force-release`: Melepaskan dana tertahan langsung ke saldo toko mitra jika komplain tidak terbukti.
     - `force-refund`: Mengembalikan dana penuh ke rekening pelanggan jika terbukti terjadi kelalaian mitra lapak.
   - **Persetujuan Penarikan Dana Mitra (Withdrawals):** Admin dapat menyetujui (*processed*) transfer bank mitra atau menolak (*rejected*) permohonan yang tidak valid.
   - **Tabel Transaksi Menyeluruh:** Memantau seluruh transaksi sewa dan jual beli lintas toko di platform CamHub.

4. **Pengawasan Jadwal Unit Kamera Lintas Mitra (`/dashboard/admin/jadwal`):**
   - Mengawasi seluruh nomor seri fisik kamera (`serial_number`), nama toko mitra pemilik, dan status operasional (`available`, `rented`, `maintenance`).
   - Menampilkan kalender tanggal sewa aktif untuk mencegah manipulasi ketersediaan unit.

5. **Manajemen Penegakan Denda Operasional (`/dashboard/admin/denda`):**
   - Penerbitan denda resmi pelanggaran operasional: Keterlambatan Pengembalian (`late_return`), Kerusakan Fisik Unit (`damage`), dan Aksesori Hilang (`missing_accessory`).
   - Pelunasan status denda dari `pending` menjadi `paid` dengan pencatatan nominal denda.

6. **Pusat Pengaturan Pengguna & Jejak Audit Keamanan (`/dashboard/admin/pengaturan`):**
   - Pengelolaan status aktif/suspend seluruh akun pengguna platform (Pelanggan, Mitra, Admin) dengan proteksi anti-lockout akun Root Admin (ID: 1).
   - Penayangan 50 baris riwayat terkini aktivitas sistem pada tabel `audit_logs` secara transparan.

---

## 2. Arsitektur Komponen & Basis Data

### A. Tabel Terkait
- **`kyc_submissions`**: Menyimpan status dokumen (`pending`, `approved`, `rejected`), waktu verifikasi, dan catatan penolakan.
- **`escrow_transactions`**: Status penahanan dana (`held`, `released`, `refunded`, `disputed`).
- **`partner_withdrawals`**: Pengajuan pencairan saldo toko mitra lapak.
- **`product_units`**: Unit fisik kamera dengan nomor seri unik dan status ketersediaan.
- **`fines`**: Rekam denda keterlambatan dan kerusakan fisik unit.
- **`users`**: Tabel pengguna dengan kolom kontrol status (`is_active` / suspend).
- **`audit_logs`**: Log audit keamanan platform yang merekam seluruh aksi administratif.

### B. Daftar Modul Backend
1. **`App\Repositories\AdminRepository`**:
   - `getPendingKycSubmissions()`: Mengambil antrean verifikasi KTP.
   - `getAllKycSubmissions(string $status)`: Mengambil daftar pengajuan KYC terfilter.
   - `approveKyc(int $submissionId, int $adminId)`: Menyetujui KYC dan mengaktifkan `kyc_verified` akun.
   - `rejectKyc(int $submissionId, int $adminId, string $reason)`: Menolak pengajuan KYC.
   - `getAllTransactions()`: Mengambil ringkasan transaksi sewa dan jual beli.
   - `getDisputedEscrows()`: Mengambil sengketa escrow aktif yang membutuhkan arbitrasi.
   - `getPendingWithdrawals()`: Mengambil permohonan pencairan saldo mitra.
   - `resolveDispute(int $escrowId, string $action, int $adminId)`: Arbitrasi sengketa escrow.
   - `processWithdrawal(int $withdrawalId, string $status, int $adminId)`: Pemrosesan pencairan dana.
   - `getAllUnitSchedules()`: Mengambil ketersediaan nomor seri unit kamera.
   - `getAllFines()`: Mengambil rekapitulasi denda operasional platform.
   - `createFine(...)`: Menerbitkan denda baru ke pengguna.
   - `settleFine(int $fineId)`: Melunasi denda pending.
   - `getAllUsers()`: Mengambil daftar akun pengguna sistem.
   - `toggleUserStatus(int $userId)`: Mengubah status aktif / suspended akun.
   - `getAuditLogs(int $limit)`: Mengambil rekaman log audit keamanan sistem.

2. **`App\Services\AdminService`**:
   - Menjamin integritas transaksi dengan `$db->beginTransaction()` dan rollback atomik.
   - Mencegah penonaktifan akun Root Administrator ID 1.
   - Mencatat setiap tindakan administratif ke `audit_logs` secara otomatis.

3. **`App\Controllers\AdminDashboardController`**:
   - `index()`: Dashboard metrik utama & chart transaksi.
   - `kyc()`, `approveKyc()`, `rejectKyc()`: Manajemen verifikasi dokumen KYC.
   - `transactions()`, `resolveDispute()`, `processWithdrawal()`: Manajemen transaksi & escrow.
   - `schedule()`: Pengawasan jadwal dan nomor seri unit kamera.
   - `fines()`, `storeFine()`, `payFine()`: Manajemen denda operasional.
   - `settings()`, `toggleUserStatus()`: Manajemen akun & log audit sistem.

---

## 3. Matriks Hasil Pengujian Mutu (Definition of Done)

Pengujian dijalankan melalui [`tests/test_admin_suite.php`](file:///c:/laragon/www/camhub/tests/test_admin_suite.php):

| Kode Uji | Skenario Pengujian | Hasil | Keterangan Verifikasi |
|:---:|:---|:---:|:---|
| `TEST-ADM-01` | Proteksi Akses RBAC Super Admin | **PASS** | Akses dibatasi ketat untuk role `admin`, non-admin dialihkan ke beranda publik. |
| `TEST-ADM-02` | Agregasi Metrik Dashboard Admin (4 Kartu & Chart) | **PASS** | Metrik KYC, unit inspeksi, transaksi hari ini, dan denda teragregasi presisi. |
| `TEST-ADM-03` | Peninjauan Antrean Verifikasi Dokumen KYC | **PASS** | Pengajuan KTP dan selfie termuat lengkap dengan dokumen pendukung. |
| `TEST-ADM-04` | Aksi Persetujuan KYC (Approval) & Sinkronisasi Profil | **PASS** | Status KYC menjadi `approved`, `users.kyc_verified` otomatis aktif bernilai 1. |
| `TEST-ADM-05` | Aksi Penolakan KYC (Rejection) dengan Alasan | **PASS** | Status KYC menjadi `rejected` dan alasan tersimpan untuk instruksi perbaikan. |
| `TEST-ADM-06` | Arbitrasi Sengketa Escrow (Force-Release ke Mitra) | **PASS** | Sengketa terselesaikan, dana escrow cair ke saldo mitra, status menjadi `released`. |
| `TEST-ADM-07` | Arbitrasi Sengketa Escrow (Force-Refund ke Pelanggan) | **PASS** | Sengketa terselesaikan, dana escrow berstatus `refunded`, tidak memotong saldo mitra. |
| `TEST-ADM-08` | Pengawasan Nomor Seri Unit Fisik & Jadwal Sewa | **PASS** | Daftar unit kamera, nomor seri fisik, nama mitra, dan jadwal booking termuat rapi. |
| `TEST-ADM-09` | Penerbitan & Pelunasan Denda Operasional (Fines) | **PASS** | Penerbitan denda `late_return` sukses tercatat dan dapat dilunasi menjadi `paid`. |
| `TEST-ADM-10` | Manajemen Status Pengguna & Jejak Audit Keamanan | **PASS** | Pengguna dapat di-suspend/aktifkan (Root Admin terproteksi), `audit_logs` merekam aksi. |

**Status Akumulatif Pengujian Platform CamHub:**
- 11 Berkas Test Suite Platform: **121 / 121 LULUS (100% SUCCESS, 0 GAGAL / 0 ERROR)**

---

## 4. Panduan Demonstrasi Sidang & Simulasi Peran Tim

Berikut adalah naskah presentasi resmi saat sidang pengujian di hadapan Dosen Penguji / Asisten Praktikum:

### **Pemeran 1: Yogi Ario Pratama (Project Owner & Lead Developer)**
> *"Selamat pagi/siang Bapak/Ibu Dosen Penguji. Pada Tahap 11 ini, kami menyelesaikan modul Pusat Kendali Super Administrator (Super Admin Command Center) untuk platform CamHub. Seluruh antarmuka admin telah kami bangun ulang 100% presisi mengacu pada template resmi `CamHub — Dashboard Admin.png`. Tampilan kini memiliki Sidebar Navy gelap, 4 kartu ringkasan operasional harian, serta grafik batang transaksi 7 hari terakhir. Di sini Super Admin memegang kendali penuh atas verifikasi dokumen, integritas transaksi, jadwal unit sewa, denda, dan audit keamanan platform."*

### **Pemeran 2: Muhammad Regieyanto (Backend & System Architect)**
> *"Dari sudut pandang arsitektur backend, kami memperluas `AdminRepository` dan `AdminService` untuk mengelola seluruh domain platform secara atomik. Salah satu fitur paling krusial adalah Arbitrasi Sengketa Rekening Bersama Escrow. Apabila terjadi komplain antara pelanggan dan mitra lapak, Super Admin memiliki instrumen 'Force-Release' untuk mencairkan dana ke toko mitra, atau 'Force-Refund' untuk mengembalikan dana penuh ke rekening pelanggan. Seluruh perubahan status dieksekusi dengan isolasi transaksi ACID MySQL InnoDB sehingga tidak mungkin terjadi selisih saldo atau mutasi silang."*

### **Pemeran 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)**
> *"Dari aspek keamanan dan penegakan regulasi sistem, seluruh rute `/dashboard/admin/*` diproteksi secara berlapis oleh middleware `auth` dan `role:admin`. Pada modul verifikasi KYC, admin dapat menyetujui dokumen identitas pengguna dalam satu klik yang langsung menyinkronkan status `kyc_verified` akun pelanggan, atau menolaknya dengan menyertakan alasan. Kami juga menyematkan proteksi anti-lockout pada manajemen pengguna: akun Root Admin ID 1 tidak dapat di-suspend dalam kondisi apa pun. Setiap tindakan pengawasan dan sanksi operasional direkam secara otomatis pada tabel `audit_logs`."*

### **Pemeran 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)**
> *"Untuk membuktikan integritas seluruh sistem, kami mengeksekusi test suite mandiri `test_admin_suite.php` yang mencakup 10 skenario pengujian Definition of Done dan seluruhnya dinyatakan 100% PASS. Selain itu, seluruh 11 rangkaian pengujian otomatis platform dari Tahap 01 hingga Tahap 11 berhasil lulus dengan skor sempurna: **121 dari 121 tes sukses (100% PASS, 0 Gagal, 0 Error)**. Platform CamHub kini telah stabil dan siap melangkah ke Tahap 12: Integrasi Komprehensif Antar Modul, Optimasi Performa, & Uji Stabilitas E2E."*

---



--------------------------------------------------------------------------------
<div id="tahap-12"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 12: IN-APP NOTIFICATION HUB & PELAPORAN FINANSIAL RESMI
# Berkas Sumber Asli: Docs/buku/12 Tahap 12.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN PENGUJIAN & DOKUMENTASI RESMI TAHAP 12
## SISTEM NOTIFIKASI DALAM APLIKASI & PELAPORAN FINANSIAL PLATFORM (PUSAT NOTIFIKASI, REKAPITULASI OMZET, EKSPOR CSV, & CETAK NOTA RESMI TRANSAKSI)
### Platform Marketplace & Rental Kamera CamHub

> **Kredit Tim Pengembang (Kelompok 1):**  
> 1. **Yogi Ario Pratama** — *Project Owner & Lead Developer*  
> 2. **Muhammad Regieyanto** — *Backend & System Architect*  
> 3. **Ivan Farrel Nanda Kristanto** — *Core Routing & Security Engineer*  
> 4. **Krisna Mukti** — *Quality Assurance & Presentation Coordinator*  
>  
> **Tanggal Rilis:** 04 November 2026  
> **Target Status Mutu:** **100% LULUS (131 / 131 PENGUJIAN OTOMATIS PLATFORM)**  
> **Branch Git:** `tahap/12-notifikasi-pelaporan` -> `develop`

---

## 1. Ringkasan Eksekutif Tahap 12

Tahap 12 menghadirkan dua pilar transparansi dan komunikasi kunci pada ekosistem CamHub: **Pusat Notifikasi Dalam Aplikasi (In-App Notification Hub)** dan **Sistem Pelaporan Finansial Komprehensif Platform (Financial Reporting & Invoice System)**.

### Fitur-Fitur Utama yang Diselesaikan:
1. **Pusat Notifikasi Interaktif Pelanggan (`/notifikasi`):**
   - Layanan pengiriman notifikasi instan saat terjadi kejadian penting sistem: verifikasi KYC disetujui/ditolak, pesanan sewa/beli baru dibuat, dana escrow dirilis/di-refund, denda diterbitkan, dan pengajuan titip jual diinspeksi.
   - Penandaan notifikasi telah dibaca (*mark as read*) secara individual maupun serentak (*mark all as read*).
   - Pengalihan langsung (*action URL*) ke halaman transaksi atau dokumen terkait.
   - Endpoint API JSON unread count (`/api/notifikasi/unread-count`) untuk sinkronisasi antarmuka pengguna tanpa refresh.

2. **Pusat Laporan Finansial & Omzet Administrator (`/dashboard/admin/laporan`):**
   - 4 Kartu Metrik Kas Utama: **Total Perputaran Kas (GTV)**, **Pendapatan Bersih Platform (Net Revenue)**, **Total Transaksi Berjalan**, dan **Total Pencairan Saldo Mitra**.
   - Analisis pendapatan bersih dari 3 sumber resmi:
     1. Biaya administrasi rekening bersama escrow Rp 5.000 per transaksi aktif.
     2. Bagi hasil komisi titip jual konsinyasi (10% dari harga deal kamera).
     3. Penerimaan denda pelanggaran operasional yang telah dilunasi.
   - Pemfilteran dinamis berdasarkan rentang waktu: **Hari Ini (`today`)**, **7 Hari Terakhir (`7days`)**, **Bulan Ini (`month`)**, dan **Rentang Kustom (`custom`)**.
   - Distribusi omzet penjualan dan penyewaan per kategori kamera (DSLR, Mirrorless, Lensa, Aksesori, Drone).
   - Tabel riwayat transaksi lengkap dengan rincian biaya platform dan status rekening bersama.

3. **Mesin Ekspor Rekapitulasi CSV / Excel:**
   - Generator CSV berstandar streaming dengan UTF-8 BOM (`\xEF\xBB\xBF`) agar aksen karakter dan pemformatan rupiah terbaca sempurna di Microsoft Excel.
   - Ekspor Laporan Finansial Admin (`/dashboard/admin/laporan/export`).
   - Ekspor Rekapitulasi Mutasi Saldo & Transaksi Mitra Lapak (`/dashboard/mitra/saldo/export`).

4. **Halaman Cetak Nota & Invoice Resmi Transaksi (Print-to-PDF):**
   - **Nota Resmi Penyewaan Kamera (`/pesanan/sewa/{id}/cetak`):** Memuat nomor nota resmi `#RENT-XXXXX`, rincian unit kamera dan nomor seri fisik (`serial_number`), periode tanggal dan durasi sewa, informasi toko mitra lapak penyedia, serta kartu jaminan perlindungan rekber escrow.
   - **Invoice Resmi Pembelian Kamera (`/pesanan/beli/{id}/cetak`):** Memuat kode transaksi `#BUY-XXXXX`, alamat tujuan ekspedisi kurir, rincian biaya kamera, ongkos kirim, dan fee rekber escrow.
   - Dilengkapi CSS cetak mandiri `@media print` untuk menghilangkan tombol navigasi saat mencetak langsung ke kertas atau menyimpannya sebagai berkas PDF resmi.
   - Dilindungi mekanisme isolasi IDOR (*Insecure Direct Object Reference*): Pelanggan hanya dapat mencetak dokumen miliknya sendiri, sementara Super Administrator memiliki wewenang mencetak seluruh nota transaksi platform.

---

## 2. Arsitektur Komponen & Basis Data

### A. Tabel Basis Data
1. **`notifications`**:
   - `id` (INT, Primary Key)
   - `user_id` (INT, Foreign Key -> `users.id`)
   - `title` (VARCHAR 150)
   - `message` (TEXT)
   - `is_read` (TINYINT 1, Default 0)
   - `action_url` (VARCHAR 255, Nullable)
   - `created_at` (DATETIME)

2. **Tabel Finansial yang Diagregasi:**
   - `rental_bookings` & `sales_orders` (Omzet kotor transaksi GTV & fee rekber)
   - `consignments` (Bagi hasil komisi 10% unit titip jual yang laku)
   - `fines` (Penerimaan kas dari denda terlambat/kerusakan berstatus `paid`)
   - `partner_withdrawals` (Arus kas keluar pencairan dana saldo mitra lapak)

### B. Daftar Kelas & Repositori Baru
- **Model:** [`app/Models/Notification.php`](file:///c:/laragon/www/camhub/app/Models/Notification.php)
- **Repositori:**
  - [`app/Repositories/NotificationRepository.php`](file:///c:/laragon/www/camhub/app/Repositories/NotificationRepository.php)
  - [`app/Repositories/ReportRepository.php`](file:///c:/laragon/www/camhub/app/Repositories/ReportRepository.php)
- **Layanan:**
  - [`app/Services/NotificationService.php`](file:///c:/laragon/www/camhub/app/Services/NotificationService.php)
  - [`app/Services/ReportService.php`](file:///c:/laragon/www/camhub/app/Services/ReportService.php)
- **Pengontrol:**
  - [`app/Controllers/NotificationController.php`](file:///c:/laragon/www/camhub/app/Controllers/NotificationController.php)
  - Diperbarui: [`app/Controllers/AdminDashboardController.php`](file:///c:/laragon/www/camhub/app/Controllers/AdminDashboardController.php) (`reports`, `exportReport`)
  - Diperbarui: [`app/Controllers/PartnerWalletController.php`](file:///c:/laragon/www/camhub/app/Controllers/PartnerWalletController.php) (`export`)
  - Diperbarui: [`app/Controllers/BookingController.php`](file:///c:/laragon/www/camhub/app/Controllers/BookingController.php) (`printReceipt`)
  - Diperbarui: [`app/Controllers/SalesController.php`](file:///c:/laragon/www/camhub/app/Controllers/SalesController.php) (`printInvoice`)

---

## 3. Matriks Pengujian Mutu (10 Definition of Done)

Berikut adalah hasil eksekusi pengujian otomatis pada berkas [`tests/test_report_notifications.php`](file:///c:/laragon/www/camhub/tests/test_report_notifications.php):

| Kode Pengujian | Kriteria Pengujian Mutu (Definition of Done) | Hasil | Keterangan Verifikasi |
|---|---|:---:|---|
| **TEST-REP-01** | Model & Repositori Notifikasi Terdaftar | **PASS** | Entitas notifikasi tersimpan dan fungsi `getUnreadCount` menghitung badge secara akurat. |
| **TEST-REP-02** | Pembuatan Notifikasi Otomatis pada Aksi Transaksi & Operasional | **PASS** | Dispatcher memicu notifikasi otomatis saat KYC disetujui dan saat unit titip jual lolos inspeksi. |
| **TEST-REP-03** | Aksi Penandaan Notifikasi Dibaca (`markAsRead` & `markAllAsRead`) | **PASS** | Status `is_read` berubah menjadi 1 dan unread count disetel menjadi 0 secara instan. |
| **TEST-REP-04** | Proteksi Hak Akses RBAC Modul Laporan Admin | **PASS** | Role non-admin diblokir dengan kode HTTP 403, sedangkan Super Admin diberikan akses penuh. |
| **TEST-REP-05** | Kalkulasi Akurat Metrik Kas Platform (Gross GTV & Net Revenue) | **PASS** | GTV terhitung Rp 267.420.000 dan Net Platform Revenue terakumulasi Rp 13.670.000. |
| **TEST-REP-06** | Pemfilteran Laporan Finansial Berdasarkan Rentang Waktu | **PASS** | Filter `today`, `7days`, `month`, dan rentang tanggal `custom` berhasil mengeksekusi parameter SQL terisolasi. |
| **TEST-REP-07** | Distribusi Omzet Transaksi per Kategori Kamera | **PASS** | Agregasi omzet berhasil dikelompokkan ke 5 kategori kamera secara menurun (*descending*). |
| **TEST-REP-08** | Ekspor Rekapitulasi Laporan Finansial Admin ke Format CSV | **PASS** | Berkas CSV admin tergenerate lengkap dengan UTF-8 BOM, judul laporan, metrik kas, dan header kolom transaksi. |
| **TEST-REP-09** | Ekspor Mutasi Saldo & Transaksi Mitra ke Format CSV | **PASS** | Berkas CSV mutasi escrow toko mitra lapak tergenerate rapi beserta status dan nominal dana. |
| **TEST-REP-10** | Halaman Cetak Nota Resmi Transaksi & Proteksi Anti-IDOR | **PASS** | Template cetak nota sewa dan invoice beli siap pakai dengan proteksi Anti-IDOR lintas pengguna. |

**Total Skor Uji Tahap 12: 10 / 10 LULUS (100% SUKSES)**  
**Total Skor Regresi Seluruh Sistem: 131 / 131 LULUS (100% SUKSES)**

---

## 4. Naskah Roleplay Presentasi Sidang Tim (Kelompok 1)

Berikut panduan dialog pembagian peran tim Kelompok 1 untuk mempresentasikan hasil Tahap 12 di hadapan dosen penguji / stakeholder:

```
[SCENE: Presentasi Progres Pengembangan CamHub Tahap 12]

Yogi Ario Pratama (Project Owner & Lead Developer):
"Selamat pagi Bapak/Ibu Dosen Penguji dan rekan-rekan sekalian.
Saya Yogi Ario Pratama bersama rekan-rekan Kelompok 1 hadir hari ini untuk
mempresentasikan pencapaian Tahap 12 pada platform CamHub.
Pada tahap ini, fokus utama kami adalah transparansi finansial dan kepastian
informasi pengguna. Kami telah menuntaskan sistem notifikasi otomatis terintegrasi
dan modul pelaporan keuangan super administrator yang dilengkapi fitur cetak nota
serta ekspor CSV resmi.
Arsitektur teknis backend akan dijelaskan langsung oleh rekan saya, Regie."

Muhammad Regieyanto (Backend & System Architect):
"Terima kasih Yogi. Dari sudut pandang backend arsitektur, kami mengimplementasikan
NotificationService dan ReportService yang beroperasi di atas pola murni MVC native.
ReportRepository mengakumulasi metrik finansial secara presisi: perputaran kas kotor (GTV),
biaya administrasi rekening bersama escrow Rp 5.000 per transaksi, bagi hasil 10% komisi
titip jual kamera bekas, serta penerimaan kas dari denda operasional.
Seluruh data dapat difilter dinamis berdasarkan hari ini, 7 hari, bulanan, maupun rentang
tanggal kustom. Selain itu, generator CSV kami menyematkan UTF-8 BOM agar kompatibel
100% saat dibuka di Microsoft Excel tanpa masalah karakter.
Mengenai proteksi keamanan dan otorisasi dokumen, Ivan akan memaparkannya."

Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer):
"Terima kasih Mas Regie. Dari sisi keamanan dan routing, seluruh rute laporan admin
dilindungi Middleware Role-Based Access Control (RBAC). Pengguna dengan role pelanggan
atau mitra lapak yang mencoba mengakses endpoint /dashboard/admin/laporan secara otomatis
diblokir dengan halaman 403 Forbidden.
Pada modul cetak nota sewa dan invoice pembelian, kami menerapkan proteksi Anti-IDOR ketat.
Pelanggan hanya diizinkan mencetak dokumen pesanannya sendiri. Jika ID pesanan dimanipulasi
di URL browser, sistem menolak akses tersebut dan mengembalikan pengguna ke dashboard aman.
Selanjutnya, hasil validasi mutu pengujian akan dilaporkan oleh Krisna."

Krisna Mukti (Quality Assurance & Presentation Coordinator):
"Terima kasih Mas Ivan. Sebagai penanggung jawab pengujian mutu platform, kami telah
menjalankan pengujian otomatis end-to-end melalui berkas test_report_notifications.php
dan seluruh 10 skenario Definition of Done lulus 100%.
Kami juga menjalankan uji regresi menyeluruh pada seluruh 13 rangkaian tes dari Tahap 1
hingga Tahap 12, dan hasilnya adalah 131 dari 131 pengujian berstatus HIJAU (PASS),
dengan 0 error dan 0 warning. Seluruh modul notifikasi, laporan keuangan, ekspor CSV,
dan nota cetak telah terverifikasi sempurna.
Saya kembalikan kepada Yogi untuk kesimpulan."

Yogi Ario Pratama (Project Owner & Lead Developer):
"Terima kasih tim. Dengan selesainya Tahap 12, platform CamHub kini telah mencapai
92% penyelesaian sistem. Platform ini siap memasuki tahap finalisasi Tahap 13 untuk
persiapan deployment produksi dan presentasi akhir. Terima kasih atas perhatiannya."
```

---
*Dokumen ini diterbitkan secara resmi pada repositori CamHub oleh Kelompok 1.*



--------------------------------------------------------------------------------
<div id="tahap-13"></div>

# ==============================================================================
# 📖 BATASAN: TAHAP 13: UJI KONKURENSI PESSIMISTIC LOCKING & NASKAH SIDANG AKHIR
# Berkas Sumber Asli: Docs/buku/13 Tahap 13.md
# ==============================================================================
--------------------------------------------------------------------------------


# BUKU PANDUAN PENGUJIAN & DOKUMENTASI RESMI TAHAP 13
## FINAL HARDENING, PENGUJIAN BEBAN (STRESS TEST), AUDIT KEAMANAN PRODUKSI & KESIAPAN PENERAPAN SIDANG AKHIR
### Platform Marketplace & Rental Kamera CamHub (Pure Native PHP MVC)

> **Kredit Tim Pengembang (Kelompok 1):**  
> 1. **Yogi Ario Pratama** — *Project Owner & Lead Developer*  
> 2. **Muhammad Regieyanto** — *Backend & System Architect*  
> 3. **Ivan Farrel Nanda Kristanto** — *Core Routing & Security Engineer*  
> 4. **Krisna Mukti** — *Quality Assurance & Presentation Coordinator*  
>  
> **Status Akhir Proyek:** **100% SELESAI (13 DARI 13 TAHAPAN TUNTAS)**  
> **Hasil Uji Mutu Akhir:** **141 / 141 PENGUJIAN OTOMATIS PLATFORM LULUS (100% PASS, 0 ERROR, 0 WARNING)**  
> **Tanggal Rilis Final:** 04 November 2026  
> **Branch Git:** `tahap/13-pengujian-penerapan` -> `develop`

---

## 1. Ringkasan Eksekutif Tahap 13 (Pencapaian Pamungkas)

Tahap 13 merupakan gerbang akhir penyempurnaan sistem CamHub sebelum disidangkan dan dirilis ke lingkungan produksi. Fokus utama tahap ini adalah membuktikan keandalan platform di bawah beban nyata, menutup celah keamanan, memastikan kepatuhan ACID transaksi keuangan, dan menyiapkan panduan penerapan menyeluruh:

1. **Injeksi HTTP Security Headers Global:**
   Front controller [`public/index.php`](file:///c:/laragon/www/camhub/public/index.php) menyematkan header keamanan esensial:
   - `X-Frame-Options: SAMEORIGIN` (mencegah pembajakan klik / Clickjacking).
   - `X-Content-Type-Options: nosniff` (mencegah eksploitasi MIME-type confusion).
   - `X-XSS-Protection: 1; mode=block` (mencegah reflected Cross-Site Scripting).
   - `Referrer-Policy: strict-origin-when-cross-origin`.
   - `X-Permitted-Cross-Domain-Policies: none`.

2. **Proteksi Anti-Brute Force & Rate Limiting:**
   Pembangunan mekanisme pembatasan frekuensi percobaan masuk (`rate_limit_check`, `rate_limit_increment`, `rate_limit_clear`) pada [`app/Controllers/AuthController.php`](file:///c:/laragon/www/camhub/app/Controllers/AuthController.php). Percobaan login gagal lebih dari 5 kali secara otomatis dibekukan selama 60 detik dengan informasi *retry-after* yang ramah pengguna.

3. **Pengamanan Direktori Unggahan Berkas:**
   Penerapan berkas konfigurasi [`public/uploads/.htaccess`](file:///c:/laragon/www/camhub/public/uploads/.htaccess) yang secara mutlak menonaktifkan mesin eksekusi PHP pada folder dokumen KTP, foto selfie KYC, dan foto produk kamera untuk menangkal serangan *Remote Code Execution (RCE)*.

4. **Uji Beban Konkurensi & Pessimistic Locking (*SELECT ... FOR UPDATE*):**
   Pengujian simulasi 5 transaksi sewa konkuren yang menargetkan unit fisik kamera yang sama pada rentang tanggal bertubrukan (*overlapping dates*). Hasil pengujian membuktikan bahwa **hanya 1 transaksi pertama yang berhasil committed**, sementara 4 transaksi lainnya dicegah secara aman (*gracefully rejected*) oleh mekanisme locking di level basis data MySQL InnoDB, menjamin **0% risiko race-condition dan double-booking**.

5. **Audit Keutuhan Relasi Data & Konsistensi Kas:**
   - Pemeriksaan ketiadaan data yatim (*orphaned records*) pada seluruh 23 tabel relasional basis data menghasilkan **0 anomali**.
   - Formula saldo dompet mitra lapak terbukti seimbang dan akurat:  
     $$\text{Available Balance} = \max\left(0, (\text{Released Rental} + \text{Released Sales} + \text{Base Seed}) - \text{Total Withdrawn}\right)$$

6. **Pencapaian Master Regression Suite (141 / 141 PASS):**
   Seluruh 14 test suite platform (dari Tahap 1 hingga Tahap 13) dieksekusi melalui [`tests/run_all.php`](file:///c:/laragon/www/camhub/tests/run_all.php) dan membukukan rekor kelulusan **100% HIJAU (141 LULUS DARI 141 PENGUJIAN)**.

---

## 2. Matriks Pengujian Mutu Tahap 13 (10 Definition of Done)

Berkas pengujian: [`tests/test_hardening_production.php`](file:///c:/laragon/www/camhub/tests/test_hardening_production.php)

| Kode DoD | Kriteria Pengujian Mutu Tahap 13 | Hasil | Keterangan Verifikasi |
|---|---|:---:|---|
| **TEST-HARD-01** | Kesiapan Konfigurasi Lingkungan (`.env` & `.env.example`) | **PASS** | Parameter koneksi database DSN, nama database, dan mode debug sinkron sempurna. |
| **TEST-HARD-02** | Injeksi HTTP Security Headers Global | **PASS** | Header `SAMEORIGIN`, `nosniff`, `Referrer-Policy`, dan `XSS-Protection` aktif live di server. |
| **TEST-HARD-03** | Proteksi Folder Unggahan Berkas (`.htaccess`) | **PASS** | Eksekusi file berekstensi skrip (`.php`, `.phtml`, dll.) diblokir di `public/uploads/`. |
| **TEST-HARD-04** | Proteksi Rate Limiting & Anti-Brute Force | **PASS** | Percobaan login gagal 3 kali berturut-turut diblokir sementara dengan countdown detik. |
| **TEST-HARD-05** | Uji Beban Konkurensi & Pessimistic Locking | **PASS** | 5 request simultan: 1 sukses terdaftar, 4 bentrok berhasil dicegah tanpa korupsi data. |
| **TEST-HARD-06** | Audit Keutuhan Relasi Database & Ketiadaan Data Yatim | **PASS** | Nol data yatim (*0 orphaned rows*) ditemukan pada tabel sewa, pesanan beli, escrow, dan unit. |
| **TEST-HARD-07** | Integritas Pembukuan Dompet Kas Mitra Lapak | **PASS** | Saldo tersedia (Rp 7.950.000) dan saldo tertahan rekber aman konsisten non-negatif. |
| **TEST-HARD-08** | Penanganan Galat Produksi & Proteksi Informasi Rahasia | **PASS** | Tampilan kustom 404 dan 500 ramah pengguna, bebas dari kebocoran password hash atau DSN. |
| **TEST-HARD-09** | Kesiapan Skrip Instalasi 1-Klik | **PASS** | `database/schema.sql`, `migrate.php`, dan `initial_seed.php` tervalidasi siap pakai. |
| **TEST-HARD-10** | Master Regression Suite Platform | **PASS** | Seluruh 13 suite pengujian tahap sebelumnya terintegrasi utuh dan lulus 100%. |

---

## 3. Matriks Rekapitulasi Rangkaian Uji Seluruh Platform (Grand Total 14 Suite)

```
========================================================
   MASTER REGRESSION SUITE: 14 SUITE PENGUJIAN CAMHUB   
========================================================

 1.  test_foundation.php              :  7 /  7 PASS (100%)
 2.  test_database.php                :  7 /  7 PASS (100%)
 3.  test_auth.php                    : 10 / 10 PASS (100%)
 4.  test_kyc.php                     : 10 / 10 PASS (100%)
 5.  test_catalog.php                 : 10 / 10 PASS (100%)
 6.  test_partner_dashboard.php       : 10 / 10 PASS (100%)
 7.  test_booking.php                 : 10 / 10 PASS (100%)
 8.  test_marketplace.php             : 10 / 10 PASS (100%)
 9.  test_consignment.php             : 10 / 10 PASS (100%)
 10. test_escrow_payment.php          : 10 / 10 PASS (100%)
 11. test_admin_suite.php             : 10 / 10 PASS (100%)
 12. test_browser_e2e.php             : 17 / 17 PASS (100%)
 13. test_report_notifications.php    : 10 / 10 PASS (100%)
 14. test_hardening_production.php    : 10 / 10 PASS (100%)

========================================================
HASIL AKHIR: 141 / 141 TESTS PASSED (14 / 14 SUITES OK)
STATUS     : SEMUA 141 PENGUJIAN 100% SUKSES HIJAU
========================================================
```

---

## 4. Naskah Roleplay Presentasi Sidang Akhir (Kelompok 1)

Berikut adalah panduan skrip pembagian peran komprehensif bagi 4 anggota Kelompok 1 untuk mempresentasikan proyek akhir CamHub di hadapan dosen penguji dan audiens akademik:

```
[SCENE: Sidang Tugas Akhir / Evaluasi Proyek Perangkat Lunak CamHub]

Yogi Ario Pratama (Project Owner & Lead Developer):
"Selamat pagi / siang kepada Bapak/Ibu Dosen Penguji dan rekan-rekan sekalian.
Nama saya Yogi Ario Pratama, selaku Project Owner dan Lead Developer dari Kelompok 1.
Hari ini, kami dengan bangga mempersembahkan 'CamHub' — Platform Marketplace dan Rental
Kamera Terintegrasi yang dibangun secara murni menggunakan arsitektur Native PHP MVC
tanpa ketergantungan pada framework eksternal apa pun.

Proyek ini berangkat dari permasalahan nyata di industri kreatif, di mana penyewa kamera
sering mengalami bentrok jadwal unit fisik, kekhawatiran penipuan transaksi moneter,
serta minimnya transparansi pelaporan bagi pemilik lapak kamera.
Dalam 13 tahapan pengembangan yang terstruktur rapi, kami telah menyelesaikan 100% fitur
sistem, mulai dari manajemen inventaris serial number fisik, sistem rekening bersama (Escrow),
verifikasi identitas KYC, hingga dashboard terpadu bagi Pelanggan, Mitra Lapak, dan Super Admin.
Untuk membedah arsitektur backend dan keandalan data transaksi, saya serahkan kepada Regie."

Muhammad Regieyanto (Backend & System Architect):
"Terima kasih Yogi. Dari sudut pandang arsitektur sistem, CamHub mengadopsi prinsip
Separation of Concerns (SoC) dengan pola Model-View-Controller murni dan koneksi aman
PDO Singleton pada basis data MySQL InnoDB yang terdiri dari 23 tabel relasional.
Salah satu tantangan terberat dalam sistem rental adalah 'Double-Booking Problem' saat
banyak pengguna memesan unit kamera yang sama secara simultan.
Untuk mengatasinya, kami merancang mekanisme Pessimistic Locking menggunakan klausul
'SELECT ... FOR UPDATE' di dalam transaksi ACID basis data.

Pada pengujian beban di Tahap 13 ini, kami mensimulasikan 5 transaksi konkuren secara bersamaan
pada rentang tanggal yang bertubrukan. Hasilnya terbukti solid: tepat 1 transaksi berhasil di-commit,
sementara 4 transaksi lainnya dicegah secara aman tanpa ada korupsi data kas maupun inventaris.
Selain itu, perputaran dana diatur melalui siklus hidup Escrow yang transparan: dana ditahan (held),
dirilis ke saldo mitra setelah konfirmasi serah terima unit (released), atau dikembalikan penuh
jika terjadi pembatalan (refunded).
Selanjutnya, aspek keamanan siber, routing, dan proteksi berkas akan dijelaskan oleh Ivan."

Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer):
"Terima kasih Mas Regie. Dari pilar keamanan dan infrastruktur routing, CamHub menerapkan
prinsip 'Defense in Depth' di setiap lapisan:
Pertama, seluruh HTTP Request melewati pipeline Router native yang mengeksekusi middleware
secara berjenjang: GuestMiddleware, AuthMiddleware, dan RoleMiddleware (RBAC).
Pengguna biasa atau mitra yang mencoba membuka URL administratif dipastikan terblokir dengan
kode status 403 Forbidden.

Kedua, kami memperkuat keamanan peramban dengan menginjeksi 5 HTTP Security Headers secara global:
X-Frame-Options SAMEORIGIN untuk menangkal Clickjacking, nosniff untuk mencegah MIME-confusion,
dan Referrer-Policy.
Ketiga, pada jalur autentikasi, kami menerapkan Rate Limiter berbasis sesi dan IP yang membatasi
percobaan masuk maksimal 5 kali per menit guna menangkis serangan Brute Force Credential Stuffing.
Keempat, seluruh folder unggahan publik dilindungi konfigurasi .htaccess yang menonaktifkan
eksekusi skrip PHP secara total, mencegah celah Remote Code Execution (RCE).
Terakhir, modul nota sewa dan invoice kami lindungi dengan validasi Anti-IDOR (Insecure Direct
Object Reference) yang ketat.
Untuk pembuktian mutu dan verifikasi pengujian, Krisna akan memaparkan laporannya."

Krisna Mukti (Quality Assurance & Presentation Coordinator):
"Terima kasih Mas Ivan. Sebagai Quality Assurance Coordinator, komitmen kami di Kelompok 1
adalah menjamin standar mutu 'Zero Defect' dengan pendekatan Test-Driven Verification.
Setiap baris kode yang ditulis telah melewati skenario pengujian otomatis Definition of Done (DoD).

Hari ini, kami melaporkan bahwa seluruh 14 suite pengujian platform — mulai dari fondasi sistem,
skema database, autentikasi, KYC, booking sewa, jual beli marketplace, titip jual konsinyasi,
escrow gateway, dashboard super admin, pusat notifikasi, pelaporan finansial, hingga uji beban
konkurensi — telah dieksekusi melalui master test runner.
Hasil akhirnya adalah: 141 dari 141 pengujian berstatus HIJAU (100% LULUS) dengan 0 eror dan 0 peringatan.
Platform ini juga telah melalui pengujian antarmuka langsung secara visual di peramban Chrome,
memastikan keselarasan 100% terhadap prototype UI/UX acuan.
Saya kembalikan kepada Yogi untuk kesimpulan penutup."

Yogi Ario Pratama (Project Owner & Lead Developer):
"Terima kasih rekan-rekan. Dengan tuntasnya Tahap 13, platform CamHub telah mencapai
titik penyelesaian 100% dan sepenuhnya siap diterapkan pada lingkungan produksi (Production Ready).
Panduan deployment lengkap telah kami dokumentasikan pada Docs/DEPLOYMENT.md.
Kami mengucapkan terima kasih yang sebesar-besarnya kepada Bapak/Ibu Dosen Penguji atas
arahan dan bimbingannya selama proses pengembangan sistem ini.
Sekian presentasi dari Kelompok 1, kami membuka ruang seluas-luasnya untuk sesi tanya jawab."
```

---
*Dokumen ini diterbitkan oleh Kelompok 1 sebagai bukti resmi kelulusan seluruh tahapan pengerjaan proyek CamHub.*


--------------------------------------------------------------------------------
# AKHIR DOKUMEN BUKU PANDUAN PENGEMBANGAN SISTEM CAMHUB (TAHAP 00 — 13)
*Diterbitkan dan diverifikasi secara resmi oleh Kelompok 1 untuk Sidang Evaluasi Tugas Akhir.*
--------------------------------------------------------------------------------
