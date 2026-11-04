# BUKU PANDUAN PENGUJIAN & DOKUMENTASI RESMI TAHAP 11
## DASHBOARD SUPER ADMINISTRATOR (PUSAT KENDALI PENGGUNA, VERIFIKASI KYC DOKUMEN, MODERASI SENGKETA ESCROW, & AUDIT KEAMANAN PLATFORM)
### Platform Marketplace & Rental Kamera CamHub

> **Kredit Tim Pengembang (Kelompok 1):**  
> 1. **Yogi Ario Pratama** — *Project Owner & Lead Developer*  
> 2. **Muhammad Regieyanto** — *Backend & System Architect*  
> 3. **Ivan Farrel Nanda Kristanto** — *Core Routing & Security Engineer*  
> 4. **Krisna Mukti** — *Quality Assurance & Presentation Coordinator*  
>  
> **Tanggal Rilis:** 04 November 2026  
> **Target Status Mutu:** **100% LULUS (121 / 121 PENGUJIAN OTOMATIS PLATFORM)**  
> **Branch Git:** `tahap/11-dashboard-administrator` -> `develop`

---

## 1. Ringkasan Eksekutif Tahap 11

Tahap 11 menghadirkan pusat komando tertinggi sistem (**Super Administrator Control Center**) untuk platform CamHub. Seluruh alur moderasi platform, kepatuhan identitas pengguna, pengawasan unit fisik kamera, arbitrasi finansial rekening bersama, hingga penegakan sanksi denda operasional kini dikendalikan secara tersentralisasi, aman, dan transparan:

1. **Penyelarasan Tampilan 100% Mengacu Mockup Resmi:**
   Antarmuka dashboard admin dirombak menyeluruh mengacu pada prototype [`template/CamHub — Interactive UI UX Prototype/CamHub — Dashboard Admin.png`](file:///c:/laragon/www/camhub/template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Dashboard%20Admin.png):
   - Sidebar vertikal navy gelap (`#0c192c`) dengan logo CamHub, badge pengguna, navigasi modul dinamis, dan tombol keluar sesi terisolasi.
   - 4 Kartu Metrik Ringkas: **Menunggu Verifikasi KYC**, **Unit Inspeksi**, **Transaksi Hari Ini**, dan **Denda Pending**.
   - Grafik batang visual **Transaksi 7 Hari Terakhir** berwarna biru `#2563eb`.

2. **Pusat Verifikasi Dokumen KYC (`/dashboard/admin/kyc`):**
   - Peninjauan berkas KTP dan foto selfie wajah pengguna secara visual dengan zoom modal.
   - Filter tab status: *Semua Pengajuan*, *Menunggu Verifikasi*, *Disetujui*, dan *Ditolak*.
   - Aksi 1-klik Persetujuan (*Approve*) yang mengaktifkan hak sewa pelanggan secara instan (`kyc_verified = 1`).
   - Aksi Penolakan (*Reject*) dengan alasan penolakan wajib untuk panduan perbaikan pengguna.

3. **Pusat Transaksi, Arbitrasi Sengketa Escrow, & Pencairan Dana Mitra (`/dashboard/admin/transaksi`):**
   - **Moderasi Sengketa Escrow (Disputed Resolution):** Admin memiliki wewenang mengakhiri status sengketa melalui 2 keputusan adil:
     - `force-release`: Melepaskan dana tertahan langsung ke saldo toko mitra jika komplain tidak terbukti.
     - `force-refund`: Mengembalikan dana penuh ke rekening pelanggan jika terbukti terjadi kelalaian mitra lapak.
   - **Persetujuan Penarikan Dana Mitra (Withdrawals):** Admin dapat menyetujui (*processed*) transfer bank mitra atau menolak (*rejected*) permohonan yang tidak valid.
   - **Tabel Transaksi Menyeluruh:** Memantau seluruh transaksi sewa dan jual beli lintas toko di platform CamHub.

4. **Pengawasan Jadwal Unit Kamera Lintas Mitra (`/dashboard/admin/jadwal`):**
   - Mengawasi seluruh nomor seri fisik kamera (`serial_number`), nama toko mitra pemilik, dan status operasional (`available`, `rented`, `maintenance`).
   - Menampilkan kalender tanggal sewa aktif untuk mencegah manipulasi ketersediaan unit.

5. **Manajemen Penegakan Denda Operasional (`/dashboard/admin/denda`):**
   - Penerbitan denda resmi pelanggaran operasional: Keterlambatan Pengembalian (`late_return`), Kerusakan Fisik Unit (`damage`), dan Aksesori Hilang (`missing_accessory`).
   - Pelunasan status denda dari `pending` menjadi `paid` dengan pencatatan nominal denda.

6. **Pusat Pengaturan Pengguna & Jejak Audit Keamanan (`/dashboard/admin/pengaturan`):**
   - Pengelolaan status aktif/suspend seluruh akun pengguna platform (Pelanggan, Mitra, Admin) dengan proteksi anti-lockout akun Root Admin (ID: 1).
   - Penayangan 50 baris riwayat terkini aktivitas sistem pada tabel `audit_logs` secara transparan.

---

## 2. Arsitektur Komponen & Basis Data

### A. Tabel Terkait
- **`kyc_submissions`**: Menyimpan status dokumen (`pending`, `approved`, `rejected`), waktu verifikasi, dan catatan penolakan.
- **`escrow_transactions`**: Status penahanan dana (`held`, `released`, `refunded`, `disputed`).
- **`partner_withdrawals`**: Pengajuan pencairan saldo toko mitra lapak.
- **`product_units`**: Unit fisik kamera dengan nomor seri unik dan status ketersediaan.
- **`fines`**: Rekam denda keterlambatan dan kerusakan fisik unit.
- **`users`**: Tabel pengguna dengan kolom kontrol status (`is_active` / suspend).
- **`audit_logs`**: Log audit keamanan platform yang merekam seluruh aksi administratif.

### B. Daftar Modul Backend
1. **`App\Repositories\AdminRepository`**:
   - `getPendingKycSubmissions()`: Mengambil antrean verifikasi KTP.
   - `getAllKycSubmissions(string $status)`: Mengambil daftar pengajuan KYC terfilter.
   - `approveKyc(int $submissionId, int $adminId)`: Menyetujui KYC dan mengaktifkan `kyc_verified` akun.
   - `rejectKyc(int $submissionId, int $adminId, string $reason)`: Menolak pengajuan KYC.
   - `getAllTransactions()`: Mengambil ringkasan transaksi sewa dan jual beli.
   - `getDisputedEscrows()`: Mengambil sengketa escrow aktif yang membutuhkan arbitrasi.
   - `getPendingWithdrawals()`: Mengambil permohonan pencairan saldo mitra.
   - `resolveDispute(int $escrowId, string $action, int $adminId)`: Arbitrasi sengketa escrow.
   - `processWithdrawal(int $withdrawalId, string $status, int $adminId)`: Pemrosesan pencairan dana.
   - `getAllUnitSchedules()`: Mengambil ketersediaan nomor seri unit kamera.
   - `getAllFines()`: Mengambil rekapitulasi denda operasional platform.
   - `createFine(...)`: Menerbitkan denda baru ke pengguna.
   - `settleFine(int $fineId)`: Melunasi denda pending.
   - `getAllUsers()`: Mengambil daftar akun pengguna sistem.
   - `toggleUserStatus(int $userId)`: Mengubah status aktif / suspended akun.
   - `getAuditLogs(int $limit)`: Mengambil rekaman log audit keamanan sistem.

2. **`App\Services\AdminService`**:
   - Menjamin integritas transaksi dengan `$db->beginTransaction()` dan rollback atomik.
   - Mencegah penonaktifan akun Root Administrator ID 1.
   - Mencatat setiap tindakan administratif ke `audit_logs` secara otomatis.

3. **`App\Controllers\AdminDashboardController`**:
   - `index()`: Dashboard metrik utama & chart transaksi.
   - `kyc()`, `approveKyc()`, `rejectKyc()`: Manajemen verifikasi dokumen KYC.
   - `transactions()`, `resolveDispute()`, `processWithdrawal()`: Manajemen transaksi & escrow.
   - `schedule()`: Pengawasan jadwal dan nomor seri unit kamera.
   - `fines()`, `storeFine()`, `payFine()`: Manajemen denda operasional.
   - `settings()`, `toggleUserStatus()`: Manajemen akun & log audit sistem.

---

## 3. Matriks Hasil Pengujian Mutu (Definition of Done)

Pengujian dijalankan melalui [`tests/test_admin_suite.php`](file:///c:/laragon/www/camhub/tests/test_admin_suite.php):

| Kode Uji | Skenario Pengujian | Hasil | Keterangan Verifikasi |
|:---:|:---|:---:|:---|
| `TEST-ADM-01` | Proteksi Akses RBAC Super Admin | **PASS** | Akses dibatasi ketat untuk role `admin`, non-admin dialihkan ke beranda publik. |
| `TEST-ADM-02` | Agregasi Metrik Dashboard Admin (4 Kartu & Chart) | **PASS** | Metrik KYC, unit inspeksi, transaksi hari ini, dan denda teragregasi presisi. |
| `TEST-ADM-03` | Peninjauan Antrean Verifikasi Dokumen KYC | **PASS** | Pengajuan KTP dan selfie termuat lengkap dengan dokumen pendukung. |
| `TEST-ADM-04` | Aksi Persetujuan KYC (Approval) & Sinkronisasi Profil | **PASS** | Status KYC menjadi `approved`, `users.kyc_verified` otomatis aktif bernilai 1. |
| `TEST-ADM-05` | Aksi Penolakan KYC (Rejection) dengan Alasan | **PASS** | Status KYC menjadi `rejected` dan alasan tersimpan untuk instruksi perbaikan. |
| `TEST-ADM-06` | Arbitrasi Sengketa Escrow (Force-Release ke Mitra) | **PASS** | Sengketa terselesaikan, dana escrow cair ke saldo mitra, status menjadi `released`. |
| `TEST-ADM-07` | Arbitrasi Sengketa Escrow (Force-Refund ke Pelanggan) | **PASS** | Sengketa terselesaikan, dana escrow berstatus `refunded`, tidak memotong saldo mitra. |
| `TEST-ADM-08` | Pengawasan Nomor Seri Unit Fisik & Jadwal Sewa | **PASS** | Daftar unit kamera, nomor seri fisik, nama mitra, dan jadwal booking termuat rapi. |
| `TEST-ADM-09` | Penerbitan & Pelunasan Denda Operasional (Fines) | **PASS** | Penerbitan denda `late_return` sukses tercatat dan dapat dilunasi menjadi `paid`. |
| `TEST-ADM-10` | Manajemen Status Pengguna & Jejak Audit Keamanan | **PASS** | Pengguna dapat di-suspend/aktifkan (Root Admin terproteksi), `audit_logs` merekam aksi. |

**Status Akumulatif Pengujian Platform CamHub:**
- 11 Berkas Test Suite Platform: **121 / 121 LULUS (100% SUCCESS, 0 GAGAL / 0 ERROR)**

---

## 4. Panduan Demonstrasi Sidang & Simulasi Peran Tim

Berikut adalah naskah presentasi resmi saat sidang pengujian di hadapan Dosen Penguji / Asisten Praktikum:

### **Pemeran 1: Yogi Ario Pratama (Project Owner & Lead Developer)**
> *"Selamat pagi/siang Bapak/Ibu Dosen Penguji. Pada Tahap 11 ini, kami menyelesaikan modul Pusat Kendali Super Administrator (Super Admin Command Center) untuk platform CamHub. Seluruh antarmuka admin telah kami bangun ulang 100% presisi mengacu pada template resmi `CamHub — Dashboard Admin.png`. Tampilan kini memiliki Sidebar Navy gelap, 4 kartu ringkasan operasional harian, serta grafik batang transaksi 7 hari terakhir. Di sini Super Admin memegang kendali penuh atas verifikasi dokumen, integritas transaksi, jadwal unit sewa, denda, dan audit keamanan platform."*

### **Pemeran 2: Muhammad Regieyanto (Backend & System Architect)**
> *"Dari sudut pandang arsitektur backend, kami memperluas `AdminRepository` dan `AdminService` untuk mengelola seluruh domain platform secara atomik. Salah satu fitur paling krusial adalah Arbitrasi Sengketa Rekening Bersama Escrow. Apabila terjadi komplain antara pelanggan dan mitra lapak, Super Admin memiliki instrumen 'Force-Release' untuk mencairkan dana ke toko mitra, atau 'Force-Refund' untuk mengembalikan dana penuh ke rekening pelanggan. Seluruh perubahan status dieksekusi dengan isolasi transaksi ACID MySQL InnoDB sehingga tidak mungkin terjadi selisih saldo atau mutasi silang."*

### **Pemeran 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)**
> *"Dari aspek keamanan dan penegakan regulasi sistem, seluruh rute `/dashboard/admin/*` diproteksi secara berlapis oleh middleware `auth` dan `role:admin`. Pada modul verifikasi KYC, admin dapat menyetujui dokumen identitas pengguna dalam satu klik yang langsung menyinkronkan status `kyc_verified` akun pelanggan, atau menolaknya dengan menyertakan alasan. Kami juga menyematkan proteksi anti-lockout pada manajemen pengguna: akun Root Admin ID 1 tidak dapat di-suspend dalam kondisi apa pun. Setiap tindakan pengawasan dan sanksi operasional direkam secara otomatis pada tabel `audit_logs`."*

### **Pemeran 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)**
> *"Untuk membuktikan integritas seluruh sistem, kami mengeksekusi test suite mandiri `test_admin_suite.php` yang mencakup 10 skenario pengujian Definition of Done dan seluruhnya dinyatakan 100% PASS. Selain itu, seluruh 11 rangkaian pengujian otomatis platform dari Tahap 01 hingga Tahap 11 berhasil lulus dengan skor sempurna: **121 dari 121 tes sukses (100% PASS, 0 Gagal, 0 Error)**. Platform CamHub kini telah stabil dan siap melangkah ke Tahap 12: Integrasi Komprehensif Antar Modul, Optimasi Performa, & Uji Stabilitas E2E."*

---
