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

