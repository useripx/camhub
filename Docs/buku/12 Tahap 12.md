# BUKU PANDUAN PENGUJIAN & DOKUMENTASI RESMI TAHAP 12
## SISTEM NOTIFIKASI DALAM APLIKASI & PELAPORAN FINANSIAL PLATFORM (PUSAT NOTIFIKASI, REKAPITULASI OMZET, EKSPOR CSV, & CETAK NOTA RESMI TRANSAKSI)
### Platform Marketplace & Rental Kamera CamHub

> **Kredit Tim Pengembang (Kelompok 1):**  
> 1. **Yogi Ario Pratama** — *Project Owner & Lead Developer*  
> 2. **Muhammad Regieyanto** — *Backend & System Architect*  
> 3. **Ivan Farrel Nanda Kristanto** — *Core Routing & Security Engineer*  
> 4. **Krisna Mukti** — *Quality Assurance & Presentation Coordinator*  
>  
> **Tanggal Rilis:** 04 November 2026  
> **Target Status Mutu:** **100% LULUS (131 / 131 PENGUJIAN OTOMATIS PLATFORM)**  
> **Branch Git:** `tahap/12-notifikasi-pelaporan` -> `develop`

---

## 1. Ringkasan Eksekutif Tahap 12

Tahap 12 menghadirkan dua pilar transparansi dan komunikasi kunci pada ekosistem CamHub: **Pusat Notifikasi Dalam Aplikasi (In-App Notification Hub)** dan **Sistem Pelaporan Finansial Komprehensif Platform (Financial Reporting & Invoice System)**.

### Fitur-Fitur Utama yang Diselesaikan:
1. **Pusat Notifikasi Interaktif Pelanggan (`/notifikasi`):**
   - Layanan pengiriman notifikasi instan saat terjadi kejadian penting sistem: verifikasi KYC disetujui/ditolak, pesanan sewa/beli baru dibuat, dana escrow dirilis/di-refund, denda diterbitkan, dan pengajuan titip jual diinspeksi.
   - Penandaan notifikasi telah dibaca (*mark as read*) secara individual maupun serentak (*mark all as read*).
   - Pengalihan langsung (*action URL*) ke halaman transaksi atau dokumen terkait.
   - Endpoint API JSON unread count (`/api/notifikasi/unread-count`) untuk sinkronisasi antarmuka pengguna tanpa refresh.

2. **Pusat Laporan Finansial & Omzet Administrator (`/dashboard/admin/laporan`):**
   - 4 Kartu Metrik Kas Utama: **Total Perputaran Kas (GTV)**, **Pendapatan Bersih Platform (Net Revenue)**, **Total Transaksi Berjalan**, dan **Total Pencairan Saldo Mitra**.
   - Analisis pendapatan bersih dari 3 sumber resmi:
     1. Biaya administrasi rekening bersama escrow Rp 5.000 per transaksi aktif.
     2. Bagi hasil komisi titip jual konsinyasi (10% dari harga deal kamera).
     3. Penerimaan denda pelanggaran operasional yang telah dilunasi.
   - Pemfilteran dinamis berdasarkan rentang waktu: **Hari Ini (`today`)**, **7 Hari Terakhir (`7days`)**, **Bulan Ini (`month`)**, dan **Rentang Kustom (`custom`)**.
   - Distribusi omzet penjualan dan penyewaan per kategori kamera (DSLR, Mirrorless, Lensa, Aksesori, Drone).
   - Tabel riwayat transaksi lengkap dengan rincian biaya platform dan status rekening bersama.

3. **Mesin Ekspor Rekapitulasi CSV / Excel:**
   - Generator CSV berstandar streaming dengan UTF-8 BOM (`\xEF\xBB\xBF`) agar aksen karakter dan pemformatan rupiah terbaca sempurna di Microsoft Excel.
   - Ekspor Laporan Finansial Admin (`/dashboard/admin/laporan/export`).
   - Ekspor Rekapitulasi Mutasi Saldo & Transaksi Mitra Lapak (`/dashboard/mitra/saldo/export`).

4. **Halaman Cetak Nota & Invoice Resmi Transaksi (Print-to-PDF):**
   - **Nota Resmi Penyewaan Kamera (`/pesanan/sewa/{id}/cetak`):** Memuat nomor nota resmi `#RENT-XXXXX`, rincian unit kamera dan nomor seri fisik (`serial_number`), periode tanggal dan durasi sewa, informasi toko mitra lapak penyedia, serta kartu jaminan perlindungan rekber escrow.
   - **Invoice Resmi Pembelian Kamera (`/pesanan/beli/{id}/cetak`):** Memuat kode transaksi `#BUY-XXXXX`, alamat tujuan ekspedisi kurir, rincian biaya kamera, ongkos kirim, dan fee rekber escrow.
   - Dilengkapi CSS cetak mandiri `@media print` untuk menghilangkan tombol navigasi saat mencetak langsung ke kertas atau menyimpannya sebagai berkas PDF resmi.
   - Dilindungi mekanisme isolasi IDOR (*Insecure Direct Object Reference*): Pelanggan hanya dapat mencetak dokumen miliknya sendiri, sementara Super Administrator memiliki wewenang mencetak seluruh nota transaksi platform.

---

## 2. Arsitektur Komponen & Basis Data

### A. Tabel Basis Data
1. **`notifications`**:
   - `id` (INT, Primary Key)
   - `user_id` (INT, Foreign Key -> `users.id`)
   - `title` (VARCHAR 150)
   - `message` (TEXT)
   - `is_read` (TINYINT 1, Default 0)
   - `action_url` (VARCHAR 255, Nullable)
   - `created_at` (DATETIME)

2. **Tabel Finansial yang Diagregasi:**
   - `rental_bookings` & `sales_orders` (Omzet kotor transaksi GTV & fee rekber)
   - `consignments` (Bagi hasil komisi 10% unit titip jual yang laku)
   - `fines` (Penerimaan kas dari denda terlambat/kerusakan berstatus `paid`)
   - `partner_withdrawals` (Arus kas keluar pencairan dana saldo mitra lapak)

### B. Daftar Kelas & Repositori Baru
- **Model:** [`app/Models/Notification.php`](file:///c:/laragon/www/camhub/app/Models/Notification.php)
- **Repositori:**
  - [`app/Repositories/NotificationRepository.php`](file:///c:/laragon/www/camhub/app/Repositories/NotificationRepository.php)
  - [`app/Repositories/ReportRepository.php`](file:///c:/laragon/www/camhub/app/Repositories/ReportRepository.php)
- **Layanan:**
  - [`app/Services/NotificationService.php`](file:///c:/laragon/www/camhub/app/Services/NotificationService.php)
  - [`app/Services/ReportService.php`](file:///c:/laragon/www/camhub/app/Services/ReportService.php)
- **Pengontrol:**
  - [`app/Controllers/NotificationController.php`](file:///c:/laragon/www/camhub/app/Controllers/NotificationController.php)
  - Diperbarui: [`app/Controllers/AdminDashboardController.php`](file:///c:/laragon/www/camhub/app/Controllers/AdminDashboardController.php) (`reports`, `exportReport`)
  - Diperbarui: [`app/Controllers/PartnerWalletController.php`](file:///c:/laragon/www/camhub/app/Controllers/PartnerWalletController.php) (`export`)
  - Diperbarui: [`app/Controllers/BookingController.php`](file:///c:/laragon/www/camhub/app/Controllers/BookingController.php) (`printReceipt`)
  - Diperbarui: [`app/Controllers/SalesController.php`](file:///c:/laragon/www/camhub/app/Controllers/SalesController.php) (`printInvoice`)

---

## 3. Matriks Pengujian Mutu (10 Definition of Done)

Berikut adalah hasil eksekusi pengujian otomatis pada berkas [`tests/test_report_notifications.php`](file:///c:/laragon/www/camhub/tests/test_report_notifications.php):

| Kode Pengujian | Kriteria Pengujian Mutu (Definition of Done) | Hasil | Keterangan Verifikasi |
|---|---|:---:|---|
| **TEST-REP-01** | Model & Repositori Notifikasi Terdaftar | **PASS** | Entitas notifikasi tersimpan dan fungsi `getUnreadCount` menghitung badge secara akurat. |
| **TEST-REP-02** | Pembuatan Notifikasi Otomatis pada Aksi Transaksi & Operasional | **PASS** | Dispatcher memicu notifikasi otomatis saat KYC disetujui dan saat unit titip jual lolos inspeksi. |
| **TEST-REP-03** | Aksi Penandaan Notifikasi Dibaca (`markAsRead` & `markAllAsRead`) | **PASS** | Status `is_read` berubah menjadi 1 dan unread count disetel menjadi 0 secara instan. |
| **TEST-REP-04** | Proteksi Hak Akses RBAC Modul Laporan Admin | **PASS** | Role non-admin diblokir dengan kode HTTP 403, sedangkan Super Admin diberikan akses penuh. |
| **TEST-REP-05** | Kalkulasi Akurat Metrik Kas Platform (Gross GTV & Net Revenue) | **PASS** | GTV terhitung Rp 267.420.000 dan Net Platform Revenue terakumulasi Rp 13.670.000. |
| **TEST-REP-06** | Pemfilteran Laporan Finansial Berdasarkan Rentang Waktu | **PASS** | Filter `today`, `7days`, `month`, dan rentang tanggal `custom` berhasil mengeksekusi parameter SQL terisolasi. |
| **TEST-REP-07** | Distribusi Omzet Transaksi per Kategori Kamera | **PASS** | Agregasi omzet berhasil dikelompokkan ke 5 kategori kamera secara menurun (*descending*). |
| **TEST-REP-08** | Ekspor Rekapitulasi Laporan Finansial Admin ke Format CSV | **PASS** | Berkas CSV admin tergenerate lengkap dengan UTF-8 BOM, judul laporan, metrik kas, dan header kolom transaksi. |
| **TEST-REP-09** | Ekspor Mutasi Saldo & Transaksi Mitra ke Format CSV | **PASS** | Berkas CSV mutasi escrow toko mitra lapak tergenerate rapi beserta status dan nominal dana. |
| **TEST-REP-10** | Halaman Cetak Nota Resmi Transaksi & Proteksi Anti-IDOR | **PASS** | Template cetak nota sewa dan invoice beli siap pakai dengan proteksi Anti-IDOR lintas pengguna. |

**Total Skor Uji Tahap 12: 10 / 10 LULUS (100% SUKSES)**  
**Total Skor Regresi Seluruh Sistem: 131 / 131 LULUS (100% SUKSES)**

---

## 4. Naskah Roleplay Presentasi Sidang Tim (Kelompok 1)

Berikut panduan dialog pembagian peran tim Kelompok 1 untuk mempresentasikan hasil Tahap 12 di hadapan dosen penguji / stakeholder:

```
[SCENE: Presentasi Progres Pengembangan CamHub Tahap 12]

Yogi Ario Pratama (Project Owner & Lead Developer):
"Selamat pagi Bapak/Ibu Dosen Penguji dan rekan-rekan sekalian.
Saya Yogi Ario Pratama bersama rekan-rekan Kelompok 1 hadir hari ini untuk
mempresentasikan pencapaian Tahap 12 pada platform CamHub.
Pada tahap ini, fokus utama kami adalah transparansi finansial dan kepastian
informasi pengguna. Kami telah menuntaskan sistem notifikasi otomatis terintegrasi
dan modul pelaporan keuangan super administrator yang dilengkapi fitur cetak nota
serta ekspor CSV resmi.
Arsitektur teknis backend akan dijelaskan langsung oleh rekan saya, Regie."

Muhammad Regieyanto (Backend & System Architect):
"Terima kasih Yogi. Dari sudut pandang backend arsitektur, kami mengimplementasikan
NotificationService dan ReportService yang beroperasi di atas pola murni MVC native.
ReportRepository mengakumulasi metrik finansial secara presisi: perputaran kas kotor (GTV),
biaya administrasi rekening bersama escrow Rp 5.000 per transaksi, bagi hasil 10% komisi
titip jual kamera bekas, serta penerimaan kas dari denda operasional.
Seluruh data dapat difilter dinamis berdasarkan hari ini, 7 hari, bulanan, maupun rentang
tanggal kustom. Selain itu, generator CSV kami menyematkan UTF-8 BOM agar kompatibel
100% saat dibuka di Microsoft Excel tanpa masalah karakter.
Mengenai proteksi keamanan dan otorisasi dokumen, Ivan akan memaparkannya."

Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer):
"Terima kasih Mas Regie. Dari sisi keamanan dan routing, seluruh rute laporan admin
dilindungi Middleware Role-Based Access Control (RBAC). Pengguna dengan role pelanggan
atau mitra lapak yang mencoba mengakses endpoint /dashboard/admin/laporan secara otomatis
diblokir dengan halaman 403 Forbidden.
Pada modul cetak nota sewa dan invoice pembelian, kami menerapkan proteksi Anti-IDOR ketat.
Pelanggan hanya diizinkan mencetak dokumen pesanannya sendiri. Jika ID pesanan dimanipulasi
di URL browser, sistem menolak akses tersebut dan mengembalikan pengguna ke dashboard aman.
Selanjutnya, hasil validasi mutu pengujian akan dilaporkan oleh Krisna."

Krisna Mukti (Quality Assurance & Presentation Coordinator):
"Terima kasih Mas Ivan. Sebagai penanggung jawab pengujian mutu platform, kami telah
menjalankan pengujian otomatis end-to-end melalui berkas test_report_notifications.php
dan seluruh 10 skenario Definition of Done lulus 100%.
Kami juga menjalankan uji regresi menyeluruh pada seluruh 13 rangkaian tes dari Tahap 1
hingga Tahap 12, dan hasilnya adalah 131 dari 131 pengujian berstatus HIJAU (PASS),
dengan 0 error dan 0 warning. Seluruh modul notifikasi, laporan keuangan, ekspor CSV,
dan nota cetak telah terverifikasi sempurna.
Saya kembalikan kepada Yogi untuk kesimpulan."

Yogi Ario Pratama (Project Owner & Lead Developer):
"Terima kasih tim. Dengan selesainya Tahap 12, platform CamHub kini telah mencapai
92% penyelesaian sistem. Platform ini siap memasuki tahap finalisasi Tahap 13 untuk
persiapan deployment produksi dan presentasi akhir. Terima kasih atas perhatiannya."
```

---
*Dokumen ini diterbitkan secara resmi pada repositori CamHub oleh Kelompok 1.*
