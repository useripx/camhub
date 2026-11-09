# Laporan Bug: Penyajian Raw JSON Mentah pada Rincian Data Mutasi Audit Log Admin

## 1. Identifikasi Masalah
- **ID Laporan:** BUG-008
- **Tanggal Ditemukan:** 05 November 2026
- **Tanggal Selesai:** 05 November 2026
- **Status:** RESOLVED (SELESAI DIVERIFIKASI)
- **Komponen Terdampak:**
  - `app/Helpers/functions.php` (`format_audit_changes()`)
  - `resources/views/admin/settings.php`
  - `resources/views/admin/transactions.php`
  - `resources/views/admin/reports.php`
- **Tingkat Keparahan:** Sedang (Visual UX Defect / Raw Technical Debug Data Exposure)

---

## 2. Deskripsi Masalah & Tangkapan Layar Pengguna
Pada halaman `/dashboard/admin/pengaturan` (tabel *Log Audit Keamanan & Finansial Terkini*), kolom **Rincian Data Mutasi** menampilkan string JSON teknis mentah yang terpotong titik-titik:
```
{"resolution":"release","notes":"Unit test forc...
{"booking_code":"#RENT-00050","product_unit_id"...
{"resolution":"refund","notes":"Unit test force...
{"role":"partner","user_agent":"CamHub-Automate...
```
Tampilan ini terlihat tidak wajar, tidak estetis, dan membingungkan administrator non-teknis karena menyerupai kebocoran dump debug basis data yang lupa diformat.

Selain itu, terdapat emotikon judul section (`👥`, `🛡️`, `⚖️`, `🏦`, `📊`) yang masih tersisa di beberapa view admin, tidak sesuai dengan kebijakan platform *Zero Icon*.

---

## 3. Akar Masalah
1. **Pencetakan Langsung Kolom `changes_json`:**
   Pada `resources/views/admin/settings.php`, data mutasi dicetak langsung tanpa parsing:
   `<?= htmlspecialchars($log['changes_json'] ?? '-') ?>` dengan styling `text-overflow: ellipsis; white-space: nowrap;`.
2. **Ketiadaan Formatter Khusus:**
   Sistem belum memiliki helper fungsi untuk mengonversi payload mutasi JSON audit log ke dalam representasi teks/badge manusiawi (*human-readable*).

---

## 4. Solusi & Implementasi
1. **Pembangunan Helper `format_audit_changes(?string $json, ?string $action = null)`:**
   - Dibuat di `app/Helpers/functions.php`.
   - Mengurai payload JSON secara aman (`json_decode`).
   - Menyediakan pemetaan human-readable per tipe aksi audit:
     - `DISPUTE_FORCE_RELEASE` / `DISPUTE_FORCE_REFUND`: Badge resolusi (Release ke Mitra / Refund ke Pelanggan), nominal, dan catatan resmi.
     - `RENTAL_BOOKING_CREATE`: Kode booking, nomor unit kamera, rentang tanggal sewa, dan total tagihan.
     - `SALES_ORDER_CREATE`: Nomor pesanan, total bayar, dan metode escrow.
     - `LOGIN_SUCCESS` / `LOGIN_FAILED`: Peran pengguna (Super Admin / Mitra / Pelanggan) dan tipe klien/browser.
     - `USER_STATUS_CHANGE`: Akun target dan mutasi status (misal `Pending → Aktif`).
     - `FINE_CREATE` / `FINE_PAID`: Jenis denda (Keterlambatan / Kerusakan / Aksesoris), nominal, dan alasan.
     - `KYC_APPROVE` / `KYC_REJECT` / `KYC_SUBMIT`: Keputusan status KYC, nama KTP, NIK, dan alasan penolakan.
     - `ESCROW_RELEASE` / `ESCROW_REFUND` / `ESCROW_DISPUTE`: Tipe tindakan escrow, nominal, dan alasan.
     - Fallback dinamis untuk payload kustom lainnya dengan label terformat rapi.
2. **Peningkatan Tampilan Antarmuka di `resources/views/admin/settings.php`:**
   - Mengganti pemanggilan raw text dengan `format_audit_changes($log['changes_json'], $log['action'])`.
   - Menambahkan CSS `.audit-chips-wrap` dan `.audit-chip` yang responsif terhadap Dark Mode dan Light Mode.
   - Menyematkan atribut `title="Payload JSON Asli: ..."` sehingga admin tetap dapat melihat string teknis aslinya saat mengarahkan kursor (hover).
   - Menghapus emotikon `👥` dan `🛡️` pada judul section serta merapatkan layout sidebar footer.
3. **Pembersihan Emotikon di View Admin Lain:**
   - `resources/views/admin/transactions.php`: Menghapus emotikon `⚖️`, `🏦`, dan `📊`.
   - `resources/views/admin/reports.php`: Menghapus emotikon `📊` pada tombol unduh rekap.

---

## 5. Hasil Verifikasi
- **Pengujian Browser (Chrome DevTools):**
  - Tabel Log Audit menampilkan badge terstruktur dan rapi (misal: *Resolusi: Release ke Mitra • Nominal: Rp 1.500.000 • Catatan: ...*).
  - Tampilan selaras sempurna baik dalam Dark Mode maupun Light Mode.
  - Tangkapan layar bukti: `Docs/error/image/evidence_audit_logs_human_readable.png`.
- **Pengujian Regresi:**
  - `php tests/run_all.php`: **147 / 147 PASS (100% SUKSES HIJAU)** pada 15 test suite.

