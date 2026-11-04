# PANDUAN PENERAPAN & DEPLOYMENT RESMI (CAMHUB DEPLOYMENT GUIDE)
## Platform Marketplace & Rental Kamera Terintegrasi (Pure Native PHP MVC)

> **Tim Pengembang (Kelompok 1):**  
> 1. **Yogi Ario Pratama** — *Project Owner & Lead Developer*  
> 2. **Muhammad Regieyanto** — *Backend & System Architect*  
> 3. **Ivan Farrel Nanda Kristanto** — *Core Routing & Security Engineer*  
> 4. **Krisna Mukti** — *Quality Assurance & Presentation Coordinator*  
>  
> **Versi Rilis:** 1.0.0 (Production Ready)  
> **Tanggal Rilis:** 04 November 2026  
> **Status Verifikasi Mutu:** **141 / 141 PENGUJIAN OTOMATIS LULUS (100% PASS)**

---

## 1. Kebutuhan Sistem Minimum (System Requirements)

| Komponen | Spesifikasi Minimum | Rekomendasi Produksi |
|---|---|---|
| **Bahasa Pemrograman** | PHP 8.2.0 atau lebih baru | PHP 8.3 / 8.4 64-bit |
| **Ekstensi PHP Wajib** | `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `openssl`, `curl` | Diaktifkan pada `php.ini` |
| **Basis Data** | MySQL 8.0+ atau MariaDB 10.4+ | MySQL 8.0 InnoDB (`utf8mb4_unicode_ci`) |
| **Web Server** | Apache 2.4+ (`mod_rewrite` aktif) atau Nginx 1.20+ | Apache / Nginx reverse proxy dengan SSL/TLS |
| **Penyimpanan Berkas** | Ruang kosong min. 500 MB | SSD NVMe dengan partisi cadangan harian |

---

## 2. Instalasi Cepat pada Lingkungan Lokal (Windows Laragon / XAMPP)

### Langkah 1: Kloning Repositori
Tempatkan direktori proyek pada folder root web server:
```bash
cd C:\laragon\www
git clone https://github.com/useripx/camhub.git
cd camhub
```

### Langkah 2: Konfigurasi Berkas Lingkungan (`.env`)
Salin berkas template lingkungan `.env.example` menjadi `.env`:
```bash
copy .env.example .env
```
Pastikan kredensial koneksi database sesuai dengan Laragon/XAMPP lokal Anda:
```env
APP_NAME=CamHub
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/camhub/public
APP_TIMEZONE=Asia/Jakarta

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=camhub_db
DB_USER=root
DB_PASS=

SESSION_LIFETIME=7200
SESSION_SECURE=false
```

### Langkah 3: Eksekusi Migrasi Basis Data & Data Awal (Seeder)
Jalankan skrip migrasi native 1-klik untuk membangun seluruh 23 tabel relasional beserta foreign key dan indeks performa:
```bash
php database/migrate.php
```
Jalankan seeder untuk mengisi akun demo, 5 kategori kamera, 6 produk kamera etalase, serta inventaris unit fisik dengan nomor seri unik:
```bash
php database/seeders/initial_seed.php
```

### Langkah 4: Validasi Integritas Sistem (Master Regression Suite)
Jalankan seluruh 14 rangkaian pengujian otomatis untuk memverifikasi kesiapan operasional:
```bash
php tests/run_all.php
```
*Output yang diharapkan: **141 / 141 TESTS PASSED (14 / 14 SUITES OK) — SEMUA 141 PENGUJIAN 100% SUKSES HIJAU**.*

### Langkah 5: Akses Aplikasi
Buka peramban (*web browser*) dan arahkan ke alamat:
```
http://localhost/camhub/public/
```

---

## 3. Kredensial Akun Pengujian Sidang (Pre-Configured Demo Accounts)

Tersedia tombol akses cepat 1-klik pada halaman masuk ([`/login`](file:///c:/laragon/www/camhub/public/login)) atau gunakan kredensial berikut:

| Peran Akun | Username | Password | Hak Akses Utama |
|---|---|---|---|
| **Super Administrator** | `admin` | `admin` | Pusat kendali moderasi, verifikasi KYC KTP, resolusi sengketa escrow, audit log, & laporan finansial platform. |
| **Mitra Lapak (Yogi Camera)** | `mitra` | `mitra` | Kelola inventaris kamera, serial number fisik, jadwal sewa, dompet pencairan saldo, & kelola titip jual. |
| **Pelanggan Terverifikasi** | `user` | `user` | Pencarian katalog, booking sewa kamera, checkout marketplace jual beli, pengajuan KYC, & riwayat pesanan. |

---

## 4. Panduan Penerapan di Server Produksi (Linux Ubuntu / Nginx)

### A. Pengaturan Izin Direktori (Directory Permissions)
Pastikan folder `storage/` dan `public/uploads/` memiliki izin tulis untuk pengguna server web (`www-data`):
```bash
sudo chown -R www-data:www-data /var/www/camhub
sudo chmod -R 755 /var/www/camhub
sudo chmod -R 775 /var/www/camhub/storage
sudo chmod -R 775 /var/www/camhub/public/uploads
```

### B. Pengerasan Keamanan Berkas `.env` Produksi
Ubah parameter konfigurasi produksi di `/var/www/camhub/.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://camhub.id

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=camhub_db
DB_USER=camhub_user
DB_PASS=PasswordKuatBasisData2026!

SESSION_SECURE=true
```

### C. Konfigurasi Virtual Host Nginx
Buat berkas konfigurasi `/etc/nginx/sites-available/camhub`:
```nginx
server {
    listen 80;
    server_name camhub.id www.camhub.id;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name camhub.id www.camhub.id;

    root /var/www/camhub/public;
    index index.php index.html;

    ssl_certificate /etc/letsencrypt/live/camhub.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/camhub.id/privkey.pem;

    # Security Headers Tambahan Level Server
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Blokir akses berkas tersembunyi
    location ~ /\. {
        deny all;
    }

    # Blokir eksekusi skrip di dalam folder upload
    location ~ ^/uploads/.*\.php$ {
        deny all;
    }

    # Front Controller Rewrite
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM Processor
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```
Aktifkan konfigurasi dan muat ulang Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/camhub /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 5. Prosedur Pencadangan & Pemulihan Bencana (Backup & Recovery)

### Pencadangan Basis Data
```bash
mysqldump -u root -p camhub_db --single-transaction --quick --lock-tables=false > backup_camhub_$(date +%Y%m%d_%H%M%S).sql
```

### Pemulihan Basis Data
```bash
mysql -u root -p camhub_db < backup_camhub_20261104.sql
```

---
*Dokumen ini diterbitkan oleh Kelompok 1 sebagai panduan resmi deployment platform CamHub.*

