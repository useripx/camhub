# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 04: PROFIL PELANGGAN DAN VERIFIKASI KYC

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner / Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI BUKU:** [`Docs/buku/04 Tahap 4.md`](04%20Tahap%204.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/04.implementation_plan_Tahap4.md`](../tahapan/04.implementation_plan_Tahap4.md)  
> **ACUAN MOCKUP:** [`template/CamHub — Interactive UI UX Prototype/CamHub — Verifikasi Identitas KYC.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Verifikasi%20Identitas%20KYC.png)  
> **TABEL DATABASE:** `kyc_submissions`, `user_profiles`, `users`, `audit_logs`

---

# BAGIAN 1 — PANDUAN SETUP & PENGUJIAN FITUR KYC TIM (STEP-BY-STEP)

Panduan langkah demi langkah bagi seluruh anggota Kelompok 1 untuk menjalankan dan mendemokan fitur Profil Pelanggan dan Verifikasi KYC di laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **Apache** dan **MySQL** di Laragon aktif.
- Berkas `.env` telah terisi kredensial database `camhub_db`.
- Ekstensi PHP `fileinfo` aktif untuk pemeriksaan MIME type berkas.

---

### 2. Langkah-Langkah Menjalankan di Laptop Masing-Masing

#### Langkah 1: Masuk ke Branch Tahap 04
Buka terminal di `C:\laragon\www\camhub` dan jalankan:
```bash
git checkout tahap/04-profil-pelanggan-kyc
```

#### Langkah 2: Memastikan Folder Uploads & Keamanan Siap
Folder penyimpanan berkas unggahan telah terproteksi di:
`public/uploads/kyc/` lengkap dengan berkas konfigurasi `.htaccess` yang mematikan eksekusi script berbahaya (*execution disabled*).

#### Langkah 3: Menjalankan Pengujian Otomatis (100% PASS Gate)
Jalankan test runner mandiri modul KYC:
```bash
php tests/test_kyc.php
```
*Hasil yang diharapkan:*
```text
========================================================
HASIL PENGUJIAN TAHAP 04: 10/10 BERHASIL (100% PASS)
========================================================
```

Jalankan juga seluruh rangkaian pengujian dari Tahap 01 hingga 04:
```bash
php tests/test_foundation.php
php tests/test_database.php
php tests/test_auth.php
php tests/test_kyc.php
```
*(Seluruh 34 uji kasus terbukti LULUS 100%)*

#### Langkah 4: Pengujian Langsung di Browser
1. Buka browser ke alamat: [http://localhost/camhub/public/login](http://localhost/camhub/public/login).
2. Klik tombol pill **User (user:user)** lalu tekan **Masuk ke Akun**.
3. Di Dashboard Pelanggan:
   - Terlihat kartu **Status Verifikasi KYC**.
   - Klik tombol **🪪 Mulai Verifikasi KYC** (mengarah ke `/kyc`).
4. Pada Halaman Form KYC:
   - Terlihat panduan foto KTP ber-checkmark.
   - Isi Nama Lengkap sesuai KTP, NIK KTP (16 digit), Tanggal Lahir, dan Jenis Kelamin.
   - Unggah Foto KTP dan Swafoto memegang KTP (format JPG/PNG/WEBP, maksimal 2MB).
   - Centang kotak pernyataan keabsahan data.
   - Klik **Kirim Pengajuan Verifikasi KYC**.
5. Sistem otomatis mengalihkan ke halaman **Status KYC** (`/kyc/status`) dengan lencana kuning **Menunggu Tinjauan Admin (Pending)**.
6. Buka halaman **Profil Saya** (`/profil`): Anda dapat melihat lencana status KYC real-time serta memperbarui alamat domisili dan nomor telepon.

---

# BAGIAN 2 — PENJELASAN TEKNIS ARSITEKTUR PROFIL & KYC

### 1. Desain Visual Multi-Step Form (100% Sesuai Prototype)
Form verifikasi KYC dibangun mengacu pada prototype `CamHub — Verifikasi Identitas KYC.png`:
- **Indikator Stepper:** Menunjukkan tahapan pengisian: 1) Data Identitas KTP, 2) Unggah Berkas, dan 3) Pernyataan & Kirim.
- **Kotak Panduan KTP:** Memberikan edukasi visual kepada pelanggan cara memotret KTP yang benar (tidak terpotong, teks tajam, bukan fotokopi buram).
- **Dual Drag-and-Drop Dropzone:** Area pengunggahan berkas KTP dan Swafoto yang responsif dengan pratinjau nama dan ukuran file.

### 2. Standar Keamanan Unggah Berkas (Secure Upload Architecture)
Mengunggah file ke web server memiliki risiko keamanan tinggi (*Remote Code Execution*). CamHub menerapkan 4 lapis perlindungan:
1. **Pemeriksaan MIME Type Asli:** Tidak hanya memeriksa ekstensi nama file, melainkan membaca *magic bytes* berkas menggunakan PHP `finfo` (`image/jpeg`, `image/png`, `image/webp`).
2. **Pembatasan Ukuran:** Dibatasi maksimal **2 MB** per berkas untuk efisiensi penyimpanan server.
3. **Pengacakan Nama Berkas (Cryptographic Hash):** File disimpan dengan nama acak `ktp_{userId}_{hash}.jpg` menggunakan `random_bytes(16)`, sehingga mustahil ditebak penyerang (*Anti-Path Traversal & Anti-File Overwrite*).
4. **Isolasi Folder via `.htaccess`:** Folder `public/uploads/kyc/` dilengkapi aturan larangan eksekusi PHP sehingga berkas yang diunggah tidak akan pernah bisa dieksekusi sebagai skrip.

### 3. Logika Bisnis & Perlindungan Pengajuan Ganda
- Pelanggan yang telah memiliki pengajuan berstatus `pending` diblokir dari mengajukan ulang secara bertubi-tubi guna mencegah beban antrean pada administrator (*Rate Limiting Guard*).
- Jika pengajuan berstatus `approved`, tombol sewa terbuka penuh. Jika `rejected`, pelanggan diberikan catatan revisi (*rejection reason*) dan tombol ajukan ulang.
- Seluruh mutasi data dicatat secara transparan pada tabel `audit_logs` dengan aksi `KYC_SUBMIT` dan `PROFILE_UPDATE`.

---

# BAGIAN 3 — NASKAH SKENARIO PRESENTASI KELOMPOK 1 (4 ORANG)

Gunakan urutan berbicara ini saat presentasi progres Tahap 04 di depan dosen/auditor:

```
+-----------------------------------------------------------------------------------------+
|                                ALUR PRESENTASI TAHAP 04                                |
|                                                                                         |
|  [Yogi Ario Pratama]            --> Pembukaan, Filosofi Keamanan KYC & Presisi Mockup   |
|  [Muhammad Regieyanto]          --> Arsitektur Backend, Entitas KYC, & Upload Hashing   |
|  [Ivan Farrel Nanda Kristanto]  --> Sistem Validasi NIK, Duplicate Guard & Audit Trail  |
|  [Krisna Mukti]                 --> QA, 10/10 PASS Test Suite, Demo Browser & Penutup   |
+-----------------------------------------------------------------------------------------+
```

---

### 🎙️ PEMBICARA 1: Yogi Ario Pratama (Project Owner & Lead Developer)
**Peran:** Membuka presentasi, menjelaskan pentingnya verifikasi identitas di industri rental kamera, dan presisi mockup visual.

> *"Selamat pagi/siang Bapak/Ibu Dosen penguji dan rekan-rekan sekalian.*  
> *Saya **Yogi Ario Pratama**, mewakili **Kelompok 1**, selaku Project Owner platform **CamHub**.*
>
> *Pada Tahap 04 ini, tim kami mengembangkan modul krusial yang menjadi benteng pertahanan utama bisnis rental kamera, yaitu **Profil Pelanggan dan Verifikasi Identitas Resmi (Know Your Customer / KYC)**.*
>
> *Dalam industri penyewaan kamera, unit yang dipinjamkan bernilai puluhan juta rupiah—seperti Sony A7 III atau Canon R50. Tanpa verifikasi identitas resmi, platform rentan terhadap penipuan dan penggelapan unit. Oleh karena itu, modul KYC ini menjadi prasyarat mutlak sebelum seorang pelanggan dapat menyewa kamera.*
>
> *Kami membangun antarmuka pengajuan KYC **100% presisi** mengikuti file rancangan desainer kami di `CamHub — Verifikasi Identitas KYC.png`:*
> *1. Tersedia indikator tahapan (Stepper) yang jelas.*
> *2. Dilengkapi panduan visual pengambilan foto KTP agar pelanggan tidak salah memotret.*
> *3. Dua area unggah terpisah untuk Foto KTP Asli dan Swafoto memegang KTP.*
> *4. Pernyataan integritas hukum yang wajib disetujui pelanggan.*
>
> *Selanjutnya, rekan saya Muhammad Regieyanto akan membedah arsitektur backend dan sistem pengunggahan berkas yang aman. Silakan, Regi."*

---

### 🎙️ PEMBICARA 2: Muhammad Regieyanto (Backend & System Architect)
**Peran:** Menjelaskan struktur database `kyc_submissions`, pola Repository-Service, dan arsitektur pengunggahan berkas berkeamanan tinggi.

> *"Terima kasih, Yogi. Halo semuanya, saya **Muhammad Regieyanto**, Backend & System Architect Kelompok 1.*
>
> *Pada lapisan backend, modul ini bertumpu pada dua entitas utama: `user_profiles` dan `kyc_submissions`:*
> *1. **Model & Repository Layer (`App\Repositories\KycRepository`):** Mengelola relasi data antara pengguna, profil alamat pengiriman, dan riwayat dokumen pengajuan KYC menggunakan PDO Prepared Statements.*
> *2. **Service Layer (`App\Services\KycService`):** Mengatur alur pemindahan berkas, pengecekan status terkini, dan pencatatan jejak audit.*
>
> *Tantangan terbesar dalam fitur KYC adalah **keamanan unggah berkas (File Upload Security)**. Bila tidak diamankan dengan benar, penyerang bisa mengunggah skrip web shell PHP berbahaya. Untuk mencegah hal tersebut, kami menerapkan 4 lapisan proteksi:*
> *- Pertama, kami memeriksa **MIME type asli berkas** menggunakan `finfo` langsung pada magic header file, bukan sekadar melihat ekstensi nama.*
> *- Kedua, membatasi ukuran file maksimal 2 Megabyte.*
> *- Ketiga, seluruh file yang tersimpan di server diacak namanya menggunakan **Cryptographic Random Hash** `bin2hex(random_bytes(16))`, sehingga nama file asli pelanggan tidak dapat ditebak dan kebal terhadap bentrokan nama.*
> *- Keempat, folder upload diisolasi dengan konfigurasi `.htaccess` yang menonaktifkan mesin eksekusi skrip PHP.*
>
> *Selanjutnya, rekan kami Ivan Farrel akan menjelaskan sistem validasi NIK dan proteksi alur pengajuannya. Silakan, Ivan."*

---

### 🎙️ PEMBICARA 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
**Peran:** Menjelaskan validasi NIK KTP Indonesia, pencegahan pengajuan ganda, proteksi Route Guard, dan audit logging.

> *"Terima kasih, Regi. Selamat pagi/siang semuanya, saya **Ivan Farrel Nanda Kristanto**, Core Routing & Security Engineer Kelompok 1.*
>
> *Pada modul KYC ini, saya memastikan bahwa seluruh data yang masuk tervalidasi secara ketat di sisi server:*
> *1. **Validasi NIK KTP Indonesia:** Sistem memeriksa pola NIK menggunakan ekspresi reguler tepat 16 digit numerik serta memvalidasi usia minimal peminjam yaitu 17 tahun berdasarkan tanggal lahir.*
> *2. **Pencegahan Pengajuan Ganda (Duplicate Submission Guard):** Pelanggan yang telah memiliki berkas berstatus `pending` secara otomatis dicegah mengajukan form baru sebelum ditinjau oleh admin, mencegah penumpukan data spam di server.*
> *3. **Proteksi Route Guard:** Seluruh rute `/profil`, `/profil/edit`, `/kyc`, dan `/kyc/status` diproteksi secara ganda oleh `AuthMiddleware` dan `RoleMiddleware` khusus peran `customer`. Pengguna luar atau peran lain tidak dapat mengakses endpoint ini secara ilegal.*
> *4. **Audit Trail Keamanan:** Setiap aktivitas pengiriman KYC dan perubahan biodata profil dicatat otomatis ke tabel `audit_logs` bersama alamat IP dan rincian perubahan yang telah disanitasi.*
>
> *Selanjutnya, rekan kami Krisna Mukti akan memaparkan bukti hasil pengujian otomatis dan demo langsung di browser. Silakan, Krisna."*

---

### 🎙️ PEMBICARA 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
**Peran:** Menampilkan laporan pengujian otomatis 100% PASS, uji regresi 34 test case, live demo di browser, dan menutup presentasi.

> *"Terima kasih, Ivan. Halo semuanya, saya **Krisna Mukti**, Quality Assurance & Presentation Coordinator Kelompok 1.*
>
> *Untuk membuktikan bahwa sistem berjalan tanpa cela, kami menyusun suite pengujian otomatis mandiri di file `tests/test_kyc.php`. Pengujian ini mencakup **10 skenario ketat**, antara lain:*
> *1. Render antarmuka formulir KYC sesuai mockup.*
> *2. Penolakan NIK yang tidak 16 digit.*
> *3. Penolakan berkas bukan gambar atau file yang melebihi batas 2MB.*
> *4. Pembuktian penyimpanan berkas dengan nama acak yang aman.*
> *5. Penyimpanan entri pengajuan KYC ke MySQL berstatus 'pending'.*
> *6. Pemblokiran pengajuan ganda saat status masih pending.*
> *7. Pembaruan biodata domisili profil pelanggan.*
> *8. Sinkronisasi lencana status KYC real-time di profil dan dashboard.*
> *9. Pencatatan audit log `KYC_SUBMIT`.*
> *10. Pengujian proteksi route guard otorisasi.*
>
> *Ketika dijalankan di terminal: **10/10 TEST TAHAP 04 LULUS 100% PASS**. Kami juga menjalankan pengujian regresi seluruh tahapan sebelumnya:*
> *- Tahap 01 (Fondasi): 7/7 LULUS.*
> *- Tahap 02 (Database): 7/7 LULUS.*
> *- Tahap 03 (Autentikasi): 10/10 LULUS.*
> *- Tahap 04 (Profil & KYC): 10/10 LULUS.*
> *Total **34 dari 34 Test Case Lulus 100% tanpa satupun error**.*
>
> *(Demo Browser):*  
> *- Kita login dengan akun `user:user`.*
> *- Di dashboard, terlihat status KYC 'Belum Diajukan'.*
> *- Kita klik tombol KYC, mengisi NIK dan mengunggah berkas KTP & Selfie.*
> *- Begitu dikirim, halaman langsung berpindah ke status 'Menunggu Tinjauan Admin' dengan lencana kuning.*
> *- Status di Dashboard Pelanggan dan Profil Saya otomatis tersinkronisasi secara real-time.*
>
> *Dengan ini, kami menyatakan bahwa **Tahap 04: Profil Pelanggan dan Verifikasi KYC** telah **SELESAI 100%** dan siap dilanjutkan ke **Tahap 05: Website Publik dan Katalog Produk**.*
>
> *Terima kasih atas perhatian Bapak/Ibu Dosen sekalian. Kami persilakan jika ada pertanyaan."*

---

# BAGIAN 4 — DAFTAR PERTANYAAN DOSEN & JAWABAN TEKNIS (Q&A CHEAT SHEET)

#### Q1: Mengapa verifikasi KYC dilakukan secara manual oleh admin, bukan menggunakan OCR otomatis?
**Jawaban (Yogi / Regi):**  
*"Sesuai dokumen rencana kerja induk CamHub, verifikasi KTP dilakukan secara native dan akurat oleh tim kurasi internal platform untuk memastikan keabsahan dokumen tanpa ketergantungan biaya API pihak ketiga per request. Selain itu, alur approval massal oleh administrator telah disiapkan untuk tahap 11 (Dashboard Administrator)."*

#### Q2: Apa yang terjadi jika pengguna mencoba mengunggah skrip PHP berbahaya yang dinamai `foto.php` atau `foto.jpg.php`?
**Jawaban (Regi / Ivan):**  
*"Sistem kami menerapkan dua lapis pertahanan: Pertama, fungsi `validateUploadedImage` memeriksa konten file biner asli menggunakan `finfo(FILEINFO_MIME_TYPE)`. File PHP akan terdeteksi sebagai `text/x-php` dan langsung ditolak. Kedua, berkas yang lolos pun akan diubah namanya menjadi nama hash acak berakhiran `.jpg` dan disimpan di direktori yang telah dilengkapi konfigurasi `.htaccess` penonaktifan mesin skrip PHP."*

#### Q3: Mengapa pelanggan tidak boleh mengedit alamat email pada form edit profil?
**Jawaban (Ivan / Regi):**  
*"Email adalah identitas unik login dan akun keuangan rekening bersama (escrow). Mengubah email secara sembarangan membuka celah pembajakan akun (Account Takeover). Jika pengguna ingin mengganti email, hal tersebut membutuhkan alur verifikasi OTP terpisah di masa mendatang, sehingga pada profil pelanggan saat ini alamat email dikunci (*read-only*) demi keamanan."*

