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

