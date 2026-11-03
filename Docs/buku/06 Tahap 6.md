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
