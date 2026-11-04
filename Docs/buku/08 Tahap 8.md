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
