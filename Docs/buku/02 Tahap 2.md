# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 02: PERANCANGAN DATABASE

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner / Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>
> **LOKASI BUKU:** [`Docs/buku/02 Tahap 2.md`](02%20Tahap%202.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/02.implementation_plan_Tahap2.md`](../tahapan/02.implementation_plan_Tahap2.md)  
> **DIAGRAM ERD LENGKAP:** [`Docs/scema/ERD.md`](../scema/ERD.md)  
> **KAMUS DATA RESMI:** [`Docs/scema/database_dictionary.md`](../scema/database_dictionary.md)

---

# BAGIAN 1 — PANDUAN SETUP BASIS DATA TIM (STEP-BY-STEP)

Panduan langkah demi langkah bagi seluruh anggota Kelompok 1 untuk menginisialisasi basis data MySQL di server Laragon laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **MySQL** di Laragon aktif pada port **3306**.
- Berkas `.env` telah terkonfigurasi (`DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_NAME=camhub_db`, `DB_USER=root`, `DB_PASS=`).

---

### 2. Langkah-Langkah Inisialisasi Basis Data

#### Langkah 1: Masuk ke Branch Tahap 02
Buka terminal di `C:\laragon\www\camhub` dan jalankan:
```bash
git checkout tahap/02-perancangan-database
```

#### Langkah 2: Eksekusi Migrasi DDL (Otomatis Membuat 22 Tabel)
Jalankan runner migrasi mandiri CamHub:
```bash
php database/migrate.php
```
*Hasil yang diharapkan:*
- Database `camhub_db` dibuat otomatis jika belum ada.
- Seluruh berkas `database/schema.sql` dieksekusi.
- Terminal menampilkan output:
```text
STATUS: SUKSES (22 / 22 TABEL TERVERIFIKASI)
```

#### Langkah 3: Eksekusi Seeder Data Awal & Mockup
Jalankan pengisian data awal untuk mengisi data master dan kamera prototype:
```bash
php database/seeders/initial_seed.php
```
*Data yang otomatis terisi:*
1. 3 Peran: `admin`, `partner`, `customer`.
2. 5 Kategori: *Kamera Body, Lensa, Audio, Lighting, Aksesoris*.
3. Akun Administrator (`admin@camhub.id`), Akun Mitra (`yogi@camerastore.id` / Toko: "Yogi Camera"), dan Akun Customer (`regi@camhub.id` / Nama: "Mohammad Regieyanto"). Password default: `password123`.
4. 6 Kamera acuan mockup prototype lengkap dengan 13 unit fisik dan 24 skema harga sewa harian.

#### Langkah 4: Menjalankan Pengujian Basis Data (Wajib 100% PASS)
Jalankan test suite otomatis basis data:
```bash
php tests/test_database.php
```
*Hasil yang diharapkan:*
```text
HASIL PENGUJIAN: 7 / 7 LULUS (100%)
STATUS: 100% PASS (0 GAGAL / 0 ERROR) — VERIFIKASI SELESAI
```

#### Langkah 5: Memeriksa Basis Data via phpMyAdmin (Opsional)
Buka browser ke [http://localhost/phpmyadmin](http://localhost/phpmyadmin), pilih basis data `camhub_db`, dan periksa struktur 22 tabel serta relasi *Designer*-nya.

---

# BAGIAN 2 — PENJELASAN TEKNIS ARSITEKTUR BASIS DATA (22 TABEL)

### 1. Pembagian 8 Domain Relasional
Basis data CamHub dirancang tidak hanya untuk menampilkan data, melainkan memproses transaksi nyata secara aman:
1. **Otoritas & Identitas:** `roles`, `users`, `user_profiles`, `kyc_submissions` (mendukung verifikasi KTP/Selfie).
2. **Kemitraan Toko:** `partners`, `partner_stores` (mendukung mitra lapak independen).
3. **Katalog & Inventaris:** `categories`, `products`, `product_images`, `product_units`, `rental_pricing` (mendukung unit fisik bernomor seri unik).
4. **Transaksi Rental:** `rental_bookings` (menyimpan tanggal mulai, selesai, durasi, dan biaya admin).
5. **Transaksi Jual Beli:** `sales_orders`, `sales_order_items` (manajemen stok kamera).
6. **Penitipan Jual (Consignment):** `consignments` (alur komisi titip jual kamera).
7. **Keuangan & Rekening Bersama:** `payments`, `payment_transactions`, `escrow_transactions` (penahanan saldo sewa).
8. **Operasional & Audit:** `fines` (denda), `inspections` (inspeksi fisik unit), `notifications`, `audit_logs`.

### 2. Aturan Emas Anti-Bentrokan Jadwal Sewa (Rental Locking Rule)
Untuk mencegah unit fisik kamera yang sama disewa oleh dua orang di waktu bertubrukan:
$$\text{Overlap: } (\text{Start}_{\text{Baru}} \le \text{End}_{\text{Lama}}) \land (\text{End}_{\text{Baru}} \ge \text{Start}_{\text{Lama}})$$
Sistem menerapkan query *Pessimistic Locking* di dalam transaksi database:
```sql
SELECT id FROM rental_bookings 
WHERE product_unit_id = :unit_id 
  AND rental_status IN ('confirmed', 'active', 'pending_payment')
  AND NOT (end_date < :new_start OR start_date > :new_end)
FOR UPDATE;
```

### 3. Koneksi PDO Singleton & Standar Moneter
- Koneksi diatur terpusat di [`app/Core/Database.php`](../../app/Core/Database.php) menggunakan pola Singleton, menghemat penggunaan memori koneksi MySQL.
- Seluruh kolom nominal uang menggunakan `DECIMAL(15, 2)` guna mencegah galat presisi pecahan desimal (*floating-point issue*).

---

# BAGIAN 3 — SCRIPT PRESENTASI KELOMPOK 1 (TAHAP 2)

```text
DURASI TOTAL: ± 8-10 Menit
ALAT BANTU: Proyektor / Screen Share Terminal & phpMyAdmin / DBeaver
SLIDE / TAMPILAN:
1. Slide Judul & Anggota Kelompok 1
2. Diagram ERD 22 Entitas CamHub (Mermaid Diagram)
3. Demo Terminal: Eksekusi php database/migrate.php & initial_seed.php
4. Demo Uji: Eksekusi php tests/test_database.php (100% PASS)
```

---

### 🎙 PEMBICARA 1: Yogi Ario Pratama (Project Owner & Lead Developer)
**Waktu:** 2 Menit  
**Fokus:** Pembukaan, Gambaran Umum 22 Entitas, dan Alur Bisnis Data

> *"Selamat pagi/siang rekan-rekan dan Bapak/Ibu dosen penguji.*  
>
> *Melanjutkan keberhasilan Tahap 01, hari ini kami dari **Kelompok 1** yang beranggotakan saya sendiri **Yogi Ario Pratama**, bersama rekan saya **Muhammad Regieyanto**, **Ivan Farrel Nanda Kristanto**, dan **Krisna Mukti**, mempersembahkan hasil implementasi **Tahap 02: Perancangan & Implementasi Basis Data MySQL**.*  
>
> *Di tahap kedua ini, kami mewujudkan fondasi data nyata bagi platform CamHub. Kami tidak hanya membuat tabel sederhana, melainkan membangun arsitektur relasional komprehensif yang mencakup **22 entitas data**.*  
>
> *Ke-22 entitas ini dirancang presisi untuk memfasilitasi 4 model bisnis utama CamHub: mulai dari transaksi sewa kamera berbasis tanggal, penjualan produk, sistem titip jual barang bekas atau consignment, hingga pencatatan rekening bersama atau escrow.*  
>
> *Selanjutnya, rekan saya Muhammad Regieyanto akan membedah skema relasi dan aturan integritas data yang telah kami susun."*

---

### 🎙 PEMBICARA 2: Muhammad Regieyanto (Backend & System Architect)
**Waktu:** 2 Menit  
**Fokus:** Skema Relasional, 27 Foreign Keys, dan Pencegahan Double-Booking

> *(Tampilkan diagram ERD dari `Docs/scema/ERD.md`)*  
>
> *"Terima kasih Yogi.*  
>
> *Saya **Muhammad Regieyanto**. Saya akan menjelaskan bagaimana ke-22 entitas ini saling terhubung secara relasional.*  
>
> *Di layar kita melihat diagram ERD resmi CamHub. Terdapat **27 relasi Foreign Key** aktif yang kami kunci dengan constraint referensial ketat menggunakan engine **MySQL InnoDB**.*  
>
> *Salah satu keunggulan desain basis data kami adalah pemisahan antara entitas `products` (katalog umum) dan `product_units` (unit fisik riil). Sebagai contoh, toko 'Yogi Camera' memiliki produk Sony A7 III dengan stok 3 unit. Di basis data, terdapat 3 nomor seri berbeda di tabel `product_units`.*  
>
> *Hal ini krusial untuk fitur rental: sistem kami dapat melacak unit mana yang sedang disewa dan unit mana yang tersedia. Kami telah menyusun aturan indeks gabungan `idx_bookings_dates` dan query penguncian *Pessimistic Locking* `SELECT ... FOR UPDATE` untuk menjamin tidak akan pernah terjadi double-booking atau jadwal sewa yang bentrok.*  
>
> *Bagaimana koneksi dan mekanisme migrasi data ini dieksekusi akan dijelaskan oleh Ivan Farrel."*

---

### 🎙 PEMBICARA 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
**Waktu:** 2 Menit  
**Fokus:** Singleton PDO, Migration CLI Runner, dan Mockup Seeder

> *"Terima kasih Regi.*  
>
> *Saya **Ivan Farrel Nanda Kristanto**. Di sisi implementasi kode backend, kami menyelesaikan 3 komponen inti:*  
>
> *1. **Database Connection Manager (Singleton Pattern):** Di dalam `app/Core/Database.php`, kami membangun pengelola koneksi PDO berbasis Singleton. Desain ini memastikan aplikasi hanya membuka satu koneksi PDO per request, sangat hemat memori, dan mengaktifkan mode strict `PDO::ERRMODE_EXCEPTION` serta menonaktifkan emulasi prepared statements demi keamanan SQL injection tingkat tinggi.*  
>
> *2. **Migration Runner Mandiri:** Alih-alih menginstal library pihak ketiga yang membengkakkan proyek, kami membangun skrip CLI native `database/migrate.php`. Skrip ini mampu membaca DDL `database/schema.sql` dan membangun 22 tabel di Laragon secara otomatis dan idempotensial.*  
>
> *3. **Initial Data Seeder:** Kami menyediakan `database/seeders/initial_seed.php` yang secara otomatis mengisi data master: 3 peran, 5 kategori produk, akun toko mitra 'Yogi Camera', customer 'Regi', serta 6 kamera acuan dari mockup katalog (Sony A7 III, Canon R50, Fujifilm X-T30, dll.) lengkap dengan nomor seri unit dan paket harga sewa harian.*  
>
> *Selanjutnya, pembuktian eksekusi dan uji integritas transaksi ACID akan dipandu oleh Krisna Mukti."*

---

### 🎙 PEMBICARA 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
**Waktu:** 2.5 Menit  
**Fokus:** Demo Terminal Eksekusi, Pembuktian 100% PASS, dan Transaksi Rollback

> *(Membuka terminal dan bersiap menjalankan pengujian)*  
>
> *"Terima kasih Ivan.*  
>
> *Saya **Krisna Mukti** dari bagian Quality Assurance.*  
>
> *Sesuai prinsip pilar kami: **No Evidence, No Completion**, mari kita buktikan integritas basis data ini secara langsung di hadapan Bapak/Ibu dosen.*  
>
> *Pertama, saya jalankan test suite basis data:*  
> `php tests/test_database.php`  
>
> *(Arahkan perhatian audiens ke terminal)*  
> *Dapat kita lihat, sistem menjalankan 7 pengujian otomatis:*  
> 1. *Verifikasi koneksi PDO aktif.*  
> 2. *Pemeriksaan keberadaan 22 tabel lengkap.*  
> 3. *Pemeriksaan 27 foreign key constraints di information_schema.*  
> 4. *Pemeriksaan indeks performa pencarian.*  
> 5. *Integritas data master 3 role dan 5 kategori.*  
> 6. *Ketersediaan data mockup 6 kamera dan 13 unit fisik.*  
> 7. *Uji integritas transaksi ACID: sistem melakukan insert data lalu mengeksekusi `rollBack()` untuk memastikan data uji tidak mencemari basis data.*  
>
> *Hasilnya adalah **7 dari 7 LULUS (100% PASS)**.*  
>
> *Dan untuk membuktikan bahwa Tahap 02 tidak merusak pekerjaan Tahap 01 sebelumnya, saya jalankan uji regresi:*  
> `php tests/test_foundation.php`  
> *Hasilnya tetap **100% PASS**.*  
>
> *Dengan ini, basis data CamHub telah siap 100% untuk digunakan pada pengembangan modul autentikasi dan fungsional bisnis berikutnya. Terima kasih atas perhatiannya, kami menyambut pertanyaan dari Bapak/Ibu penguji."*

---

# BAGIAN 4 — PANDUAN MENJAWAB PERTANYAAN DOSEN (Q&A GUIDE)

| Kemungkinan Pertanyaan Dosen | Rekomendasi Jawaban dari Kelompok 1 |
|---|---|
| **Mengapa menggunakan tipe data `DECIMAL(15,2)` untuk harga, bukan `INT` atau `FLOAT`?** | *"Tipe `FLOAT` atau `DOUBLE` memiliki kelemahan binary floating-point representation yang dapat menghasilkan galat pembulatan sen. Tipe `DECIMAL` menjamin ketepatan nilai eksak hingga dua digit sen, standar baku pada aplikasi e-commerce dan perbankan."* |
| **Bagaimana cara menangani integritas data jika server mati di tengah proses checkout?** | *"Kami memanfaatkan engine InnoDB dengan fitur ACID Database Transactions. Di kelas `App\Core\Database`, kami menyediakan method `transaction(callable)`. Jika terjadi kegagalan sistem sebelum commit, seluruh perubahan otomatis dibatalkan (`rollBack()`), mencegah saldo terpotong tanpa adanya pesanan."* |
| **Apakah tabel `users` dipisah antara pembeli, penyewa, dan mitra?** | *"Tabel `users` kami rancang terpusat dengan relasi ke tabel `roles`. Pengguna yang menjadi mitra akan memiliki data relasional di tabel `partners` dan `partner_stores`. Desain ini memungkinkan satu akun memiliki peran yang fleksibel namun tetap terisolasi otoritasnya."* |
