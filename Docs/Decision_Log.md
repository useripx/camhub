# DECISION LOG — CATATAN KEPUTUSAN ARSITEKTUR & TEKNIS

> **PROYEK:** CamHub – Camera Marketplace Platform  
> **TIM PENGEMBANG:** Kelompok 1  
> 1. **Yogi Ario Pratama** (Project Owner & Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  

---

| ID Keputusan | Tanggal | Topik / Komponen | Keputusan yang Diambil | Rationale / Alasan Teknis | Status Persetujuan |
|:---:|:---:|---|---|---|:---:|
| **DEC-001** | 03/11/2026 | Backend Technology Policy | Menggunakan **Strict Modular PHP Native (PDO)** tanpa framework eksternal (Laravel/CI dilarang). | Memastikan performa tinggi, pemahaman arsitektur fundamental yang solid, dan kepatuhan penuh terhadap arahan Project Owner. | **Disetujui** |
| **DEC-002** | 03/11/2026 | Document Root & Web Server | Document root aplikasi diisolasi pada `public/` dengan URL Rewrite `.htaccess`. | Mengamankan file konfigurasi sensitif (`.env`), kode sumber `app/`, dan `storage/` dari akses langsung HTTP publik. | **Disetujui** |
| **DEC-003** | 03/11/2026 | Routing Mechanism | Membangun router native ringan `App\Core\Router` dengan dukungan URI parameter dan HTTP method matching. | Menghasilkan struktur URL bersih (*Clean URL*) yang SEO-friendly tanpa ketergantungan library pihak ketiga. | **Disetujui** |
| **DEC-004** | 03/11/2026 | Git Branching Model | Menggunakan branch `main` (produksi tertutup), `develop` (integrasi), dan 13 branch `tahap/<nomor>-<nama>` sebagai feature branches. | Menjaga riwayat pengembangan tetap teratur, transparan, dan dapat diverifikasi per tahapan pengerjaan. | **Disetujui** |
| **DEC-005** | 03/11/2026 | Anti-Overlap Rental Rule | Menerapkan *Pessimistic Locking* (`SELECT ... FOR UPDATE`) pada level database transaction saat pemesanan sewa kamera. | Mencegah *race condition* dan bentrokan jadwal sewa unit fisik kamera yang sama pada rentang tanggal bertubrukan. | **Disetujui** |
| **DEC-006** | 03/11/2026 | UI Design System Compliance | Mengunci 9 prototype UI/UX di folder `template/` sebagai aset *READ-ONLY* dengan palet warna Royal Blue (`#2563eb`) dan Deep Slate (`#0b192e`). | Menjamin konsistensi visual setinggi mungkin antara implementasi produksi dengan desain acuan yang telah disetujui. | **Disetujui** |
| **DEC-007** | 03/11/2026 | Database Engine & Schema | Menggunakan MySQL InnoDB dengan collation `utf8mb4_unicode_ci` dan 22 tabel relasional. | Menjamin integritas referensial antar entitas, kepatuhan ACID transaksi keuangan, dan dukungan emoji/karakter internasional. | **Disetujui** |
| **DEC-008** | 03/11/2026 | PDO Singleton Connection | Menggunakan singleton `App\Core\Database::getConnection()` untuk koneksi basis data. | Mencegah pembukaan banyak koneksi tidak perlu per request lifecycle, efisien memori, dan mengisolasi DSN database. | **Disetujui** |
| **DEC-009** | 03/11/2026 | Migration Runner Architecture | Menggunakan berkas DDL SQL terpusat `database/schema.sql` dengan eksekutor CLI `database/migrate.php`. | Memberikan transparansi skema 100%, mudah di-audit, dan bebas dari dependensi library migrasi pihak ketiga. | **Disetujui** |
| **DEC-010** | 03/11/2026 | Password Hashing Standard | Menggunakan algoritma **BCRYPT** (`PASSWORD_BCRYPT`) melalui `password_hash()` dan `password_verify()`. | Standar industri PHP yang aman terhadap brute-force dan rainbow tables, memiliki salt otomatis bawaan. | **Disetujui** |
| **DEC-011** | 03/11/2026 | Multi-Identifier Login | Mengizinkan login melalui **Username, Email, atau Nomor HP** dalam satu kolom input tunggal. | Sesuai desain mockup prototype (*"Email atau No. HP"*) dan memudahkan pengguna lokal Indonesia login dengan nomor WhatsApp atau username. | **Disetujui** |
| **DEC-012** | 03/11/2026 | Middleware Pipeline | Memperluas `App\Core\Router` untuk mengeksekusi pipeline middleware (`GuestMiddleware`, `AuthMiddleware`, `RoleMiddleware`) sebelum memanggil controller. | Menerapkan *Separation of Concerns*, mengisolasi proteksi route guard dari logika controller. | **Disetujui** |
| **DEC-013** | 03/11/2026 | Session Fixation Protection | Mengeksekusi `session_regenerate_id(true)` sesaat setelah autentikasi password terbukti valid. | Menghancurkan ID sesi lama dan mencegah pengambilalihan akun korban via session fixation attack. | **Disetujui** |
| **DEC-014** | 03/11/2026 | Demo Credentials Convenience | Menyediakan akun instan `admin:admin`, `user:user`, dan `mitra:mitra` dengan tombol 1-klik di halaman login. | Memenuhi permintaan khusus Project Owner untuk mempermudah evaluasi dosen dan demo tim tanpa mengurangi keamanan sistem. | **Disetujui** |
