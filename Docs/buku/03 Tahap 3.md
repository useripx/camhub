# BUKU PANDUAN & SCRIPT PRESENTASI — TAHAP 03: AUTENTIKASI PENGGUNA

> **NAMA PROYEK:** CamHub – Camera Marketplace Platform  
> **KELOMPOK:** Kelompok 1  
> **ANGGOTA KELOMPOK:**  
> 1. **Yogi Ario Pratama** (Project Owner / Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI BUKU:** [`Docs/buku/03 Tahap 3.md`](03%20Tahap%203.md)  
> **REFERENSI IMPLEMENTASI:** [`Docs/tahapan/03.implementation_plan_Tahap3.md`](../tahapan/03.implementation_plan_Tahap3.md)  
> **ACUAN MOCKUP:** [`template/CamHub — Interactive UI UX Prototype/CamHub — Login & Register.png`](../../template/CamHub%20%E2%80%94%20Interactive%20UI%20UX%20Prototype/CamHub%20%E2%80%94%20Login%20%26%20Register.png)  
> **TABEL DATABASE:** `users`, `roles`, `user_profiles`, `partners`, `partner_stores`, `audit_logs`

---

# BAGIAN 1 — PANDUAN SETUP & PENGUJIAN AUTENTIKASI TIM (STEP-BY-STEP)

Panduan langkah demi langkah bagi seluruh anggota Kelompok 1 untuk menjalankan dan mendemokan fitur autentikasi di server lokal Laragon laptop masing-masing.

### 1. Prasyarat (Prerequisites)
- Layanan **Apache** dan **MySQL** di Laragon aktif.
- Berkas `.env` telah memuat konfigurasi `camhub_db`.
- Database telah ter-migrate pada Tahap 02 (`php database/migrate.php`).

---

### 2. Langkah-Langkah Menjalankan di Laptop Masing-Masing

#### Langkah 1: Masuk ke Branch Tahap 03
Buka terminal di root `C:\laragon\www\camhub` dan jalankan:
```bash
git checkout tahap/03-autentikasi-pengguna
```

#### Langkah 2: Jalankan Migrasi Penambahan Kolom Username
Pastikan tabel `users` telah memiliki kolom `username` unik:
```bash
php database/migrations/01_add_username_to_users.php
```

#### Langkah 3: Eksekusi Seeder Akun Siap Pakai (Demo Credentials)
Jalankan pengisian akun demo default yang diminta Project Owner:
```bash
php database/seeders/initial_seed.php
```

*Daftar Kredensial Akun Siap Pakai untuk Demo/Dosen:*
| Peran (Role) | Username | Password | Email Alternatif | Tujuan Pengalihan Rute |
|---|---|---|---|---|
| **Administrator** | `admin` | `admin` | `admin@camhub.id` | `/dashboard/admin` |
| **Customer / User** | `user` | `user` | `user@camhub.id` | `/dashboard/pelanggan` |
| **Mitra Lapak** | `mitra` | `mitra` | `mitra@camhub.id` | `/dashboard/mitra` |

> 💡 **Fitur Kemudahan Presentasi:** Pada halaman login, terdapat kartu tombol cepat (1-klik) untuk langsung mengisi kredensial demo `admin:admin`, `user:user`, dan `mitra:mitra` secara otomatis tanpa perlu mengetik manual.

#### Langkah 4: Menjalankan Pengujian Otomatis (100% PASS Gate)
Jalankan test suite autentikasi:
```bash
php tests/test_auth.php
```
*Hasil yang diharapkan:*
```text
========================================================
HASIL PENGUJIAN TAHAP 03: 10/10 BERHASIL (100% PASS)
========================================================
```

Jalankan juga uji regresi tahapan sebelumnya:
```bash
php tests/test_foundation.php
php tests/test_database.php
```

#### Langkah 5: Pengujian Langsung di Browser
1. Buka browser ke alamat: [http://localhost/camhub/public/login](http://localhost/camhub/public/login).
2. Klik tombol pill **Admin (admin:admin)** lalu tekan **Masuk ke Akun** -> Anda akan langsung diarahkan ke Dashboard Administrator.
3. Klik tombol **Keluar (Logout)** di pojok kanan atas.
4. Coba masuk dengan **User (user:user)** -> Anda diarahkan ke Dashboard Pelanggan.
5. Coba langsung mengetik URL terlarang di address bar: `http://localhost/camhub/public/dashboard/admin` -> Sistem mendeteksi role customer dan menampilkan halaman **403 | Akses Dibatasi**.

---

# BAGIAN 2 — PENJELASAN TEKNIS ARSITEKTUR KEAMANAN & RBAC

### 1. Antarmuka Split-Screen Presisi Mockup (100% Designer Match)
- **Panel Kiri (Dark Navy `#0b192e`):** 
  - Logo CamHub dengan ikon kamera dan aksen oranye amber.
  - Tagline utama: *"Sewa, Jual, Beli, Titip. Semua Kebutuhan Fotografi."*
  - 3 Nilai Keunggulan ber-checkmark: **Transaksi Aman (Escrow)**, **Verifikasi Identitas Resmi (KYC)**, dan **Pilihan Kamera & Lensa Terlengkap**.
- **Panel Kanan (Formulir Putih Bersih):**
  - Mengakomodasi login fleksibel multi-identifier: **Username, Email, atau Nomor HP**.
  - Dilengkapi generator input tersembunyi `_token` untuk proteksi CSRF.

### 2. Pilar Keamanan Otentikasi
1. **Password Hashing BCRYPT:**
   - Semua password disimpan dalam bentuk satu arah menggunakan `password_hash($pass, PASSWORD_BCRYPT)`.
   - Tidak ada password plaintext yang tersimpan di database.
2. **Perlindungan Session Fixation Attack:**
   - Sesaat setelah password diverifikasi benar, sistem memanggil `session_regenerate_id(true)` untuk mengganti ID session lama dengan yang baru.
3. **Cross-Site Request Forgery (CSRF) Guard:**
   - Helper `csrf_token()` membangkitkan token acak 64 karakter heksadesimal berbasis `random_bytes(32)`.
   - Fungsi `verify_csrf()` memvalidasi token menggunakan `hash_equals()` untuk mencegah *timing attack*.
4. **Pencatatan Jejak Audit (Security Audit Trail):**
   - Setiap upaya login sukses (`LOGIN_SUCCESS`), login gagal (`LOGIN_FAILED`), registrasi (`REGISTER_SUCCESS`), dan logout (`LOGOUT`) dicatat otomatis ke tabel `audit_logs` lengkap dengan timestamp dan IP address pengunjung.

### 3. Pipeline Middleware pada Router Native
Router CamHub kini mendukung eksekusi pipeline middleware sebelum controller dipanggil:
- `GuestMiddleware`: Mencegah pengguna yang telah login mengakses kembali form login/register.
- `AuthMiddleware`: Memastikan request berasal dari sesi yang valid; jika belum login, otomatis redirect ke `/login` disertai pesan flash.
- `RoleMiddleware`: Memverifikasi kecocokan peran pengguna (`role:admin`, `role:partner`, `role:customer`). Akses silang peran ditolak dengan status HTTP 403.

---

# BAGIAN 3 — NASKAH SKENARIO PRESENTASI KELOMPOK 1 (4 ORANG)

Gunakan urutan berbicara ini saat presentasi progres Tahap 03 di depan dosen/auditor:

```
+-----------------------------------------------------------------------------------------+
|                                ALUR PRESENTASI TAHAP 03                                |
|                                                                                         |
|  [Yogi Ario Pratama]            --> Pembukaan, Filosofi Mockup & Demo Kredensial Cepat |
|  [Muhammad Regieyanto]          --> Arsitektur Backend, Model, Repository & BCRYPT      |
|  [Ivan Farrel Nanda Kristanto]  --> Pipeline Middleware, RBAC Guard & Proteksi CSRF    |
|  [Krisna Mukti]                 --> Quality Assurance, 100% PASS Test & Live Demo      |
+-----------------------------------------------------------------------------------------+
```

---

### 🎙️ PEMBICARA 1: Yogi Ario Pratama (Project Owner & Lead Developer)
**Peran:** Membuka presentasi, menjelaskan pencapaian antarmuka sesuai prototype, filosofi brand, dan kemudahan akses akun demo.

> *"Selamat pagi/siang Bapak/Ibu Dosen penguji dan rekan-rekan sekalian.*  
> *Saya **Yogi Ario Pratama**, mewakili **Kelompok 1**, selaku Project Owner dan Lead Developer proyek platform **CamHub**.*
>
> *Pada Tahap 03 ini, tim kami berfokus pada pembangunan fondasi keamanan terpenting dalam aplikasi marketplace, yaitu **Autentikasi Pengguna & Role-Based Access Control (RBAC)**.*
>
> *Sesuai komitmen awal kami terhadap dokumen spesifikasi dan acuan visual, tampilan halaman login dan registrasi telah kami bangun **100% presisi** mengikuti file rancangan desainer kami di `CamHub — Login & Register.png`.*
> *Kami menerapkan layout modern **Split-Screen**:*
> *1. Sisi kiri dengan panel Navy gelap menampilkan identitas CamHub, tagline 'Sewa, Jual, Beli, Titip', serta 3 jaminan utama: Transaksi Aman Escrow, Verifikasi Identitas KYC, dan Katalog Lengkap.*
> *2. Sisi kanan adalah form masuk yang sangat intuitif. Di sini, kami mendukung login multi-identifier: pengguna dapat masuk menggunakan **Username, Email, maupun Nomor HP**.*
>
> *Untuk mempermudah pengujian oleh dosen dan tim evaluator hari ini, kami juga telah menanamkan fitur **1-Click Quick Demo Credentials**: Anda cukup mengeklik tombol pill **Admin (admin:admin)**, **User (user:user)**, atau **Mitra (mitra:mitra)**, maka formulir akan terisi otomatis dan siap diuji.*
>
> *Selanjutnya, rekan saya Muhammad Regieyanto akan membedah arsitektur backend dan keamanan kata sandi yang mendasarinya. Silakan, Regi."*

---

### 🎙️ PEMBICARA 2: Muhammad Regieyanto (Backend & System Architect)
**Peran:** Menjelaskan struktur kode backend, pola Repository-Service, integritas model pengguna, dan keamanan enkripsi BCRYPT.

> *"Terima kasih, Yogi. Halo semuanya, saya **Muhammad Regieyanto**, Backend & System Architect Kelompok 1.*
>
> *Di balik tampilan antarmuka yang bersih, kami mengimplementasikan arsitektur modular berbasis **Separation of Concerns (SoC)** murni PHP Native tanpa bantuan framework pihak ketiga:*
> *1. **Model & Entitas (`App\Models\User`):** Mewakili representasi data pengguna dengan method pembantu seperti `isAdmin()`, `isPartner()`, dan `isCustomer()`.*
> *2. **Repository Layer (`App\Repositories\UserRepository`):** Seluruh query database dibungkus dalam PDO Prepared Statements murni. Kueri pencarian akun kami rancang cerdas sehingga mengenali identifier tunggal terhadap kolom `username`, `email`, atau `phone` secara aman.*
> *3. **Service Layer (`App\Services\AuthService`):** Mengelola alur logika bisnis login, pendaftaran akun, hingga pencatatan jejak audit.*
>
> *Terkait keamanan kata sandi, kami tidak pernah menyimpan password dalam bentuk teks polos. Kami menerapkan algoritma hashing standar industri **BCRYPT** melalui fungsi `password_hash()` dan verifikasi `password_verify()`. Dengan algoritma ini, data kredensial di database kebal terhadap serangan brute-force maupun rainbow table.*
>
> *Selain itu, setiap kali login sukses, sistem kami langsung mengeksekusi `session_regenerate_id(true)` untuk memitigasi risiko pembajakan sesi (Session Fixation Attack). Sesi pengguna disimpan dengan atribut cookie `HttpOnly` dan `SameSite=Lax`.*
>
> *Selanjutnya, rekan kami Ivan Farrel akan menjelaskan bagaimana rute dan akses peran dikendalikan melalui sistem Middleware native kami. Silakan, Ivan."*

---

### 🎙️ PEMBICARA 3: Ivan Farrel Nanda Kristanto (Core Routing & Security Engineer)
**Peran:** Menjelaskan perluasan Router Native, mekanisme Middleware (Auth, Guest, Role), pengalihan peran (Redirection), dan proteksi CSRF.

> *"Terima kasih, Regi. Selamat pagi/siang semuanya, saya **Ivan Farrel Nanda Kristanto**, Core Routing & Security Engineer Kelompok 1.*
>
> *Pada Tahap 03 ini, saya bertugas memperluas kemampuan **Router Native CamHub** agar mampu menjalankan **Pipeline Middleware** sebelum sebuah Controller dieksekusi. Kami membangun 3 Middleware utama yang bekerja secara deklaratif di `routes/web.php`:*
> *1. **`GuestMiddleware`:** Memastikan pengguna yang sudah memiliki sesi aktif tidak dapat mengakses kembali formulir login atau registrasi, melainkan langsung diarahkan ke dashboard peran mereka.*
> *2. **`AuthMiddleware`:** Bertindak sebagai penjaga gerbang (Route Guard). Rute privat seperti dashboard tidak akan pernah bisa dibuka tanpa session login yang sah. Pengunjung liar otomatis dialihkan ke halaman login dengan status 302.*
> *3. **`RoleMiddleware`:** Mengimplementasikan **Role-Based Access Control (RBAC)**. Middleware ini memeriksa apakah peran akun yang login cocok dengan izin rute (misalnya `role:admin`). Apabila akun Customer mencoba membuka dashboard Admin, sistem langsung menolak dengan kode HTTP **403 Forbidden** dan menampilkan halaman peringatan interaktif.*
>
> *Kami juga mengamankan seluruh form POST dengan **CSRF Token Guard**. Token unik di-generate di session dan divalidasi menggunakan perbandingan string aman `hash_equals()`. Jika ada pihak luar mencoba mengirimkan form palsu dari domain lain, request akan langsung ditolak.*
>
> *Untuk pembuktian kehandalan kode dan hasil pengujian otomatis, saya persilakan rekan kami Krisna Mukti."*

---

### 🎙️ PEMBICARA 4: Krisna Mukti (Quality Assurance & Presentation Coordinator)
**Peran:** Menampilkan laporan pengujian otomatis 100% PASS, uji regresi, mendemonstrasikan fitur di browser, dan menutup presentasi.

> *"Terima kasih, Ivan. Halo semuanya, saya **Krisna Mukti**, Quality Assurance & Presentation Coordinator Kelompok 1.*
>
> *Sebagai penanggung jawab kualitas, tugas saya adalah memastikan setiap baris kode yang ditulis telah teruji secara objektif dan tidak menimbulkan regresi terhadap tahapan sebelumnya.*
>
> *Kami membangun suite pengujian otomatis mandiri di file `tests/test_auth.php`. Pengujian ini mencakup **10 skenario krusial**, antara lain:*
> *1. Integritas render antarmuka mockup prototype.*
> *2. Validasi token CSRF (token asli diterima, token manipulasi ditolak).*
> *3. Form validator berbahasa Indonesia.*
> *4. Pendaftaran akun baru dengan enkripsi BCRYPT.*
> *5. Login instan akun Admin (`admin:admin`).*
> *6. Login instan akun Customer (`user:user`).*
> *7. Fleksibilitas login ganda via Email dan Nomor HP.*
> *8. Penolakan password salah dan pencatatan riwayat audit log.*
> *9. Proteksi Route Guard RBAC terhadap akses terlarang.*
> *10. Siklus penghancuran sesi saat Logout.*
>
> *Ketika dijalankan di terminal: **10/10 TEST BERHASIL DENGAN HASIL 100% PASS** tanpa satupun error. Tidak hanya itu, uji regresi terhadap **Tahap 01 (Foundation)** dan **Tahap 02 (Database)** tetap membukukan hasil **100% PASS**.*
>
> *(Demo Browser):*  
> *Sekarang mari kita lihat langsung di layar browser:*
> *- Kita buka `/login`, terlihat antarmuka split-screen yang sangat rapi.*
> *- Kita klik tombol pill demo **Admin**, sistem langsung membawa kita ke `/dashboard/admin`.*
> *- Kita klik **Logout**, sesi terhapus bersih.*
> *- Kita coba login dengan **User**, kita diarahkan ke `/dashboard/pelanggan`.*
>
> *Dengan seluruh pembuktian ini, kami menyatakan bahwa **Tahap 03: Autentikasi Pengguna & RBAC** telah **SELESAI 100%** dan siap dilanjutkan ke **Tahap 04: Profil Pelanggan dan KYC**.*
>
> *Terima kasih atas perhatian Bapak/Ibu Dosen dan rekan-rekan sekalian. Kami buka sesi tanya jawab jika ada masukan."*

---

# BAGIAN 4 — DAFTAR PERTANYAAN DOSEN & JAWABAN TEKNIS (Q&A CHEAT SHEET)

Berikut adalah panduan antisipasi pertanyaan teknis yang sering diajukan penguji:

#### Q1: Mengapa kalian tidak menggunakan framework seperti Laravel atau CodeIgniter untuk autentikasi?
**Jawaban (Yogi / Regi):**  
*"Sesuai aturan dan pedoman arsitektur proyek CamHub, kami menggunakan **Pure PHP Native dengan Arsitektur Modular MVC**. Tujuannya adalah memperkuat pemahaman mendalam tentang konsep dasar web security: cara kerja session fixation, hashing password, routing native, dan pipeline middleware tanpa bergantung pada 'magic' framework. Hasilnya kode kami sangat ringan, transparan, dan terstruktur rapi."*

#### Q2: Bagaimana cara kalian mencegah serangan brute-force pada password?
**Jawaban (Regi / Ivan):**  
*"Pertama, kami menggunakan hashing BCRYPT yang sengaja memiliki cost komputasi terukur untuk memperlambat upaya cracking offline. Kedua, setiap upaya login gagal dicatat secara presisi ke tabel `audit_logs` bersama alamat IP dan timestamp pengguna, sehingga siap dihubungkan dengan rate-limiting pada tahap penguatan security mendatang."*

#### Q3: Mengapa login bisa menggunakan Username, Email, sekaligus No. HP?
**Jawaban (Ivan / Yogi):**  
*"Di Indonesia, banyak pengguna lebih terbiasa login dengan nomor WhatsApp atau username singkat ketimbang mengetik alamat email yang panjang. Kami merancang kueri database pada `UserRepository` dengan klausa `WHERE username = :id1 OR email = :id2 OR phone = :id3` yang dieksekusi dengan PDO Prepared Statements, sehingga ramah pengguna lokal namun tetap 100% aman dari SQL Injection."*

#### Q4: Apa fungsi `session_regenerate_id(true)` saat login?
**Jawaban (Regi / Ivan):**  
*"Untuk mencegah serangan **Session Fixation**. Tanpa regenerasi ID, seorang penyerang bisa menyusupkan ID sesi tertentu ke browser korban sebelum login. Jika korban berhasil login dengan ID sesi tersebut, penyerang bisa mengambil alih akun korban. Dengan `session_regenerate_id(true)`, PHP langsung menghancurkan ID sesi lama dan menerbitkan ID sesi baru yang sepenuhnya acak begitu kredensial terbukti valid."*

