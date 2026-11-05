# Laporan Bug: Kontras Mode Gelap Beranda Tidak Terbaca & Mode Gelap Terang Tidak Berfungsi di Dashboard Admin

## 1. Identifikasi Masalah
- **ID Laporan:** BUG-006
- **Tanggal Ditemukan:** 05 November 2026
- **Tanggal Selesai:** 05 November 2026
- **Status:** RESOLVED (SELESAI DIVERIFIKASI)
- **Komponen Terdampak:**
  - `resources/views/public/home.php`
  - Seluruh view admin: `resources/views/admin/*.php` (`dashboard.php`, `fines.php`, `kyc.php`, `reports.php`, `schedule.php`, `settings.php`, `transactions.php`)
- **Tingkat Keparahan:** Tinggi (Keterbacaan Teks UI & Kegagalan Fitur Tema di Panel Admin)

---

## 2. Deskripsi Masalah & Akar Masalah
1. **Kontras Beranda Publik Rusak (SS 1):**
   - Variabel `--primary-navy: #ffffff;` pada Dark Mode menyebabkan background hero gradient menjadi putih-ke-biru terang. Karena teks hero berwarna putih (`#ffffff`), teks bertabrakan dengan background putih dan tidak terbaca.
   - Variabel `--accent-amber-light` bernilai opacity 15% sehingga badge transparan/hilang.
   - Body memanggil `--bg-light` yang undefined di Dark Mode sehingga background di bawah hero menjadi putih terang.
2. **Tema Gelap Terang Tidak Berfungsi di Admin (SS 3 & SS 4):**
   - Seluruh view admin tidak memiliki aturan styling CSS untuk `html[data-theme="dark"]`. Semua warna di-hardcode terang (`#f8fafc`, `#ffffff`).
   - Sidebar admin menggunakan layout lama dengan avatar `[A]`, tombol `Keluar Sesi`, dan footer terlempar ke bawah dengan `margin-top: auto`.

---

## 3. Resolusi Perbaikan
1. **Perbaikan Palette CSS Beranda Publik (`home.php`):**
   - Menetapkan `--hero-bg-from: #0b192e; --hero-bg-to: #15294a;` untuk gradient navy pekat yang elegan.
   - Menetapkan `--hero-title: #ffffff;` dan `--hero-subtitle: #cbd5e1;` untuk kontras teks tajam dan nyaman dibaca.
   - Menetapkan `--hero-badge-text: #f59e0b;` dengan border dan background amber yang tegas.
   - Menetapkan `--bg-page: #0b1320;` untuk seluruh halaman sehingga area di bawah hero serasi dalam Dark Mode.
   - Mengintegrasikan search box gelap (`#112240`) dengan input dark navy (`#0d1b30`).
2. **Penerapan Sistem Tema CSS pada Seluruh 7 View Admin:**
   - Menambahkan variabel `:root, html[data-theme="dark"]` dan `html[data-theme="light"]`.
   - Menyesuaikan `body`, `.admin-main`, `.section-card`, `.stat-card`, `.table-custom`, dan `.form-control` agar merespons perubahan mode secara dinamis dan mulus.
3. **Harmonisasi Sidebar Admin (Zero Icon & Modern Layout):**
   - Menghapus avatar lingkaran `[A]`.
   - Memindahkan identitas user ke bagian atas di bawah logo CamHub.
   - Menjadikan sidebar sticky: `position: sticky; top: 0; height: 100vh; overflow-y: auto;`.
   - Mengubah tombol logout menjadi `Keluar` (warna merah).
   - Menyematkan dynamic SVG icon (Matahari/Bulan) pada tombol switch tema di sidebar admin.

---

## 4. Hasil Verifikasi
- Master Regression Suite: **147 / 147 PASS (100% SUKSES HIJAU)** pada 15 suite pengujian.
- Pengujian browser nyata via Chrome DevTools:
  - Beranda publik: Teks hero, subtitle, dan badge berkontras tinggi dan terbaca sempurna di Dark Mode.
  - Panel admin (`/dashboard/admin/pengaturan`): Konten beralih instan antara Dark Mode (`#0b1320` & `#132644`) dan Light Mode (`#f8fafc` & `#ffffff`).
- Bukti Tangkapan Layar:
  - Beranda Dark Mode: `Docs/error/image/evidence_public_home_fixed_dark.png`
  - Admin Dark Mode: `Docs/error/image/evidence_admin_settings_dark.png`
  - Admin Light Mode: `Docs/error/image/evidence_admin_settings_light.png`
