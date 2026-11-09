# Laporan Bug: Tombol Beralih Tema Tidak Ada di Navbar Beranda Publik & Label Profil Menampilkan "Dashboard (user)"

## 1. Identifikasi Masalah
- **ID Laporan:** BUG-005
- **Tanggal Ditemukan:** 04 November 2026
- **Tanggal Selesai:** 04 November 2026
- **Status:** RESOLVED (SELESAI DIVERIFIKASI)
- **Komponen Terdampak:**
  - `resources/views/public/home.php`
  - `resources/views/public/catalog.php`
  - `resources/views/public/product_detail.php`
  - `resources/views/welcome.php`
- **Tingkat Keparahan:** Sedang (Konsistensi Desain Global & Pengalaman Pengguna)

---

## 2. Deskripsi Bug & Analisis Tangkapan Layar (Screenshot)
Berdasarkan tangkapan layar `media_1793798159032_00a68095.png`:
1. **Absennya Pengalih Tema di Navbar Publik:**
   - Pada halaman Beranda publik (`/`), tidak ditemukan tombol peralihan tema (`Mode Terang` / `Mode Gelap`).
   - Sementara seluruh halaman dashboard telah dilengkapi tombol pengalih tema dengan preferensi default Dark Mode via `localStorage.getItem('camhub_theme')`.
2. **Label Profil Navbar "👤 Dashboard (user)":**
   - Di sisi kanan navbar, tombol profil bagi pengguna yang login menampilkan teks:
     `👤 Dashboard (user)`
   - Hal ini disebabkan kode template memanggil `auth()['username']` (bernilai `"user"` pada akun demo) dan menambahkan kurung secara kaku.
   - Sesuai kebijakan pengguna, ikon `👤` dihilangkan (*Zero Icon Policy*) dan tombol harus menampilkan nama lengkap pengguna (`auth()['full_name']`, contoh: `User Demo CamHub Updated`).
3. **Ikon Dinamis Khusus Tombol Tema:**
   - Pengguna mengizinkan dan meminta agar tombol tema dilengkapi ikon tema yang menyesuaikan kondisi terang/gelap:
     - Saat Dark Mode aktif: menampilkan ikon Matahari ☀️ + label `Mode Terang`.
     - Saat Light Mode aktif: menampilkan ikon Bulan 🌙 + label `Mode Gelap`.

---

## 3. Akar Masalah (Root Cause)
1. **Navbar Publik Statis:**
   - Header publik tidak memiliki pemanggilan state tema (`data-theme`) maupun script anti-flicker di `<head>`.
2. **Hardcoded Role/Username Binding:**
   - Teks tombol dashboard mengikat nilai mentah `auth()['username']` dengan string `Dashboard (user)`.
3. **Belum Ada Slot Kontrol Tema:**
   - Wadah `.nav-actions` tidak memiliki tombol switch tema sebelum tautan profil.

---

## 4. Solusi & Perbaikan yang Diterapkan
1. **Penambahan Tombol Switch Tema di Sebelah Kiri Profil:**
   - Menyematkan tombol `<button type="button" class="btn btn-theme-toggle" onclick="toggleCamHubTheme()" id="camhubThemeToggleBtn">` di `.nav-actions` tepat sebelum tombol profil.
   - Menghubungkan fungsi JavaScript `toggleCamHubTheme()` dan `syncThemeToggleUI(theme)` yang memanipulasi `document.documentElement.setAttribute('data-theme', ...)` dan menyimpan status ke `localStorage ('camhub_theme')`.
   - Menggunakan ikon SVG dinamis (Sun/Moon) yang berubah seketika saat tema dialihkan.
2. **Penyajian Nama Lengkap Pengguna (Murni Teks):**
   - Mengganti teks tombol profil menjadi `auth()['full_name'] ?? auth()['username'] ?? 'Akun Saya'`, sehingga menampilkan `User Demo CamHub Updated`.
   - Menghapus ikon `👤` sepenuhnya sesuai arahan *Zero Icon Policy*.
   - Menyediakan tautan dinamis yang mengarahkan ke dashboard peran pengguna (`/dashboard/pelanggan`, `/dashboard/mitra`, atau `/dashboard/admin`).
3. **Harmonisasi CSS Variabel Dark Mode (Default) & Light Mode:**
   - Menerapkan variabel `--bg-page`, `--bg-surface`, `--text-dark`, `--text-title`, `--border-color` pada `public/home.php`, `catalog.php`, `product_detail.php`, dan `welcome.php`.
4. **Anti-Flicker Script:**
   - Menyematkan inline script di `<head>` agar saat halaman publik dimuat, atribut `data-theme` langsung terpasang sebelum rendering DOM, mencegah kedipan warna (*flash of unstyled theme*).

---

## 5. Hasil Verifikasi & Validasi
1. **Uji Otomatis Master Regression Suite:**
   - Perintah: `php tests/run_all.php`
   - Hasil: **147 / 147 PASS (100% SUKSES HIJAU)** pada seluruh 15 suite pengujian.
2. **Uji Browser DevTools (`http://localhost/camhub/public/`):**
   - Mode Bawaan: `dataTheme = "dark"`, tombol bertuliskan `Mode Terang` dengan ikon SVG Matahari.
   - Label Profil: Menampilkan teks `User Demo CamHub Updated` tanpa kata `Dashboard (user)` dan tanpa emotikon `👤`. Tautan mengarah ke `/dashboard/pelanggan`.
   - Klik Toggle Tema: Beralih ke `dataTheme = "light"`, tombol berganti menjadi `Mode Gelap` dengan ikon SVG Bulan, background halaman berubah menjadi terang dengan kontras teks tajam.
   - Klik Toggle Kembali: Beralih mulus kembali ke `dataTheme = "dark"`.
   - Sinkronisasi Halaman: Buka halaman `/katalog`, tema tetap konsisten `dark` dengan tombol dan profil yang identik.
3. **Tangkapan Layar Bukti (Evidence):**
   - Mode Gelap: `Docs/error/image/evidence_public_home_dark_mode.png`
   - Mode Terang: `Docs/error/image/evidence_public_home_light_mode.png`
