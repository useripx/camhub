# Laporan Bug: Footer Sidebar Tersembunyi di Bawah Layar & Undefined Variable $kycStatus pada Pengaturan

**ID Bug:** `BUG-UI-004`  
**Kategori:** Layout Flow & PHP Runtime Warning  
**Tingkat Keparahan:** 🔴 Tinggi (*Blocking UX & Runtime Warning*)  
**Status:** 🟢 **TERSELESAIKAN (*RESOLVED*)**  
**Tanggal Kejadian:** 04 November 2026, 19:53 WIB  
**Tanggal Penyelesaian:** 04 November 2026, 20:10 WIB  
**Branch:** `fix`  
**Penanggung Jawab:** Kelompok 1 (Yogi Ario Pratama, Muhammad Regieyanto, Ivan Farrel Nanda Kristanto, Krisna Mukti)  

---

## 1. Ringkasan Masalah (Issue Summary)

Berdasarkan pengujian pengguna dan bukti tangkapan layar antarmuka:
1. **Footer Sidebar Tersembunyi di Halaman Berkonten Panjang (`/profil`):**  
   Tombol *Mode Terang/Gelap*, tautan *Lihat Beranda Publik*, dan tombol *Keluar* yang berada di dalam `.sidebar-bottom` tampak hilang/tersembunyi pada halaman Pengaturan ([`/profil`](file:///c:/laragon/www/camhub/resources/views/customer/profile.php)). Tombol tersebut hanya terlihat pada halaman dengan konten pendek seperti Titip Jual Saya ([`/titip-jual/saya`](file:///c:/laragon/www/camhub/resources/views/customer/consignment_list.php)).
2. **Pesan Peringatan PHP (*PHP Runtime Warning*):**  
   Pada halaman [`/profil`](file:///c:/laragon/www/camhub/resources/views/customer/profile.php) muncul pesan kesalahan berulang:
   ```text
   Warning: Undefined variable $kycStatus in C:\laragon\www\camhub\resources\views\customer\profile.php on line 495
   Warning: Undefined variable $kycStatus in C:\laragon\www\camhub\resources\views\customer\profile.php on line 499
   Warning: Undefined variable $kycStatus in C:\laragon\www\camhub\resources\views\customer\profile.php on line 503
   Warning: Undefined variable $kycStatus in C:\laragon\www\camhub\resources\views\customer\profile.php on line 516
   Warning: Undefined variable $kycStatus in C:\laragon\www\camhub\resources\views\customer\profile.php on line 524
   Warning: Undefined variable $kycStatus in C:\laragon\www\camhub\resources\views\customer\profile.php on line 532
   ```

---

## 2. Bukti Visual (Evidence Screenshots)

### Bukti 1: Footer Sidebar Terlihat pada Halaman Konten Pendek (`/titip-jual/saya`):
![Footer Sidebar Terlihat](file:///C:/Users/yogia/.gemini/antigravity/brain/ec57eb36-033d-444e-ade3-8d0c90550b05/.user_uploaded/media_1793796811314_bf667839.png)

### Bukti 2: Footer Sidebar Tersembunyi & Muncul Error $kycStatus pada Pengaturan (`/profil`):
![Error Pengaturan & Sidebar Terpotong](file:///C:/Users/yogia/.gemini/antigravity/brain/ec57eb36-033d-444e-ade3-8d0c90550b05/.user_uploaded/media_1793796911345_004e9cbc.jpg)

---

## 3. Analisis Akar Masalah (Root Cause Analysis)

1. **Akar Masalah Sidebar Footer Tersembunyi:**  
   - Elemen `.sidebar-bottom` menggunakan `margin-top: auto` di dalam `.sidebar` yang memiliki `min-height: 100vh`.
   - Pada halaman `/profil`, form dan kartu pengguna memiliki tinggi melebihi 1200px. Karena `body` menggunakan `display: flex` tanpa `position: sticky` pada sidebar, elemen sidebar ikut memanjang mengikuti tinggi konten halaman.
   - Akibatnya, `margin-top: auto` mendorong `.sidebar-bottom` ke dasar halaman (di koordinat Y > 1000px), sehingga pengguna pada layar resolusi standar (768p/1080p) melihat area kosong di bawah menu Pengaturan dan footer tersembunyi jauh di bawah lipatan layar (*below the fold*).
2. **Akar Masalah Undefined Variable `$kycStatus`:**  
   - Berkas [`CustomerProfileController.php`](file:///c:/laragon/www/camhub/app/Controllers/CustomerProfileController.php) pada method `showProfile()` hanya mem-passing `'profile' => $profile`, tanpa secara eksplisit mendefinisikan kunci `'kycStatus'` dan `'kycRecord'` pada array data view.
   - Di sisi view [`profile.php`](file:///c:/laragon/www/camhub/resources/views/customer/profile.php), pengecekan variabel menggunakan `$kycStatus === 'approved'` langsung tanpa operator *null coalescing* (`$profile['kyc_status'] ?? 'unverified'`), memicu PHP Warning.

---

## 4. Solusi & Rencana Perbaikan (Solution Plan)

1. **Penyelarasan Posisi Footer Sidebar Langsung di Bawah Pengaturan:**
   - Menghapus `margin-top: auto` dari `.sidebar-bottom`.
   - Memposisikan `.sidebar-bottom` langsung menempel secara rapat dan rapi di bawah menu `Pengaturan` (`margin-top: 18px; padding-top: 16px; border-top: 1px solid #1e3a5f;`).
   - Menerapkan `position: sticky; top: 0; height: 100vh; overflow-y: auto;` pada `.sidebar` di seluruh view pelanggan agar sidebar tetap terkunci di layar saat konten halaman di-scroll.
2. **Penyelesaian Error `$kycStatus`:**
   - Menginisialisasi variabel di baris teratas [`profile.php`](file:///c:/laragon/www/camhub/resources/views/customer/profile.php):
     ```php
     $kycStatus = $kycStatus ?? ($profile['kyc_status'] ?? 'unverified');
     $kycRecord = $kycRecord ?? ($profile['kyc'] ?? null);
     ```
   - Memastikan [`CustomerProfileController.php`](file:///c:/laragon/www/camhub/app/Controllers/CustomerProfileController.php) menyertakan `'kycStatus' => $profile['kyc_status'] ?? 'unverified'` dan `'kycRecord' => $profile['kyc'] ?? null`.
3. **Hasil Verifikasi Live Browser:**
   - Halaman `http://localhost/camhub/public/profil` diverifikasi via Chrome DevTools: `hasWarnings: false`, `warnings: []` (0 warning).
   - `isBottomVisibleDirectlyInViewport: true`: Tombol *Mode Terang*, *Lihat Beranda Publik*, dan *Keluar* terlihat langsung di viewport layar (Y = 468px s.d. 613px pada viewport 674px), menempel tepat di bawah *Pengaturan*.
   - Seluruh 15 suite pengujian (`php tests/run_all.php`) menghasilkan **147 / 147 PASS (100% SUKSES HIJAU)**.

---

## 5. Keputusan Arsitektur
Pembaruan ini dicatat secara resmi pada [`Docs/Decision_Log.md`](file:///c:/laragon/www/camhub/Docs/Decision_Log.md) di bawah kode **`DEC-043`**.

