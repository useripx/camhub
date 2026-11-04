
# Laporan Bug: SQLSTATE[01000] Data Truncated for Column 'order_status'

**ID Bug:** `BUG-ADM-001`
**Kategori:** Database Schema & Backend Service Integrity
**Tingkat Keparahan:** 🔴 Kritis (*Critical Blocker*)
**Status:** 🟢 **TERSELESAIKAN (*RESOLVED*)**
**Tanggal Kejadian:** 04 November 2026, 17:49 WIB
**Tanggal Perbaikan:** 04 November 2026, 18:10 WIB
**Branch:** `fix`
**Penanggung Jawab:** Kelompok 1 (Yogi Ario Pratama, Muhammad Regieyanto, Ivan Farrel Nanda Kristanto, Krisna Mukti)

---

## 1. Ringkasan Masalah (Issue Summary)

Saat Super Admin melakukan moderasi sengketa transaksi rekening bersama (*Escrow Dispute Resolution*) untuk transaksi berjenis **Jual Beli Kamera** (`sales_orders`) pada halaman **Pusat Transaksi & Rekening Bersama Escrow** (`/dashboard/admin/transaksi`), penekanan tombol **`✓ Cairkan ke Mitra`** gagal diproses dan memunculkan kotak galat (*flash error*) berwarna merah.

### Pesan Galat:

```text
Gagal memproses resolusi sengketa: SQLSTATE[01000]: Warning: 1265 Data truncated for column 'order_status' at row 1
```

---

## 2. Bukti Visual (Evidence Screenshots)

### Sebelum Perbaikan (Kondisi Galat):

Tangkapan layar pada saat admin mengeksekusi resolusi sengketa pesanan `#BUY-00018` (Escrow ID `#84`):
![Galat Resolusi Sengketa](file:///C:/Users/yogia/.gemini/antigravity/brain/ec57eb36-033d-444e-ade3-8d0c90550b05/.user_uploaded/media_1793789424004_b447d419.jpg)

### Setelah Perbaikan (Kondisi Sukses Terverifikasi di Browser):

Tangkapan layar verifikasi langsung di peramban Chrome DevTools setelah perbaikan diterapkan:
![Sukses Resolusi Sengketa](file:///C:/Users/yogia/.gemini/antigravity/brain/ec57eb36-033d-444e-ade3-8d0c90550b05/.system_generated/steps/4205/media_0.png)
*(Tampil banner hijau: "Sengketa Escrow #104#104 diputus untuk dicairkan ke toko mitra." tanpa error)*

---

## 3. Analisis Akar Masalah (Root Cause Analysis)

1. **Titik Panggilan Kode:**
   Ketika tombol `✓ Cairkan ke Mitra` diklik, peramban mengirimkan request `POST /dashboard/admin/transaksi/sengketa/{id}/resolusi` dengan parameter `resolution = 'release'`.
2. **Eksekusi Logika Bisnis:**
   Metode `AdminService::resolveDispute()` menangani putusan ini:
   ```php
   // File: app/Services/AdminService.php (Baris 140-143)
   } elseif (!empty($escrow['sales_order_id'])) {
       $this->db->prepare("UPDATE sales_orders SET order_status = 'completed' WHERE id = :id")
                ->execute(['id' => $escrow['sales_order_id']]);
   }
   ```
3. **Penyebab Inkonsistensi (*Schema vs Code Mismatch*):**
   - Pada skema awal basis data MySQL (`database/schema.sql` baris 199), kolom `order_status` didefinisikan sebagai:
     ```sql
     `order_status` ENUM('pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'
     ```
   - Berbeda dengan `rental_bookings.rental_status` yang telah memiliki enum `'completed'`, tabel `sales_orders.order_status` **TIDAK MEMILIKI** nilai `'completed'`.
   - Karena koneksi PDO mengaktifkan mode strict (`ATTR_ERRMODE => ERRMODE_EXCEPTION`), peringatan MySQL *Warning 1265: Data truncated* dianggap sebagai *fatal exception*.
   - Blok `try-catch` menangkap *exception* ini, melakukan pembatalan transaksi (*rollback*), dan mengembalikan pesan galat ke antarmuka pengguna.

---

## 4. Langkah-Langkah Perbaikan (Fix Implementation)

### 1. Migrasi DDL Basis Data MySQL Live

Mengeksekusi pembaruan struktur kolom secara langsung pada tabel `sales_orders`:

```sql
ALTER TABLE `sales_orders` 
MODIFY COLUMN `order_status` 
ENUM('pending', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'cancelled') 
NOT NULL DEFAULT 'pending';
```

### 2. Sinkronisasi Skema Sumber (`database/schema.sql`)

Memperbarui berkas DDL master [`database/schema.sql`](file:///c:/laragon/www/camhub/database/schema.sql#L199) baris 199 agar setiap migrasi baru atau *fresh setup* memiliki definisi ENUM yang lengkap dan seragam.

### 3. Pembaruan Model Objek (`app/Models/SalesOrder.php`)

Menambahkan penanganan status `'completed'` pada metode lencana tampilan dan menyediakan helper method:

- Menambahkan `'completed' => '<span class="badge badge-success">Selesai</span>'` ke dalam `getStatusBadge()`.
- Menambahkan helper method `isCompleted()`, `isCancelled()`, dan `isPaid()`.

### 4. Pembuatan Pengujian Otomatis (`tests/test_fix_admin_dispute.php`)

Menyusun test suite mandiri yang menguji 6 skenario kritis:

- `TEST-FIX-01`: Validasi kolom `order_status` di MySQL memuat enum `'completed'`.
- `TEST-FIX-02`: Eksekusi `AdminService::resolveDispute()` opsi `release` berhasil tanpa galat.
- `TEST-FIX-03`: Status `sales_orders.order_status = 'completed'` dan `escrow_transactions.status = 'released'`.
- `TEST-FIX-04`: Eksekusi `AdminService::resolveDispute()` opsi `refund` berhasil tanpa galat.
- `TEST-FIX-05`: Status `sales_orders.order_status = 'cancelled'` dan `escrow_transactions.status = 'refunded'`.
- `TEST-FIX-06`: Pencatatan audit trail `DISPUTE_FORCE_RELEASE` dan `DISPUTE_FORCE_REFUND` di tabel `audit_logs`.

---

## 5. Hasil Pengujian & Verifikasi

### A. Pengujian Unit Mandiri (`php tests/test_fix_admin_dispute.php`):

```text
========================================================
   CAMHUB TEST SUITE — FIX: ADMIN DISPUTE RESOLUTION  
========================================================

 [PASS] TEST-FIX-01: Validasi Skema MySQL sales_orders.order_status
        -> Kolom order_status memuat nilai enum 'completed' (enum('pending','paid','processing','shipped','delivered','completed','cancelled')).
 [PASS] TEST-FIX-02: Eksekusi AdminService::resolveDispute Opsi 'release'
        -> Sengketa Escrow #89 diputus untuk dicairkan ke toko mitra.
 [PASS] TEST-FIX-03: Integritas Status Transaksi Setelah 'release'
        -> sales_orders.order_status = 'completed', escrow status = 'released'.
 [PASS] TEST-FIX-04: Eksekusi AdminService::resolveDispute Opsi 'refund'
        -> Sengketa Escrow #90 diputus untuk dikembalikan (refund) penuh ke pelanggan.
 [PASS] TEST-FIX-05: Integritas Status Transaksi Setelah 'refund'
        -> sales_orders.order_status = 'cancelled', escrow status = 'refunded'.
 [PASS] TEST-FIX-06: Pencatatan Audit Trail Resmi Super Admin
        -> Aksi DISPUTE_FORCE_RELEASE dan DISPUTE_FORCE_REFUND terekam di tabel audit_logs.

========================================================
HASIL PENGUJIAN: 6 / 6 BERHASIL (100% PASS)
========================================================
```

### B. Master Regression Suite (`php tests/run_all.php`):

```text
========================================================
   MASTER REGRESSION SUITE: 15 SUITE PENGUJIAN CAMHUB   
========================================================

 test_foundation.php              :  7 /  7 PASS (100%)
 test_database.php                :  7 /  7 PASS (100%)
 test_auth.php                    : 10 / 10 PASS (100%)
 test_kyc.php                     : 10 / 10 PASS (100%)
 test_catalog.php                 : 10 / 10 PASS (100%)
 test_partner_dashboard.php       : 10 / 10 PASS (100%)
 test_booking.php                 : 10 / 10 PASS (100%)
 test_marketplace.php             : 10 / 10 PASS (100%)
 test_consignment.php             : 10 / 10 PASS (100%)
 test_escrow_payment.php          : 10 / 10 PASS (100%)
 test_admin_suite.php             : 10 / 10 PASS (100%)
 test_browser_e2e.php             : 17 / 17 PASS (100%)
 test_report_notifications.php    : 10 / 10 PASS (100%)
 test_hardening_production.php    : 10 / 10 PASS (100%)
 test_fix_admin_dispute.php       :  6 /  6 PASS (100%)

========================================================
HASIL AKHIR: 147 / 147 TESTS PASSED (15 / 15 SUITES OK)
STATUS     : SEMUA 147 PENGUJIAN 100% SUKSES HIJAU
========================================================
```

### C. Verifikasi Interaktif Browser Chrome DevTools:

- **Halaman Diuji:** `http://localhost/camhub/public/dashboard/admin/transaksi`
- **Tindakan:** Mengklik tombol `✓ Cairkan ke Mitra` pada transaksi sengketa Escrow `#104`.
- **Hasil:** Transaksi sengketa berhasil diputuskan, status dana escrow dirilis ke toko mitra, dan banner notifikasi hijau berhasil muncul di layar tanpa pesan galat SQL.

---

## 6. Riwayat Keputusan Arsitektur

Pembaruan ini dicatat secara resmi pada [`Docs/Decision_Log.md`](file:///c:/laragon/www/camhub/Docs/Decision_Log.md) di bawah kode **`DEC-039`**.
