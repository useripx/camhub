# CamHub — Camera Marketplace Platform

> Platform marketplace web terintegrasi untuk penyewaan, jual beli, dan penitipan penjualan (consignment) kamera serta pengelolaan lapak mitra di Indonesia.

---

## 📌 Identitas Proyek
- **Nama Proyek:** CamHub
- **Project Owner:** Yogi Ario
- **Teknologi Backend:** PHP Native (Modular MVC-inspired Architecture) dengan PDO MySQL
- **Web Server:** Laragon (Apache, MySQL, PHP 8.x)
- **Repository:** [https://github.com/useripx/camhub.git](https://github.com/useripx/camhub.git)
- **Integritas Desain:** Mengacu penuh pada 9 Prototype UI/UX di folder `template/`

---

## 🏛 4 Layanan Utama
1. **Penyewaan Kamera:** Peminjaman unit dengan seleksi tanggal, durasi fleksibel, verifikasi ketersediaan anti-bentrok, dan keamanan pembayaran rekening bersama (escrow).
2. **Jual Beli Kamera:** Marketplace penjualan langsung perlengkapan kamera baru & bekas terverifikasi.
3. **Penitipan Penjualan (Consignment):** Pengguna dapat menitipkan barang ke toko mitra terpercaya dengan skema bagi hasil/komisi transparan.
4. **Pengelolaan Lapak Mitra:** Dashboard manajemen produk, pemantauan pesanan sewa, dan pencairan saldo bagi mitra toko.

---

## 📂 Struktur Direktori

```text
camhub/
├── app/
│   ├── Core/                  # Router native dan engine dasar
│   ├── Controllers/           # Controller penangan request
│   ├── Models/                # Entitas data
│   ├── Repositories/          # Akses basis data via PDO Prepared Statements
│   ├── Services/              # Logika bisnis inti
│   ├── Middleware/            # Guard autentikasi, role guard, dan CSRF
│   ├── Validators/            # Validasi input server-side
│   └── Helpers/               # Fungsi bantuan global (functions.php)
│
├── config/                    # Konfigurasi aplikasi dan database
│   ├── app.php
│   └── database.php
│
├── database/                  # Skema, migrasi, dan seeder MySQL
│   ├── migrations/
│   └── seeders/
│
├── public/                    # Document Root web Laragon
│   ├── index.php              # Front Controller utama
│   ├── .htaccess              # URL Rewrite rules
│   └── assets/                # Aset statis (css, js, images, fonts)
│
├── resources/
│   └── views/                 # Template antarmuka (PHP Native Views)
│
├── routes/
│   └── web.php                # Definisi rute aplikasi
│
├── storage/                   # Logs, uploads, cache (terproteksi .gitignore)
│
├── tests/                     # Test suite otomatis (100% PASS Gate)
│   ├── test_foundation.php
│   ├── Unit/
│   ├── Integration/
│   └── Manual/
│
├── Docs/                      # Dokumentasi tata kelola & rencana proyek
│   ├── aturan/                # universal-rule.md, GEMINI.md, AGENTS.md
│   ├── scema/                 # ERD.md (Diagram relasi 22 entitas)
│   ├── tahapan/               # Implementation plan fix per tahap (01.implementation_plan_Tahap1.md, dst.)
│   ├── Task_Status.md         # Pelacak progres 13 tahapan
│   └── Decision_Log.md        # Catatan keputusan arsitektur
│
├── changelog/                 # [FLAT] Riwayat rilis bertimestamp
│
└── template/                  # [READ-ONLY] 9 Prototype visual mentah UI/UX CamHub
```

---

## 🚀 Panduan Menjalankan di Laragon

1. Buka aplikasi **Laragon**.
2. Pastikan service **Apache** dan **MySQL** berjalan (*Start All*).
3. Buat file `.env` dari template:
   ```bash
   cp .env.example .env
   ```
4. Buka browser dan akses:
   - Jika menggunakan Virtual Host: `http://camhub.test/`
   - Atau melalui subfolder Laragon: `http://localhost/camhub/public/`

---

## 🧪 Menjalankan Pengujian Otomatis

Untuk memastikan seluruh modul berfungsi dengan standar `100% PASS`:
```bash
php tests/test_foundation.php
```

---

*Hak Cipta © 2026 CamHub. Seluruh hak dilindungi undang-undang. Project Owner: Yogi Ario.*
