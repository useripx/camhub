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

