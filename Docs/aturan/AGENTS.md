# AGENTS.md — Template Aturan Teknis Modular (CamHub PHP Native)

> **STATUS:** ATURAN TEKNIS MODULAR (SCOPED AGENT RULES)
> **LOKASI PENEMPATAN:** Gunakan berkas ini sebagai template acuan untuk sub-folder arsitektur atau modul fitur (misal: `app/Services/Rental/AGENTS.md` atau `app/Controllers/AGENTS.md`).
> **HIERARKI TATA KELOLA:** Berkas ini menginduk langsung ke [`Docs/aturan/universal-rule.md`](universal-rule.md) dan [`GEMINI.md`](GEMINI.md). **Jika terjadi kontradiksi antara aturan modul ini dengan aturan root/universal, aturan universal dan persetujuan Yogi Ario selalu menjadi acuan tertinggi.**
> **PRINSIP UTAMA:** *High Cohesion, Loose Coupling. No Plan, No Code. No Evidence, No Completion.*

---

## 1. Identitas & Ruang Lingkup Modul

- **Nama Modul / Sub-Sistem:** `[Isi saat adopsi: misal RentalService, OrderService, ConsignmentService, AuthService, KYCService]`
- **Bahasa & Runtime:** PHP 8.x (PHP Native dengan PDO Database Access)
- **Tanggung Jawab Utama (Single Responsibility):**
  `[Jelaskan 1-2 kalimat fungsi spesifik modul ini bagi ekosistem CamHub]`
- **Batasan Ruang Lingkup:**
  - **In-Scope (Termasuk):** `[Fitur dan logika bisnis yang menjadi hak eksklusif modul ini]`
  - **Out-of-Scope (Dilarang di Modul Ini):** `[Logika yang harus didelegasikan ke layer/service lain]`

---

## 2. Enkapsulasi & Batasan Dependensi (Dependency Boundaries)

Untuk mencegah kode menjadi *spaghetti* dan saling ketergantungan liar (*tight coupling*):

1. **Komponen yang Diizinkan Diakses:**
   - Database Connection PDO via `config/database.php` atau dependency injection repository.
   - Model entitas terkait di `app/Models/`.
   - Helper dan Validator terkait di `app/Helpers/` dan `app/Validators/`.
2. **Larangan Akses Liar:**
   - Dilarang memanipulasi langsung query SQL di dalam Controller; semua akses data wajib melewati `app/Repositories/`.
   - Dilarang menempatkan business logic kritis di dalam View (`resources/views/`).
   - Komunikasi antar-domain wajib melalui Service Method resmi dengan input/output parameter yang jelas.
3. **Penyimpanan State & Transaksi:**
   - Operasi multi-tabel (misal booking + payment record, order + decrement stock) **wajib menggunakan MySQL Database Transaction (`$pdo->beginTransaction()`, `$pdo->commit()`, `$pdo->rollBack()`)**.
   - Session pengguna dikelola terpusat melalui helper session yang aman.

---

## 3. Pola Arsitektur Layering

Struktur penempatan file kode PHP Native di CamHub:

```text
app/
├── Controllers/                   # Menerima request HTTP, validasi request, memanggil service, render view/JSON
├── Services/                      # Logika bisnis inti (rental scheduling, pricing, consignment, escrow)
├── Repositories/                  # Abstraksi query database (PDO prepared statements)
├── Models/                        # Representasi entitas data / DTO
├── Middleware/                    # Guard autentikasi, role check (Customer, Partner, Admin), CSRF check
├── Validators/                    # Validasi input server-side
└── Helpers/                       # Fungsi bantuan umum (format rupiah, sanitasi, redirect)
```

---

## 4. Konvensi Penamaan & Standar Kode

- **Penamaan Berkas PHP:** `PascalCase.php` (misal: `RentalController.php`, `BookingRepository.php`, `AuthMiddleware.php`).
- **Penamaan Method / Fungsi:** `camelCase()` (misal: `checkAvailability()`, `calculateTotal()`).
- **Penamaan Variabel:** `camelCase` (misal: `productId`, `startDate`, `bookingStatus`).
- **Penamaan Tabel & Kolom Database:** `snake_case` (misal: `rental_bookings`, `created_at`, `user_id`).
- **Error Handling:**
  - Tangani exception menggunakan `try-catch` dengan log pesan error ke `storage/logs/error.log`.
  - Jangan mengekspos raw error database (SQL error / credential) ke antarmuka pengguna akhir.
  - Berikan feedback error yang ramah pengguna sesuai antarmuka prototype.

---

## 5. Pengujian Mandiri Lokal (Local Test Suite)

Sebelum mengajukan perubahan modul ini ke tahap integrasi proyek global:

- **Perintah Uji:** Jalankan skrip test terkait di folder `tests/` (misal: `php tests/run_tests.php`).
- **Target Kelulusan:** `100% PASS (0 GAGAL / 0 ERROR)`.
- **Cakupan Pengujian:** Minimal menguji skenario sukses (*happy path*), skenario gagal/validasi, dan *edge cases* (seperti jadwal booking bentrok atau stok habis).

---

## 6. Checklist Sebelum Menyelesaikan Tugas di Modul Ini

- [ ] Perubahan kode mematuhi batas tanggung jawab layer (Controller / Service / Repository / View).
- [ ] Query database menggunakan PDO Prepared Statements tanpa konkatenasi string SQL mentah.
- [ ] Telah melewati uji mandiri dengan hasil **100% PASS**.
- [ ] Rencana perubahan tetap dilaporkan dan disetujui melalui *Approval Gate* Yogi Ario di tingkat proyek.
