# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 01: PERSIAPAN FONDASI

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner / Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>
> **LOKASI BUKU:** [`Docs/buku/01 Tahap 1.md`](01%20Tahap%201.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/01.implementation_plan_Tahap1.md`](../tahapan/01.implementation_plan_Tahap1.md)

---

# BAGIAN 1 — PANDUAN SETUP & INSTALASI TIM (STEP-BY-STEP)

Panduan ini disusun agar seluruh anggota Kelompok 1 dapat melakukan setup repositori dan menjalankan aplikasi di laptop masing-masing tanpa kendala.

### 1. Prasyarat Perangkat Lunak (Prerequisites)
Pastikan laptop Anda telah terpasang:
- **Laragon:** Versi Full (Apache 2.4+, MySQL 8.0+, PHP 8.1+).
- **Git:** Versi terbaru untuk Windows.
- **Web Browser:** Google Chrome, Microsoft Edge, atau Mozilla Firefox.

---

### 2. Langkah-Langkah Setup Lokal

#### Langkah 1: Kloning Repositori ke Direktori Laragon
Buka terminal (Git Bash atau PowerShell) dan jalankan:
```bash
cd C:\laragon\www
git clone https://github.com/useripx/camhub.git
cd camhub
```

#### Langkah 2: Pindah ke Branch Kerja Tahap 01
```bash
git checkout tahap/01-persiapan-fondasi
```

#### Langkah 3: Menyiapkan Berkas Environment (`.env`)
Salin template konfigurasi `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
*Catatan:* File `.env` sudah dikonfigurasi untuk lingkungan lokal Laragon (`DB_HOST=127.0.0.1`, `DB_USER=root`, `DB_PASS=`). Berkas ini terlindungi dan tidak akan ter-commit ke GitHub.

#### Langkah 4: Menjalankan Layanan Laragon
1. Buka aplikasi **Laragon**.
2. Klik tombol **Start All** (pastikan Apache dan MySQL berstatus running warna hijau).

#### Langkah 5: Menjalankan Pengujian Otomatis (Wajib 100% PASS)
Buka terminal di dalam folder proyek `C:\laragon\www\camhub` dan jalankan:
```bash
php tests/test_foundation.php
```
*Hasil yang diharapkan:*
```text
HASIL PENGUJIAN: 7 / 7 LULUS (100%)
STATUS: 100% PASS (0 GAGAL / 0 ERROR) — VERIFIKASI SELESAI
```

#### Langkah 6: Membuka Aplikasi di Browser
Buka browser dan akses alamat berikut:
- **URL Utama:** `http://localhost/camhub/public/` (atau `http://camhub.test/` jika Virtual Host aktif)
- **URL Health API:** `http://localhost/camhub/public/api/health`

---

# BAGIAN 2 — PENJELASAN TEKNIS ARSITEKTUR FONDASI

### 1. Mengapa Memilih Modular PHP Native?
- Memastikan pemahaman arsitektur fundamental yang kuat (*Separation of Concerns*) tanpa ketergantungan library pihak ketiga.
- Performa sangat ringan, waktu muat instan di server lokal Laragon, dan kontrol penuh atas alur request-response.

### 2. Pola Front Controller & URL Rewriting
- Seluruh request web dari pengguna diarahkan ke satu gerbang utama: [`public/index.php`](../../public/index.php).
- Didukung berkas [`.htaccess`](../../public/.htaccess) dengan modul `mod_rewrite` Apache untuk menghasilkan URL bersih (*Clean URL*).
- Komponen [`app/Core/Router.php`](../../app/Core/Router.php) bertindak sebagai dispatcher yang memetakan URI ke view atau controller.

### 3. Keamanan Tingkat Lingkungan (Zero Secret Leakage)
- Document root diisolasi pada subdirektori `public/`, sehingga kode inti aplikasi di folder `app/`, `config/`, dan file rahasia `.env` tidak dapat diakses secara langsung melalui URL publik browser.
- Berkas `.gitignore` dipasang sejak commit perdana untuk melindungi file kredensial.

---

# BAGIAN 3 — SCRIPT PRESENTASI KELOMPOK 1 (TAHAP 1)

Berikut adalah naskah pembagian bicara untuk 4 anggota kelompok dalam mempresentasikan hasil pengerjaan Tahap 01:

```text
DURASI TOTAL: ± 7-10 Menit
ALAT BANTU: Proyektor / Screen Share Browser & Terminal
SLIDE / TAMPILAN:
1. Slide Judul & Anggota Kelompok 1
2. Slide Arsitektur Modular PHP Native & Struktur Folder
3. Demo Terminal: Eksekusi Test Suite (100% PASS)
4. Demo Browser: Live Tampilan Beranda CamHub di Laragon
```

---

### 🎙 PEMBICARA 1: Yogi Ario Pratama (Project Owner & Lead Developer)
**Waktu:** 2 Menit  
**Fokus:** Pembukaan, Latar Belakang Proyek CamHub, dan Filosofi Pengembangan

> *"Selamat pagi/siang rekan-rekan dan Bapak/Ibu dosen penguji.*  
>
> *Kami dari **Kelompok 1**, yang beranggotakan saya sendiri **Yogi Ario Pratama**, rekan saya **Muhammad Regieyanto**, **Ivan Farrel Nanda Kristanto**, dan **Krisna Mukti**.*  
>
> *Pada kesempatan kali ini, kami mempresentasikan hasil implementasi **Tahap 01: Persiapan Fondasi** dari platform yang sedang kami bangun, yaitu **CamHub – Camera Marketplace Platform**.*  
>
> *CamHub dirancang sebagai ekosistem marketplace kamera terpadu di Indonesia yang melayani 4 pilar bisnis: penyewaan kamera anti-bentrok, jual-beli unit kamera, penitipan penjualan atau consignment, dan manajemen lapak mitra.*  
>
> *Di tahap pertama ini, visi utama kami adalah membangun fondasi sistem yang kokoh menggunakan **Modular PHP Native** di atas server web Laragon. Kami menerapkan prinsip tata kelola ketat: **No Plan, No Code**, di mana setiap baris kode harus didasari rencana kerja tertulis dan melewati gerbang pengujian otomatis 100% PASS.*  
>
> *Selanjutnya, rekan saya Muhammad Regieyanto akan menjelaskan struktur arsitektur modular yang telah kami bangun."*

---

### 🎙 PEMBICARA 2: Muhammad Regieyanto (Backend & System Architect)
**Waktu:** 2 Menit  
**Fokus:** Arsitektur Modular & Pemisahan Layer Sistem

> *"Terima kasih Yogi.*  
>
> *Saya **Muhammad Regieyanto**. Saya akan menjelaskan arsitektur teknis yang mendasari aplikasi CamHub.*  
>
> *Untuk menjaga agar kode PHP Native tetap rapi, mudah dirawat, dan tidak menjadi 'spaghetti code', kami membagi sistem menjadi lapisan-lapisan modular:*  
> 1. *Pertama, **Presentation Layer** di folder `resources/views/` dan `public/assets/`, di mana kode antarmuka dipisahkan total dari logika bisnis.*  
> 2. *Kedua, **Controller Layer** di `app/Controllers/` untuk menangani alur HTTP request.*  
> 3. *Ketiga, **Service Layer** di `app/Services/` yang nantinya akan memegang aturan bisnis kritis seperti perhitungan durasi sewa dan komisi.*  
> 4. *Keempat, **Repository Layer** di `app/Repositories/` untuk abstraksi basis data menggunakan PDO Prepared Statements.*  
>
> *Selain itu, kami menyiapkan autoloader native berstandar PSR-4 di `public/index.php`, sehingga setiap class di namespace `App\` dapat dipanggil secara otomatis tanpa perlu `require` manual berkali-kali.*  
>
> *Aspek keamanan routing dan lingkungan akan dijelaskan lebih detail oleh rekan saya, Ivan Farrel."*

---

### 🎙 PEMBICARA 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
**Waktu:** 2 Menit  
**Fokus:** Front Controller, Clean URL Routing, dan Keamanan Lingkungan

> *"Terima kasih Regi.*  
>
> *Halo semuanya, saya **Ivan Farrel Nanda Kristanto**.*  
>
> *Dua aspek penting yang kami selesaikan pada Tahap 01 ini adalah sistem routing dan perlindungan keamanan kredensial:*  
>
> *1. **Front Controller & Routing Native:** Kami membangun class `Router.php` di dalam `app/Core/`. Dengan bantuan file `.htaccess`, semua request yang masuk akan diproses secara terpusat oleh `public/index.php`. Sistem ini mendukung clean URL yang ramah SEO serta mampu memetakan endpoint dinamis dan API health check.*  
>
> *2. **Proteksi Dokumen Root & Kunci Rahasia:** Kami mengadopsi standar industri di mana document root web server diarahkan ke folder `public/`. Artinya, file konfigurasi rahasia seperti `.env`, file logika di folder `app/`, maupun data penyimpanan di `storage/` berada di luar jangkauan akses HTTP publik secara langsung. Ditambah dengan konfigurasi `.gitignore` sejak commit perdana, risiko kebocoran kunci API dan database lokal adalah nol (*Zero Secret Leakage*).*  
>
> *Selanjutnya, pembuktian hasil pengujian otomatis dan demo live aplikasi akan dibawakan oleh rekan saya, Krisna Mukti."*

---

### 🎙 PEMBICARA 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
**Waktu:** 2.5 Menit  
**Fokus:** Verifikasi Pengujian 100% PASS, Demo Tampilan Browser, dan Penutup

> *(Sambil membuka terminal dan menjalankan perintah uji)*  
>
> *"Terima kasih Ivan.*  
>
> *Saya **Krisna Mukti**, bertanggung jawab pada penjaminan mutu dan verifikasi pengujian.*  
>
> *Di Kelompok 1, kami memegang prinsip: **No Evidence, No Completion**. Suatu tahap tidak boleh dinyatakan selesai jika belum terbukti lulus uji 100%.*  
>
> *Di layar dapat kita lihat bersama, saya mengeksekusi test runner otomatis:*  
> `php tests/test_foundation.php`  
>
> *(Arahkan perhatian audiens ke terminal)*  
> *Sistem menguji 7 parameter kritis: mulai dari konektivitas Git remote, proteksi `.gitignore`, integritas 19 folder modular, kesiapan Router class, engine rendering template, konfigurasi aplikasi, hingga fungsi helper. Hasilnya: **7 dari 7 LULUS (100% PASS)** tanpa error.*  
>
> *(Buka browser ke alamat `http://localhost/camhub/public/`)*  
> *Sekarang mari kita lihat tampilan webnya di browser. Halaman beranda fondasi ini telah mengadopsi palet warna resmi dari mockup prototype CamHub: Deep Slate `#0b192e`, Royal Blue `#2563eb`, dan latar Ice Gray. Tampilan ini membuktikan server Apache Laragon, front controller, dan stylesheet CSS telah terintegrasi sempurna.*  
>
> *Kami juga menyediakan endpoint `http://localhost/camhub/public/api/health` yang mengembalikan respon JSON `status: OK` sebagai bukti kesiapan API.*  
>
> *Dengan ini, Tahap 01 Fondasi dinyatakan sukses 100% dan kami siap melangkah ke tahap berikutnya. Sekian presentasi dari Kelompok 1, kami membuka sesi tanya jawab jika ada masukan dari Bapak/Ibu dosen. Terima kasih."*

---

# BAGIAN 4 — PANDUAN MENJAWAB PERTANYAAN DOSEN (Q&A GUIDE)

| Kemungkinan Pertanyaan Dosen | Rekomendasi Jawaban dari Kelompok 1 |
|---|---|
| **Kenapa tidak memakai framework seperti Laravel?** | *"Sesuai ketentuan Project Owner dan rencana induk implementasi, kami menggunakan Modular PHP Native agar kami benar-benar menguasai fundamental arsitektur web, routing, dependency management, dan keamanan tanpa bergantung pada 'black box' framework."* |
| **Bagaimana cara mencegah pengguna mengakses file `.env` lewat browser?** | *"Melalui isolasi Document Root. Server web diarahkan hanya membaca folder `public/`. File `.env` berada satu tingkat di atas `public/` (di root folder), sehingga server web Apache menolak akses HTTP langsung ke berkas tersebut."* |
| **Apakah routing ini mendukung parameter URL seperti `/produk/{id}`?** | *"Ya. Router di `app/Core/Router.php` telah dirancang menggunakan regular expression regex matching (`preg_match`), sehingga siap menerima parameter dinamis pada tahapan katalog dan detail produk berikutnya."* |
