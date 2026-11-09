# Laporan Bug: Foreign Key Constraint Violation Saat Menerbitkan Denda di Dashboard Admin

## 1. Identifikasi Masalah
- **ID Laporan:** BUG-007
- **Tanggal Ditemukan:** 05 November 2026
- **Tanggal Selesai:** 05 November 2026
- **Status:** RESOLVED (SELESAI DIVERIFIKASI)
- **Komponen Terdampak:**
  - `app/Services/AdminService.php` (`storeFine()`)
  - `app/Controllers/AdminDashboardController.php` (`fines()`)
  - `resources/views/admin/fines.php`
- **Tingkat Keparahan:** Tinggi (Database Integrity Exception / User Facing SQL Error)

---

## 2. Deskripsi Masalah Berdasarkan Screenshot 2 (`media_1793840120381_5013de05.jpg`)
Saat admin mencoba menerbitkan tagihan denda baru pada URL `http://localhost/camhub/public/dashboard/admin/denda`, muncul pesan error SQL:
```
Gagal menerbitkan denda: SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails (`camhub_db`.`fines`, CONSTRAINT `fk_fines_booking` FOREIGN KEY (`booking_id`) REFERENCES `rental_bookings` (`id`) ON DELETE CASCADE)
```

### Akar Masalah:
1. **Tidak Ada Validasi Keberadaan Record di `rental_bookings`:**
   Pada `AdminService::storeFine()`, sistem hanya mengecek `$bookingId <= 0`. Jika `$bookingId` bernilai positif tetapi tidak ada record dengan ID tersebut di tabel `rental_bookings`, sistem langsung mengeksekusi `INSERT INTO fines`. MySQL menolak query dengan error FK Constraint `1452`.
2. **Input Bebas Angka Manual di Form:**
   Form di `resources/views/admin/fines.php` menyediakan input teks mentah `<input type="number" id="booking_id" name="booking_id">`. Pengguna/admin dapat mengetikkan sembarang angka yang tidak terdaftar di sistem.
3. **Penyajian Error Raw SQL ke Layar Pengguna:**
   Exception ditangkap dan pesan teknis MySQL (`SQLSTATE[23000]...`) langsung ditampilkan ke flash message, memberikan pengalaman pengguna yang buruk dan membocorkan struktur tabel.

---

## 3. Rencana Solusi
1. **Validasi Query Pra-Insert di `AdminService::storeFine()`:**
   - Memeriksa apakah `rental_bookings` dengan ID tersebut ada di database sebelum insert.
   - Jika tidak ada, kembalikan pesan error ramah: `"Gagal: Pesanan sewa dengan ID #{$bookingId} tidak ditemukan dalam sistem."`
2. **Penyediaan Dropdown Pemesanan Sewa di `AdminDashboardController`:**
   - Mengambil data pesanan sewa aktif/terkini (`rental_bookings`) dari database.
   - Menjadikan form input sebagai dropdown `<select>` yang menampilkan daftar pesanan valid (format: `#RENT-00001 — Nama Penyewa (Nama Kamera)`).
   - Menyediakan fallback input ID manual jika diperlukan, dengan jaminan validasi yang aman.

---

## 4. Solusi & Implementasi
1. **Repository Helper (`AdminRepository::getRecentRentalBookings()`):**
   - Menambahkan query pengambil daftar sewa terkini dengan join ke `users` dan `product_units` untuk mendapatkan identitas penyewa dan unit kamera yang disewa.
2. **Service Validation & Safe Error Handling (`AdminService::storeFine()`):**
   - Menambahkan query verifikasi eksistensi booking sebelum query INSERT:
     `SELECT id FROM rental_bookings WHERE id = :id LIMIT 1`
   - Jika booking ID tidak ditemukan, melempar `InvalidArgumentException` dengan pesan ramah: `"Pesanan sewa dengan ID #... tidak ditemukan di sistem."`
   - Membungkus query insert dengan blok try-catch `PDOException` untuk mencegah bocornya pesan SQLSTATE ke pengguna.
3. **Controller & View Form Dropdown (`AdminDashboardController::fines()` & `fines.php`):**
   - Mengoper `$rentalBookings` ke view `admin.fines`.
   - Mengganti input manual `<input type="number">` menjadi `<select name="booking_id" required>` yang berisi opsi-opsi pesanan sewa riil di database.

---

## 5. Hasil Verifikasi
- Pengujian browser via Chrome DevTools pada `http://localhost/camhub/public/dashboard/admin/denda`:
  - Elemen form terkonfirmasi berupa `<select>` dengan 35 opsi pesanan sewa riil.
  - Tampilan dropdown menyajikan kode booking, nama penyewa, dan nama produk dengan format jelas.
- Regression Tests:
  - Seluruh 15 test suite lulus 100% (**147 / 147 PASS**).
- Bukti Tangkapan Layar:
  - Form denda dengan dropdown tervalidasi: `Docs/error/image/evidence_admin_denda_fixed.png`
