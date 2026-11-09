# Laporan Bug: Inkonsistensi Navigasi Sidebar Pelanggan & Ketiadaan Switcher Tema Gelap/Terang

**ID Bug:** `BUG-UI-003`  
**Kategori:** User Interface & User Experience (UI/UX) Consistency  
**Tingkat Keparahan:** 🟡 Sedang (*High Impact UI/UX Discrepancy*)  
**Status:** 🟢 **TERSELESAIKAN (*RESOLVED*)**  
**Tanggal Kejadian:** 04 November 2026, 19:07 WIB  
**Tanggal Penyelesaian:** 04 November 2026, 19:54 WIB  
**Branch:** `fix`  
**Penanggung Jawab:** Kelompok 1 (Yogi Ario Pratama, Muhammad Regieyanto, Ivan Farrel Nanda Kristanto, Krisna Mukti)  

---

## 1. Ringkasan Masalah (Issue Summary)

Berdasarkan pengujian pengguna dan bukti tangkapan layar antarmuka:
1. **Navigasi Terputus (*Missing Sidebar*):**  
   Pada halaman **Titip Jual Saya** ([`/titip-jual/saya`](file:///c:/laragon/www/camhub/resources/views/customer/consignment_list.php)), **Pesanan Saya** ([`/pesanan-saya`](file:///c:/laragon/www/camhub/resources/views/customer/orders.php)), dan pengajuan titip jual ([`/titip-jual/ajukan`](file:///c:/laragon/www/camhub/resources/views/customer/consignment_form.php)), tampilan masih menggunakan layout topbar horizontal lama tanpa **Sidebar Kiri Vertikal**. Area sebelah kiri layar kosong (*blank space*) dan tidak sejalan dengan halaman Dashboard, Profil, dan Dokumen KYC.
2. **Ketiadaan Sistem Tema Terpadu & Switcher Gelap/Terang:**  
   Tidak ada fitur bagi pengguna untuk beralih antara Mode Gelap (*Dark Mode*) dan Mode Terang (*Light Mode*). Padahal platform ekosistem fotografi profesional CamHub dirancang memiliki nuansa estetika *Dark Mode* sebagai tema bawaan (*default*).

---

## 2. Bukti Visual (Evidence Screenshots)

### Bukti 1: Halaman Status KYC yang Sudah Bersidebar Kiri (`/kyc/status`):
![KYC Status Bersidebar](file:///C:/Users/yogia/.gemini/antigravity/brain/ec57eb36-033d-444e-ade3-8d0c90550b05/.user_uploaded/media_1793794063138_bb6408e7.jpg)

### Bukti 2: Halaman Titip Jual yang Masih Menggunakan Topbar Lama Tanpa Sidebar (`/titip-jual/saya`):
![Titip Jual Tanpa Sidebar](file:///C:/Users/yogia/.gemini/antigravity/brain/ec57eb36-033d-444e-ade3-8d0c90550b05/.user_uploaded/media_1793794067533_96c195bd.jpg)

---

## 3. Analisis Akar Masalah (Root Cause Analysis)

1. **Inkonsistensi Template View Pelanggan:**  
   Pada tahap pengembangan awal, berkas [`orders.php`](file:///c:/laragon/www/camhub/resources/views/customer/orders.php), [`consignment_list.php`](file:///c:/laragon/www/camhub/resources/views/customer/consignment_list.php), dan [`consignment_form.php`](file:///c:/laragon/www/camhub/resources/views/customer/consignment_form.php) dibangun menggunakan komponen navigasi mandiri (`header.navbar` dan `header.topbar`), sementara halaman [`dashboard.php`](file:///c:/laragon/www/camhub/resources/views/customer/dashboard.php), [`profile.php`](file:///c:/laragon/www/camhub/resources/views/customer/profile.php), dan [`kyc_status.php`](file:///c:/laragon/www/camhub/resources/views/customer/kyc_status.php) telah menggunakan `aside.sidebar` 260px.
2. **Hardcoded CSS Color Schemes:**  
   Warna elemen antarmuka didefinisikan secara statis (*hardcoded hex*) di masing-masing berkas tampilan tanpa menggunakan variabel tema CSS global (`CSS Custom Properties`). Hal ini menyebabkan beberapa halaman berwarna dasar putih `#f8fafc` sementara halaman lain bernuansa gelap `#0b192e`, tanpa ada mekanisme pengubah tema (*theme toggle switch*).

---

## 4. Solusi & Rencana Perbaikan (Solution Plan)

1. **Harmonisasi Sidebar Kiri Pelanggan (260px Navy `#0c192c`):**  
   Mengganti layout seluruh sub-halaman pelanggan (`orders.php`, `consignment_list.php`, `consignment_form.php`, `kyc.php`) ke model Flexbox dua kolom bersidebar kiri, lengkap dengan penanda menu aktif, tautan Beranda Publik, dan tombol CSRF Logout.
2. **Kebijakan Tipografi Bersih Tanpa Ikon (*Clean Text-Only Minimalist*):**  
   Sesuai instruksi pengguna, **seluruh ikon (rumah, robot, emoji, unicode symbol, SVG) dihapus secara total** pada seluruh sidebar di **semua role (Pelanggan, Mitra Lapak, Super Admin)**. Tampilan menu murni berbasis tipografi yang bersih, tegas, dan profesional.
3. **Penerapan Universal Theme Engine (Dark Mode Default):**  
   - Mengimplementasikan CSS variables bertingkat pada tag `html[data-theme="dark"]` dan `html[data-theme="light"]`.
   - Mengatur **Dark Mode sebagai tema bawaan (*default*)**.
   - Menyediakan tombol *Switch Tema* text-only (`Mode Terang` / `Mode Gelap`) yang persisten via `localStorage` pada sidebar seluruh role (Pelanggan, Mitra Lapak, dan Super Admin).
   - Menambahkan skrip inline anti-flicker di `<head>` agar tema tidak berkedip saat navigasi antar halaman.

---

## 5. Hasil Implementasi & Verifikasi Pengujian

1. **Harmonisasi Sub-halaman Pelanggan:**
   - [`customer/orders.php`](file:///c:/laragon/www/camhub/resources/views/customer/orders.php): Sidebar kiri vertikal aktif dengan tab dinamis (Semua, Sewa Saya, Jual Beli Saya).
   - [`customer/consignment_list.php`](file:///c:/laragon/www/camhub/resources/views/customer/consignment_list.php): Sidebar kiri vertikal aktif dengan highlight menu *Titip Jual Saya*.
   - [`customer/consignment_form.php`](file:///c:/laragon/www/camhub/resources/views/customer/consignment_form.php): Sidebar kiri vertikal terpadu dengan breadcrumb kembali.
   - [`customer/kyc.php`](file:///c:/laragon/www/camhub/resources/views/customer/kyc.php): Sidebar kiri terpadu dengan menu aktif *Dokumen KYC*.
   - [`customer/kyc_status.php`](file:///c:/laragon/www/camhub/resources/views/customer/kyc_status.php): Diselaraskan tanpa ikon dan terhubung ke tema dinamis.
   - [`customer/profile.php`](file:///c:/laragon/www/camhub/resources/views/customer/profile.php): Diselaraskan tanpa ikon dan terhubung ke tema dinamis.
   - [`customer/dashboard.php`](file:///c:/laragon/www/camhub/resources/views/customer/dashboard.php): Diselaraskan tanpa ikon dan terhubung ke tema dinamis.
   - [`customer/notifications.php`](file:///c:/laragon/www/camhub/resources/views/customer/notifications.php): Sidebar kiri vertikal terpadu.

2. **Kebijakan Bersih Tanpa Ikon (*Zero Icon Policy*):**
   - Seluruh ikon emoji, unicode, dan simbol telah dihapus dari seluruh sidebar pada semua role (Pelanggan, Mitra Lapak, Super Admin).
   - Label logout diseragamkan menjadi **"Keluar"** tanpa kata "Akun".

3. **Universal Dark/Light Theme Engine:**
   - Tema bawaan (*default*) adalah **Dark Mode** (`html[data-theme="dark"]`).
   - Tombol toggle tema teks murni (`Mode Terang` ↔ `Mode Gelap`) tersimpan persisten di `localStorage`.
   - Skrip anti-flicker di `<head>` menjamin tidak ada kilatan saat navigasi.
   - Diuji via Chrome DevTools: toggle beralih instan antara mode gelap dan terang tanpa error.

4. **Verifikasi Regression Suite:**
   - `php tests/run_all.php`: **147 / 147 PASS (100% SUKSES HIJAU)** di seluruh 15 suite pengujian.

---

## 6. Keputusan Arsitektur
Pembaruan ini dicatat secara resmi pada [`Docs/Decision_Log.md`](file:///c:/laragon/www/camhub/Docs/Decision_Log.md) di bawah kode **`DEC-042`**.

