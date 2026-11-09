# Laporan Bug: URL Mismatch Menu Laporan vs Pengaturan pada Sidebar Super Admin

**ID Bug:** `BUG-ADM-002`  
**Kategori:** Navigation Routing & UI Consistency  
**Tingkat Keparahan:** 🟡 Sedang (*Navigation Blocker*)  
**Status:** 🟢 **TERSELESAIKAN (*RESOLVED*)**  
**Tanggal Kejadian:** 04 November 2026, 18:55 WIB  
**Tanggal Perbaikan:** 04 November 2026, 19:02 WIB  
**Branch:** `fix`  
**Penanggung Jawab:** Kelompok 1 (Yogi Ario Pratama, Muhammad Regieyanto, Ivan Farrel Nanda Kristanto, Krisna Mukti)  

---

## 1. Ringkasan Masalah (Issue Summary)

Super Admin melaporkan bahwa saat menu **Laporan** pada sidebar kiri diklik, peramban justru membuka halaman **Pengaturan** (`/dashboard/admin/pengaturan`). Hal ini mengakibatkan menu Pengaturan dan Laporan mengarah ke URL yang sama, sehingga Super Admin tidak dapat mengakses halaman **Laporan Keuangan & Operasional** (`/dashboard/admin/laporan`) melalui navigasi sidebar.

---

## 2. Analisis Akar Masalah (Root Cause Analysis)

1. **Rute Backend Sebenarnya:**  
   Pada berkas [`routes/web.php`](file:///c:/laragon/www/camhub/routes/web.php#L69-L72), rute untuk laporan dan pengaturan sudah terdefinisi secara terpisah:
   - `/dashboard/admin/laporan` → `AdminDashboardController::reports()` (menampilkan view `admin.reports`)
   - `/dashboard/admin/pengaturan` → `AdminDashboardController::settings()` (menampilkan view `admin.settings`)
2. **Kesalahan Penulisan Atribut `href` di Sidebar Admin:**  
   Pada 6 berkas tampilan admin (`dashboard.php`, `fines.php`, `kyc.php`, `schedule.php`, `settings.php`, dan `transactions.php`), tautan menu Laporan secara tidak sengaja dituliskan sebagai:
   ```html
   <!-- KODE SALAH SEBELUMNYA -->
   <li><a href="<?= base_url('/dashboard/admin/pengaturan') ?>">Laporan</a></li>
   <li><a href="<?= base_url('/dashboard/admin/pengaturan') ?>">Pengaturan</a></li>
   ```
   Keduanya mengarah ke `/dashboard/admin/pengaturan`.

---

## 3. Langkah-Langkah Perbaikan (Fix Implementation)

Mengoreksi seluruh tautan menu **Laporan** pada 6 berkas tampilan admin agar merujuk ke rute yang benar:
```html
<!-- KODE BENAR SETELAH PERBAIKAN -->
<li><a href="<?= base_url('/dashboard/admin/laporan') ?>">Laporan</a></li>
<li><a href="<?= base_url('/dashboard/admin/pengaturan') ?>">Pengaturan</a></li>
```

Berkas yang diperbarui:
1. [`resources/views/admin/dashboard.php`](file:///c:/laragon/www/camhub/resources/views/admin/dashboard.php)
2. [`resources/views/admin/fines.php`](file:///c:/laragon/www/camhub/resources/views/admin/fines.php)
3. [`resources/views/admin/kyc.php`](file:///c:/laragon/www/camhub/resources/views/admin/kyc.php)
4. [`resources/views/admin/schedule.php`](file:///c:/laragon/www/camhub/resources/views/admin/schedule.php)
5. [`resources/views/admin/settings.php`](file:///c:/laragon/www/camhub/resources/views/admin/settings.php)
6. [`resources/views/admin/transactions.php`](file:///c:/laragon/www/camhub/resources/views/admin/transactions.php)

---

## 4. Hasil Verifikasi

1. **Uji Klik Navigasi di Browser Chrome DevTools:**
   - Navigasi ke `/dashboard/admin/pengaturan` → Menu "Laporan" diklik → Halaman berpindah ke `/dashboard/admin/laporan` (menampilkan ringkasan keuangan, omzet kas platform, perincian kategori, dan tombol unduh CSV).
   - Menu "Pengaturan" diklik → Halaman berpindah ke `/dashboard/admin/pengaturan` (menampilkan manajemen pengguna dan audit trail keamanan).
2. **Master Regression Suite:** Seluruh 147 pengujian pada 15 test suite tetap **100% SUKSES (HIJAU)**.

---

## 5. Log Keputusan Arsitektur
Pembaruan ini dicatat secara resmi pada [`Docs/Decision_Log.md`](file:///c:/laragon/www/camhub/Docs/Decision_Log.md) di bawah kode **`DEC-041`**.

