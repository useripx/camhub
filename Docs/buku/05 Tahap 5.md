# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 05: WEBSITE PUBLIK DAN KATALOG PRODUK

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner / Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI BUKU:** [`Docs/buku/05 Tahap 5.md`](05%20Tahap%205.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/05.implementation_plan_Tahap5.md`](../tahapan/05.implementation_plan_Tahap5.md)  
> **ACUAN MOCKUP:**  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Beranda.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Beranda.png)  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Katalog Produk.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Katalog%20Produk.png)  
> - [`template/CamHub — Interactive UI UX Prototype/CamHub — Detail Produk.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Detail%20Produk.png)  
> **TABEL DATABASE:** `products`, `product_images`, `product_units`, `categories`, `partner_stores`, `rental_pricing`, `reviews`

---

# BAGIAN 1 — PANDUAN SETUP & PENGUJIAN FITUR KATALOG TIM (STEP-BY-STEP)

Panduan langkah demi langkah bagi seluruh anggota Kelompok 1 untuk menjalankan dan mendemokan fitur Website Publik dan Katalog Produk di laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **Apache** dan **MySQL** di Laragon aktif.
- Basis data `camhub_db` telah dimigrasi dan di-seed data kamera (`php database/seeders/initial_seed.php`).

---

### 2. Langkah-Langkah Menjalankan di Laptop Masing-Masing

#### Langkah 1: Pindah ke Branch Tahap 05
Buka terminal di root project (`C:\laragon\www\camhub`):
```bash
git checkout tahap/05-website-publik-katalog
```

#### Langkah 2: Jalankan Pengujian Otomatis Mandiri (100% PASS Gate)
Jalankan test runner modul katalog:
```bash
php tests/test_catalog.php
```
*Hasil yang diharapkan:*
```text
========================================================
   CAMHUB TEST SUITE — TAHAP 05: KATALOG & DETAIL PRODUK 
========================================================
 [PASS] TEST-CAT-01: Pembacaan Master Kategori Fotografi & Hitungan Produk Aktif
 [PASS] TEST-CAT-02: Pengambilan Produk Unggulan Beranda (Populer Disewa) dengan Relasi Toko
 [PASS] TEST-CAT-03: Fungsionalitas Pencarian Kata Kunci Kamera (Search Keyword)
 [PASS] TEST-CAT-04: Filter Katalog Berdasarkan Kategori (Category Slug Filtering)
 [PASS] TEST-CAT-05: Filter Katalog Berdasarkan Rentang Harga Sewa Harian (Min & Max Price)
 [PASS] TEST-CAT-06: Pengurutan Produk (Sorting): Harga Termurah & Termahal
 [PASS] TEST-CAT-07: Fungsionalitas Pagination Katalog (Limit, Offset, Total Pages)
 [PASS] TEST-CAT-08: Pengambilan Detail Produk Berdasarkan Slug beserta Galeri & Toko
 [PASS] TEST-CAT-09: Pemeriksaan Ketersediaan Unit Fisik Kamera (Units Availability Status)
 [PASS] TEST-CAT-10: Kalkulasi Logika Tarif Sewa & Diskon Durasi Bertingkat + Escrow
========================================================
HASIL PENGUJIAN TAHAP 05: 10/10 BERHASIL (100% PASS)
========================================================
```

#### Langkah 3: Menjalankan Seluruh Uji Regresi Platform (56 / 56 PASS)
Pastikan tidak ada regresi pada modul fondasi, basis data, autentikasi, KYC, dan peramban:
```bash
php tests/test_foundation.php; php tests/test_database.php; php tests/test_auth.php; php tests/test_kyc.php; php tests/test_browser_e2e.php; php tests/test_catalog.php
```

#### Langkah 4: Uji Coba Langsung di Browser
Buka browser (Google Chrome) dan akses URL berikut:
1. **Beranda Publik:** `http://localhost/camhub/public/`  
   *Perhatikan banner hero, kategori kamera, kartu produk sewa terpopuler, dan footer identitas Kelompok 1.*
2. **Katalog Produk & Filter:** `http://localhost/camhub/public/katalog`  
   *Uji fitur pencarian nama kamera (misal: "Sony"), filter kategori, filter tarif harian, dan sorting harga.*
3. **Detail Produk & Kalkulator Sewa:** `http://localhost/camhub/public/produk/canon-eos-r50`  
   *Klik chip pilihan durasi (1 Hari, 3 Hari - Diskon 5%, 7 Hari - Diskon 10%, 14 Hari - Diskon 15%), perhatikan perhitungan subtotal, diskon, biaya escrow Rp 5.000, dan tombol booking yang terbarui otomatis.*

---

# BAGIAN 2 — NASKAH PRESENTASI RESMI KELOMPOK 1 (TAHAP 05)

Naskah ini disusun terstruktur untuk 4 pembicara secara berurutan, siap dipraktikkan di hadapan dosen penguji.

```
+-----------------------------------------------------------------------------------------+
| URUTAN PEMBICARA PRESENTASI TAHAP 05:                                                   |
| 1. Yogi Ario Pratama            -> Pembukaan, Visi Etalase Publik & Demo Mockup Visual |
| 2. Muhammad Regieyanto          -> Arsitektur Repositori SQL, Query Filter & Performa   |
| 3. Ivan Farrel Nanda Kristanto  -> Clean Routing, Logika Bisnis & Kalkulator Sewa       |
| 4. Krisna Mukti                 -> Pengujian Otomatis 10 DoD, Uji Regresi & Penutup     |
+-----------------------------------------------------------------------------------------+
```

---

### Pembicara 1: Yogi Ario Pratama (Project Owner & Lead Developer)
**Peran:** Membuka presentasi, menjelaskan tujuan bisnis Tahap 05, dan mendemokan kesesuaian antarmuka dengan 3 prototipe acuan.

> *"Selamat pagi/siang kepada Bapak/Ibu Dosen Penguji dan rekan-rekan sekalian.*
> 
> *Saya **Yogi Ario Pratama**, mewakili Kelompok 1 sebagai Project Owner dan Lead Developer proyek platform **CamHub – Camera Marketplace Platform**.*
> 
> *Setelah pada empat tahap sebelumnya kami sukses meletakkan fondasi modular, merancang 22 tabel relasional MySQL, membangun sistem autentikasi multi-peran, serta mengamankan identitas penyewa melalui verifikasi KYC, pada hari ini kami dengan bangga mempresentasikan pencapaian **Tahap 05: Website Publik dan Katalog Produk**.*
> 
> *Tahap 05 adalah etalase utama yang menghubungkan calon penyewa dengan seluruh inventaris kamera di platform kami. Pada tahap ini, kami mengimplementasikan 3 halaman inti yang 100% presisi mengacu pada berkas prototipe desain yang telah disetujui:*
> 1. *Pertama, **Halaman Beranda (`/`)**: Menghadirkan hero banner bernuansa Dark Navy dengan tagline 'Sewa kamera, abadikan cerita', kotak pencarian cepat lintas kategori, grid kategori fotografi interaktif, serta etalase kamera terpopuler disewa yang diambil langsung dari database.*
> 2. *Kedua, **Halaman Katalog (`/katalog`)**: Menyediakan navigasi penjelajahan komprehensif dengan bilah filter multidimensi di sisi kiri dan grid kartu produk responsif di sisi kanan lengkap dengan informasi tarif sewa harian dan rating ulasan.*
> 3. *Ketiga, **Halaman Detail Produk (`/produk/{slug}`)**: Menampilkan galeri visual interaktif, spesifikasi teknis unit, tab kelengkapan sewa, data toko mitra terverifikasi, serta kartu kalkulator estimasi sewa.*
> 
> *Selanjutnya, rekan saya Muhammad Regieyanto akan membedah arsitektur data dan strategi kueri dinamis yang menopang katalog ini. Kepada Regi, saya persilakan."*

---

### Pembicara 2: Muhammad Regieyanto (Backend & System Architect)
**Peran:** Menjelaskan desain arsitektur data, relasi tabel `products`, `partner_stores`, `categories`, dan optimasi kueri filter dinamis.

> *"Terima kasih kepada Saudara Yogi. Salam sejahtera bagi kita semua, perkenalkan saya **Muhammad Regieyanto**, Backend & System Architect Kelompok 1.*
> 
> *Tantangan teknis utama dalam membangun katalog marketplace adalah efisiensi pemrosesan data ketika pengguna menerapkan banyak filter secara bersamaan—mulai dari pencarian kata kunci, kategori, tipe sewa atau beli, rentang harga, hingga pengurutan data.*
> 
> *Pada Tahap 05 ini, kami menerapkan arsitektur repository pattern melalui kelas `ProductRepository` dan `CategoryRepository` murni menggunakan PDO PHP Native tanpa framework:*
> 1. *Untuk kueri beranda dan katalog, kami melakukan relasi `JOIN` terindeks antara tabel `products`, `categories`, dan `partner_stores`. Tarif sewa harian dihitung secara modular dari subquery `rental_pricing` untuk durasi 1 hari.*
> 2. *Pada metode `getCatalogFiltered()`, penyusunan klausul `WHERE` dilakukan secara modular berbasis array prepared statement. Parameter kata kunci diamankan dengan binding `:kw1` dan `:kw2`, kategori dengan `:cat_id` atau `:cat_slug`, dan rentang harga melalui evaluasi HAVING.*
> 3. *Paginasi katalog diatur secara presisi dengan klausul `LIMIT :limit OFFSET :offset`, di mana total data dihitung efisien melalui kueri pembungkus `COUNT(*)`.*
> 4. *Seluruh data yang disajikan terhubung langsung dengan 13 unit fisik terdaftar pada tabel `product_units`, sehingga ketersediaan kamera di lapak dapat dipantau secara real-time.*
> 
> *Berikutnya, arsitektur routing bersih dan logika kalkulator estimasi sewa akan dijelaskan oleh rekan saya, Ivan Farrel. Silakan Ivan."*

---

### Pembicara 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
**Peran:** Memaparkan konfigurasi rute web, sanitasi parameter, dan logika bisnis kalkulasi sewa bertingkat.

> *"Terima kasih Saudara Regi. Selamat pagi/siang Bapak/Ibu Dosen, nama saya **Ivan Farrel Nanda Kristanto**, bertindak sebagai Core Routing & Security Engineer Kelompok 1.*
> 
> *Fokus saya pada Tahap 05 berpusat pada dua aspek krusial: perutean URL yang ramah SEO (*Clean SEO-Friendly URLs*) dan integritas kalkulasi finansial tarif sewa:*
> 1. *Pada berkas `routes/web.php`, kami mendaftarkan rute publik bersih:*
>    - *Rute root `/` ditangani oleh `HomeController@index`.*
>    - *Rute katalog `/katalog` ditangani oleh `CatalogController@index`.*
>    - *Rute detail produk `/produk/{slug}` ditangani oleh `ProductDetailController@show` menggunakan wildcard slug dinamis yang diekstrak oleh router native buatan kami.*
> 2. *Dari segi keamanan, seluruh parameter query string yang masuk dari formulir filter melalui proses sanitasi ketat di `ProductCatalogService` menggunakan `htmlspecialchars()` dan casting tipe numerik untuk mencegah serangan Cross-Site Scripting (XSS).*
> 3. *Untuk logika pemesanan, kami merancang `RentalCalculatorService`:*
>    - *Pengguna dapat memilih durasi cepat atau memilih tanggal ambil dan tanggal kembali.*
>    - *Sistem menerapkan aturan diskon durasi sewa bertingkat: sewa 3 hari ke atas mendapat diskon 5%, sewa 7 hari ke atas mendapat diskon 10%, dan sewa 14 hari ke atas mendapat diskon 15%.*
>    - *Di akhir, biaya layanan rekening bersama escrow sebesar Rp 5.000 ditambahkan secara transparan ke dalam total estimasi biaya.*
>    - *Logika ini diimplementasikan ganda: di sisi frontend menggunakan JavaScript interaktif untuk kenyamanan visual calon penyewa, dan di sisi backend untuk validasi sebelum pemesanan disimpan.*
> 
> *Selanjutnya, rekan kami Krisna Mukti akan memaparkan bukti pengujian otomatis dan hasil verifikasi kualitas sistem. Kepada Krisna, dipersilakan."*

---

### Pembicara 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
**Peran:** Memaparkan metodologi pengujian, laporan eksekusi 10 DoD Tahap 05, verifikasi uji regresi 56/56 PASS, dan menutup presentasi.

> *"Terima kasih Saudara Ivan. Salam hormat kepada Bapak/Ibu Dosen Penguji, perkenalkan saya **Krisna Mukti**, Quality Assurance & Presentation Coordinator Kelompok 1.*
> 
> *Sesuai prinsip utama tata kelola proyek kami: 'No Approval, No Code. No Evidence, No Completion', setiap tahapan wajib dibuktikan dengan pengujian otomatis yang dapat diverifikasi ulang kapan saja.*
> 
> *Untuk Tahap 05, kami telah menyusun skrip pengujian otomatis mandiri di `tests/test_catalog.php` yang mencakup **10 parameter Definition of Done (DoD)**:*
> - *Mulai dari pengujian pembacaan master kategori, kueri produk unggulan beranda, pencarian kata kunci, filter kategori dan harga, sorting multi-kriteria, pembagian halaman paginasi, pembacaan detail produk slug, ketersediaan unit fisik, hingga ketepatan matematis kalkulator diskon durasi dan biaya escrow.*
> - *Hasil eksekusi `php tests/test_catalog.php` menunjukkan angka sempurna: **10 dari 10 pengujian LULUS (100% PASS)**.*
> 
> *Lebih dari itu, kami juga menjalankan uji regresi menyeluruh dari Tahap 01 hingga Tahap 05:*
> - *Tahap 01 Fondasi: 7/7 PASS*
> - *Tahap 02 Database 22 Tabel: 7/7 PASS*
> - *Tahap 03 Autentikasi & RBAC: 10/10 PASS*
> - *Tahap 04 Profil Pelanggan & KYC: 10/10 PASS*
> - *Uji Peramban Otomatis (E2E Browser): 12/12 PASS*
> - *Tahap 05 Katalog & Detail Produk: 10/10 PASS*
> - *Sehingga total ada **56 skenario pengujian aktif** di platform CamHub dengan tingkat kelulusan **100% PASS tanpa satupun error**.*
> 
> *Demikian presentasi dari Kelompok 1 untuk Tahap 05. Kami siap membuka sesi tanya jawab dan mendemokan fitur interaktif secara langsung kepada Bapak/Ibu Dosen Penguji. Terima kasih."*

---

# BAGIAN 3 — SIMULASI TANYA JAWAB DOSEN PENGUJI (Q&A)

Berikut panduan jawaban atas pertanyaan yang paling sering diajukan dosen mengenai fitur katalog dan pencarian produk:

#### Pertanyaan 1: "Bagaimana cara sistem mencegah SQL Injection pada fitur pencarian dan filter rentang harga katalog?"
**Jawaban (dijawab oleh Muhammad Regieyanto):**
> *"Kami tidak pernah menggabungkan variabel input mentah secara langsung ke dalam string SQL (*tidak ada string concatenation*). Seluruh parameter pencarian (`q`), kategori (`category`), dan harga (`min_price`, `max_price`) diikat menggunakan **PDO Prepared Statements** dengan parameter named placeholders (seperti `:kw1`, `:cat_id`, `:min_price`). Selain itu, input teks telah disanitasi menggunakan `htmlspecialchars()` dan nilai numerik di-casting secara eksplisit menggunakan `(float)` atau `(int)`."*

#### Pertanyaan 2: "Mengapa kalkulator estimasi sewa perlu diimplementasikan di dua sisi (JavaScript dan PHP Service)?"
**Jawaban (dijawab oleh Ivan Farrel Nanda Kristanto):**
> *"Di sisi frontend (JavaScript), kalkulasi interaktif diperlukan untuk User Experience (UX) yang responsif, sehingga calon penyewa langsung melihat perubahan subtotal dan diskon saat mengganti durasi hari tanpa reload halaman. Namun, dari sisi keamanan, input frontend tidak boleh dipercaya secara mentah. Oleh karena itu, kami menyediakan `RentalCalculatorService` di backend yang memvalidasi ulang seluruh perhitungan tarif saat formulir booking disubmit, sehingga manipulasi harga via inspect element tidak akan berdampak pada nilai transaksi di database."*

#### Pertanyaan 3: "Bagaimana sistem menjamin bahwa unit kamera yang tampil di detail produk benar-benar tersedia secara fisik?"
**Jawaban (dijawab oleh Yogi Ario Pratama):**
> *"Pada tabel `product_units`, setiap unit fisik memiliki nomor seri (*serial number*) unik dan kolom status ketersediaan (`availability_status`). Saat halaman detail dibuka, sistem mengeksekusi `countAvailableUnits()` yang hanya menghitung unit fisik yang berstatus `'available'`. Jika seluruh unit fisik suatu kamera sedang disewa, status lencana pada antarmuka secara otomatis berubah menjadi 'Sedang Disewa' dan tombol sewa dibatasi demi mencegah bentrokan jadwal sewa."*
