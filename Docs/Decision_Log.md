# DECISION LOG — CATATAN KEPUTUSAN ARSITEKTUR & TEKNIS

> **PROYEK:** CamHub – Camera Marketplace Platform  
> **PROJECT OWNER:** Yogi Ario  

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
