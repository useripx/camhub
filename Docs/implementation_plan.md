
# CAMHUB — IMPLEMENTATION PLAN BACKEND

**Nama Proyek:** CamHub – Camera Marketplace Platform
**Versi Dokumen:** 1.2
**Status:** DRAFT — REVIEW
**Project Owner:** Yogi Ario
**Repository:** https://github.com/useripx/camhub.git
**Direktori Lokal:** `C:\laragon\www\camhub`
**Backend:** PHP Native
**Database:** MySQL
**Web Server:** Laragon
**Arsitektur:** Modular PHP Native (MVC-inspired)
**Target Pasar:** Indonesia
**Bahasa Dokumentasi:** Bahasa Indonesia

---

# BAB 1 — IDENTITAS DAN TUJUAN PROYEK

CamHub merupakan platform marketplace berbasis web yang menyediakan empat layanan utama:

1. Penyewaan kamera.
2. Jual beli kamera.
3. Penitipan penjualan kamera.
4. Pengelolaan lapak mitra.

Sistem memiliki tiga aktor utama:

- Customer.
- Partner/Mitra.
- Administrator.

Tujuan proyek adalah membangun marketplace yang dapat digunakan secara nyata, bukan sekadar prototype tampilan.

# BAB 2 — KETENTUAN TEKNOLOGI


| Komponen          | Teknologi                  |
| ------------------- | ---------------------------- |
| Backend           | PHP Native                 |
| Database          | MySQL                      |
| Database Access   | PDO                        |
| Frontend          | HTML5, CSS3, JavaScript    |
| Web Server        | Laragon                    |
| Version Control   | Git                        |
| Remote Repository | GitHub                     |
| Arsitektur        | Modular PHP Native         |
| UI/UX             | Mengikuti prototype CamHub |

Tidak diperbolehkan mengganti PHP Native dengan Laravel, CodeIgniter, atau framework backend lainnya tanpa persetujuan Project Owner.

# BAB 3 — ATURAN MOCKUP

Sembilan gambar prototype merupakan sumber acuan desain utama.

1. Beranda.
2. Katalog Produk.
3. Detail Produk.
4. Booking & Checkout.
5. Dashboard Pelanggan.
6. Login & Register.
7. Dashboard Mitra Lapak.
8. Dashboard Admin.
9. Verifikasi Identitas KYC.

Ketentuan:

- Tidak mengubah struktur layout.
- Tidak mengganti warna.
- Tidak mengubah ukuran dan susunan komponen secara sepihak.
- Tidak menghilangkan komponen.
- Tidak mengganti desain dengan template generik.
- Tidak membuat halaman baru tanpa persetujuan.
- Seluruh elemen interaktif harus berfungsi sesuai kebutuhan bisnis.

Kondisi tambahan seperti loading, error, data kosong, dan validasi harus dibuat konsisten dengan design system yang disetujui.

Setiap halaman wajib menjalani pemeriksaan visual terhadap prototype.

Targetnya adalah kesesuaian visual setinggi mungkin, bukan sekadar kemiripan tema.

# BAB 4 — ARSITEKTUR SISTEM

Menggunakan Modular PHP Native dengan pemisahan tanggung jawab.

Lapisan sistem:

1. Presentation Layer.
2. Controller Layer.
3. Application/Service Layer.
4. Repository/Data Access Layer.
5. Database Layer.

Standar implementasi:

- PDO untuk koneksi database.
- Prepared statements.
- Session management.
- Middleware autentikasi dan otorisasi.
- Validasi server-side.
- CSRF protection.
- Password hashing.
- Audit logging.

# BAB 5 — STRUKTUR DIREKTORI

```text
camhub/
│
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Repositories/
│   ├── Services/
│   ├── Middleware/
│   ├── Validators/
│   └── Helpers/
│
├── config/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── schema.sql
│
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
│       ├── css/
│       ├── js/
│       ├── images/
│       └── fonts/
│
├── resources/
│   └── views/
│       ├── layouts/
│       ├── components/
│       ├── auth/
│       ├── home/
│       ├── products/
│       ├── booking/
│       ├── customer/
│       ├── partner/
│       ├── admin/
│       └── kyc/
│
├── routes/
│
├── storage/
│   ├── logs/
│   ├── uploads/
│   └── cache/
│
├── tests/
│   ├── Unit/
│   ├── Integration/
│   └── Manual/
│
├── docs/
│   ├── mockups/
│   ├── arsitektur/
│   ├── database/
│   ├── pengujian/
│   └── progres/
│
├── .env.example
├── .gitignore
├── README.md
└── IMPLEMENTATION_PLAN.md
```

Struktur dibuat secara bertahap. Tidak perlu membuat seluruh file kosong pada tahap pertama.

# BAB 6 — STRATEGI GITHUB

Repository:

https://github.com/useripx/camhub.git

## 6.1 Branch permanen

- `main`: versi stabil dan telah disetujui.
- `develop`: integrasi seluruh hasil pengembangan.

## 6.2 Branch tahapan

```text
tahap/01-persiapan-fondasi
tahap/02-perancangan-database
tahap/03-autentikasi-pengguna
tahap/04-profil-pelanggan-kyc
tahap/05-website-publik-katalog
tahap/06-dashboard-mitra
tahap/07-penyewaan-pemesanan
tahap/08-marketplace-jual-beli
tahap/09-sistem-titip-jual
tahap/10-pembayaran-escrow
tahap/11-dashboard-administrator
tahap/12-notifikasi-pelaporan
tahap/13-pengujian-penerapan
```

## 6.3 Alur Git

```text
main
  |
  +--- develop
          |
          +--- tahap/01-persiapan-fondasi
          |
          +--- tahap/02-perancangan-database
          |
          +--- tahap/03-autentikasi-pengguna
          |
          +--- tahap/04-profil-pelanggan-kyc
          |
          +--- tahap/...
```

Aturan:

1. Setiap tahap dibuat dari develop terbaru.
2. Pengerjaan dilakukan pada branch tahap.
3. Setiap perubahan dicatat melalui commit.
4. Setiap tahap diuji.
5. Buat Pull Request menuju develop.
6. Review perubahan.
7. Integrasikan hasil yang disetujui.
8. main hanya diperbarui melalui release yang sudah diperiksa.

Branch tahap dipertahankan sebagai riwayat perkembangan yang nyata.

Tidak boleh membuat commit palsu atau memanipulasi tanggal pengerjaan.

# BAB 7 — RENCANA IMPLEMENTASI PER TAHAP

## TAHAP 01 — PERSIAPAN FONDASI

Branch: `tahap/01-persiapan-fondasi`

Pekerjaan:

- Memeriksa repository GitHub.
- Menghubungkan local project dengan remote.
- Membuat branch permanen.
- Menyiapkan folder proyek.
- Menyiapkan konfigurasi PHP.
- Menyiapkan entry point.
- Menyiapkan routing awal.
- Menyiapkan konfigurasi lokal.
- Menyiapkan README.
- Menyimpan mockup dalam dokumentasi.

Hasil:

- Aplikasi berjalan di Laragon.
- GitHub terhubung.
- Struktur dasar tersedia.

## TAHAP 02 — PERANCANGAN DATABASE

Branch: `tahap/02-perancangan-database`

Pekerjaan:

- Analisis entitas.
- Penyusunan ERD.
- Penentuan relasi.
- Penentuan primary key dan foreign key.
- Penentuan tipe data.
- Penentuan indeks.
- Pembuatan database MySQL.
- Pembuatan koneksi PDO.
- Pembuatan schema awal.
- Dokumentasi database.

Entitas awal yang perlu dianalisis:

- Users.
- Roles.
- User Profiles.
- Partners.
- Partner Stores.
- Categories.
- Products.
- Product Images.
- Product Units.
- Rental Pricing.
- Rental Bookings.
- Sales Orders.
- Sales Order Items.
- Consignments.
- KYC Submissions.
- Payments.
- Payment Transactions.
- Escrow Transactions.
- Notifications.
- Audit Logs.

Hasil:

- ERD.
- Database MySQL.
- Koneksi PDO.
- Dokumentasi struktur database.

Catatan: tahap ini menyelesaikan rancangan database menyeluruh. Pembuatan tabel tambahan atau perubahan skema selanjutnya wajib melalui migration yang terdokumentasi.

## TAHAP 03 — AUTENTIKASI PENGGUNA

Branch: `tahap/03-autentikasi-pengguna`

Mengacu pada mockup Login & Register.

Pekerjaan:

- Registrasi.
- Login.
- Logout.
- Password hashing.
- Session management.
- Role-based access control.
- Validasi form.
- Proteksi halaman.

Hasil:

Customer, Partner, dan Administrator dapat melakukan autentikasi sesuai hak aksesnya.

## TAHAP 04 — PROFIL PELANGGAN DAN KYC

Branch: `tahap/04-profil-pelanggan-kyc`

Mengacu pada mockup Dashboard Pelanggan dan KYC.

Pekerjaan:

- Profil pelanggan.
- Edit profil.
- Pengajuan KYC.
- Upload dokumen.
- Status verifikasi.
- Riwayat pengajuan.
- Validasi dokumen.

Hasil:

Sistem profil dan pengajuan KYC.

Persetujuan administratif KYC dapat diselesaikan pada tahap 11.

## TAHAP 05 — WEBSITE PUBLIK DAN KATALOG

Branch: `tahap/05-website-publik-katalog`

Mengacu pada mockup Beranda, Katalog, dan Detail Produk.

Pekerjaan:

- Homepage.
- Katalog produk.
- Pencarian.
- Filter.
- Detail produk.
- Galeri gambar.
- Informasi mitra.
- Informasi harga sewa dan jual.

Hasil:

Website publik yang terhubung dengan MySQL.

## TAHAP 06 — DASHBOARD MITRA

Branch: `tahap/06-dashboard-mitra`

Mengacu pada mockup Dashboard Mitra Lapak.

Pekerjaan:

- Dashboard mitra.
- Manajemen lapak.
- Pengelolaan produk.
- Upload gambar.
- Pengelolaan unit.
- Pengaturan harga.
- Pengelolaan ketersediaan.
- Ringkasan transaksi.

Hasil:

Mitra dapat mengelola lapaknya sendiri.

## TAHAP 07 — PENYEWAAN DAN PEMESANAN

Branch: `tahap/07-penyewaan-pemesanan`

Mengacu pada mockup Booking & Checkout.

Pekerjaan:

- Pemilihan kamera.
- Pemilihan tanggal.
- Durasi sewa.
- Pemeriksaan ketersediaan.
- Perhitungan harga.
- Booking.
- Status penyewaan.
- Pengembalian kamera.
- Riwayat penyewaan.

Hasil:

Sistem rental kamera.

Syarat penting:

Pencegahan bentrokan jadwal harus dilakukan pada tingkat database dan business logic.

## TAHAP 08 — MARKETPLACE JUAL BELI

Branch: `tahap/08-marketplace-jual-beli`

Pekerjaan:

- Penjualan produk.
- Keranjang belanja.
- Checkout.
- Pembuatan pesanan.
- Pengelolaan stok.
- Riwayat pesanan.
- Status pesanan.
- Pengelolaan pesanan mitra.

Hasil:

Sistem jual beli kamera.

## TAHAP 09 — SISTEM TITIP JUAL

Branch: `tahap/09-sistem-titip-jual`

Pekerjaan:

- Pengajuan titip jual.
- Informasi barang.
- Pemeriksaan produk.
- Persetujuan mitra.
- Penentuan komisi.
- Status penitipan.
- Penjualan produk titipan.
- Riwayat hasil penjualan.

Hasil:

Sistem consignment atau titip jual.

## TAHAP 10 — PEMBAYARAN DAN ESCROW

Branch: `tahap/10-pembayaran-escrow`

Pekerjaan:

- Integrasi payment gateway.
- Pencatatan pembayaran.
- Webhook.
- Verifikasi transaksi.
- Refund.
- Pencatatan dana tertahan.
- Pelepasan dana.
- Riwayat keuangan.

Hasil:

Sistem pembayaran dan pencatatan dana.

Ketentuan:

Escrow produksi harus menggunakan mekanisme yang diizinkan penyedia pembayaran dan ketentuan hukum yang berlaku. Jangan mengklaim saldo internal aplikasi sebagai dana escrow yang sah tanpa dukungan layanan yang sesuai.

## TAHAP 11 — DASHBOARD ADMINISTRATOR

Branch: `tahap/11-dashboard-administrator`

Mengacu pada mockup Dashboard Admin.

Pekerjaan:

- Dashboard statistik.
- Manajemen pengguna.
- Manajemen mitra.
- Verifikasi KYC.
- Manajemen produk.
- Pemantauan transaksi.
- Pemantauan pembayaran.
- Pengawasan dana.
- Pengaturan sistem.
- Audit log.

Hasil:

Dashboard administrator.

## TAHAP 12 — NOTIFIKASI DAN PELAPORAN

Branch: `tahap/12-notifikasi-pelaporan`

Pekerjaan:

- Notifikasi booking.
- Notifikasi pembayaran.
- Notifikasi KYC.
- Notifikasi pesanan.
- Notifikasi titip jual.
- Laporan penyewaan.
- Laporan penjualan.
- Laporan mitra.
- Statistik transaksi.

Hasil:

Sistem notifikasi dan pelaporan.

## TAHAP 13 — PENGUJIAN DAN PENERAPAN

Branch: `tahap/13-pengujian-penerapan`

Pekerjaan:

- Functional testing.
- Integration testing.
- Database testing.
- Security testing.
- UI consistency testing.
- Responsive testing.
- Perbaikan bug.
- Dokumentasi deployment.

Hasil:

Aplikasi siap dipresentasikan dan dipersiapkan untuk penerapan.

# BAB 8 — STANDAR KEAMANAN

Wajib menerapkan:

- PDO prepared statements.
- Password hashing.
- CSRF protection.
- XSS prevention.
- Session security.
- Role-based authorization.
- Validasi upload file.
- Perlindungan dokumen KYC.
- Audit log.
- Validasi webhook.
- Database transaction.
- Pengelolaan konfigurasi rahasia.

# BAB 9 — DOKUMENTASI PROGRES

Dokumentasi wajib:

```text
docs/
├── mockups/
├── arsitektur/
├── database/
├── pengujian/
└── progres/
    ├── Status_Pekerjaan.md
    ├── Riwayat_Keputusan.md
    ├── Riwayat_Tahapan.md
    └── Laporan_Pengujian.md
```

Setiap tahap wajib mencatat:

1. Tujuan.
2. Ruang lingkup.
3. Daftar pekerjaan.
4. File yang dibuat.
5. File yang diubah.
6. Perubahan database.
7. Hasil pengujian.
8. Bukti screenshot.
9. Commit hash.
10. Status GitHub.
11. Kendala.
12. Persetujuan Project Owner.

# BAB 10 — TAHAPAN PERSETUJUAN

Setiap tahap wajib melewati lima gerbang:

**Gerbang 1: Perencanaan**

Ruang lingkup dan task breakdown disetujui.

**Gerbang 2: Implementasi**

Kode dibuat sesuai rencana dan mockup.

**Gerbang 3: Pengujian**

Seluruh fungsi diuji dan hasilnya dicatat.

**Gerbang 4: Bukti**

Screenshot, commit, dan dokumentasi tersedia.

**Gerbang 5: Persetujuan**

Project Owner meninjau dan menyetujui hasil.

Prinsip utama:

NO PLAN, NO CODE.

NO APPROVAL, NO CODE.

NO EVIDENCE, NO COMPLETION.

# BAB 11 — INSTRUKSI AI CODING AGENT

AI Coding Agent wajib:

1. Membaca Implementation Plan.
2. Memeriksa kondisi repository.
3. Menggunakan Bahasa Indonesia.
4. Tidak mengerjakan tahap yang belum disetujui.
5. Tidak mengubah mockup.
6. Tidak mengganti teknologi.
7. Tidak menghapus pekerjaan tanpa persetujuan.
8. Tidak mengarang hasil pengujian.
9. Tidak memanipulasi riwayat Git.
10. Melaporkan perubahan setiap tahap.
11. Menunggu persetujuan sebelum melanjutkan.

Format laporan:

```text
LAPORAN IMPLEMENTASI CAMHUB

Tahap:
Nama Tahap:
Branch:
Status:

1. Tujuan
2. Fitur yang Dikerjakan
3. File yang Dibuat
4. File yang Diubah
5. Perubahan Database
6. Hasil Pengujian
7. Kendala
8. Commit Hash
9. Status GitHub
10. Bukti Pengerjaan

Status Akhir:
SELESAI / PERLU PERBAIKAN / TERHAMBAT

Tahap Berikutnya:
MENUNGGU PERSETUJUAN PROJECT OWNER
```

# BAB 12 — KRITERIA KEBERHASILAN

CamHub dinyatakan berhasil apabila:

- Seluruh layanan marketplace berjalan.
- Customer, Partner, dan Administrator memiliki hak akses sesuai peran.
- Sistem rental dapat mengelola ketersediaan kamera.
- Sistem jual beli dapat mengelola stok.
- Sistem titip jual dapat mencatat komisi dan kepemilikan barang.
- Pembayaran terintegrasi dengan benar.
- Pencatatan dana dapat diaudit.
- KYC dapat diproses.
- Tampilan sesuai prototype.
- Sistem telah melalui pengujian.
- Repository GitHub memiliki riwayat pengembangan yang dapat diverifikasi.
- Dokumentasi tersedia.

---

**AKHIR DOKUMEN IMPLEMENTATION PLAN CAMHUB**

Status: DRAFT — MENUNGGU PERSETUJUAN PROJECT OWNER.
