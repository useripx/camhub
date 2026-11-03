# KAMUS DATA BASIS DATA — CAMHUB

> **PROYEK:** CamHub – Camera Marketplace Platform  
> **PROJECT OWNER:** Yogi Ario  
> **BASIS DATA:** `camhub_db`  
> **ENGINE:** MySQL InnoDB (UTF8MB4)  
> **VERSI:** 1.0 (22 Tabel Relasional)  
> **LOKASI DOKUMEN:** [`Docs/scema/database_dictionary.md`](database_dictionary.md)  
> **DIAGRAM ERD:** [`Docs/scema/ERD.md`](ERD.md)

---

## 01. Ringkasan 22 Tabel Relasional

| No | Nama Tabel | Entitas Utama | Relasi Foreign Key Utama | Deskripsi Singkat |
|:---:|---|---|---|---|
| 01 | `roles` | Otoritas Sistem | - | Menyimpan 3 peran: `admin`, `partner`, `customer`. |
| 02 | `users` | Pengguna Akun | `role_id -> roles.id` | Kredensial email, no. HP, hash password, dan status aktif. |
| 03 | `user_profiles` | Profil Pengguna | `user_id -> users.id` | Alamat fisik, kota domisili, dan foto avatar. |
| 04 | `kyc_submissions` | Dokumen KYC | `user_id -> users.id`, `verified_by -> users.id` | NIK KTP, tanggal lahir, foto KTP, dan selfie verifikasi. |
| 05 | `partners` | Mitra Lapak | `user_id -> users.id` | Profil bisnis mitra dan persentase komisi platform. |
| 06 | `partner_stores` | Toko Mitra | `partner_id -> partners.id` | Nama lapak toko (misal: "Yogi Camera"), alamat toko, kota. |
| 07 | `categories` | Kategori Produk | - | Pengelompokan produk (*Kamera Body, Lensa, Audio, dll.*). |
| 08 | `products` | Induk Produk | `store_id -> partner_stores.id`, `category_id -> categories.id` | Data katalog, rating, harga jual, dan status rental/jual. |
| 09 | `product_images` | Foto Produk | `product_id -> products.id` | Galeri gambar produk dan tanda gambar utama. |
| 10 | `product_units` | Unit Fisik | `product_id -> products.id` | Nomor seri fisik unit dan status ketersediaan sewa. |
| 11 | `rental_pricing` | Paket Harga Sewa | `product_id -> products.id` | Tarif per durasi hari (*1, 3, 7, 14 Hari*) dan diskon. |
| 12 | `rental_bookings` | Pesanan Sewa | `user_id -> users.id`, `product_unit_id -> product_units.id` | Tanggal sewa, durasi, biaya admin Rp 5.000, status sewa. |
| 13 | `sales_orders` | Pesanan Beli | `user_id -> users.id` | Nomor order jual beli, alamat kirim, status pengiriman. |
| 14 | `sales_order_items`| Rincian Pembelian| `order_id -> sales_orders.id`, `product_id -> products.id` | Jumlah barang dan harga satuan produk yang dibeli. |
| 15 | `consignments` | Titip Jual | `user_id -> users.id`, `store_id -> partner_stores.id` | Alur penitipan barang, negosiasi harga, dan komisi. |
| 16 | `payments` | Pembayaran | `user_id -> users.id` | Rekam pembayaran terpusat (Escrow, Transfer, E-Wallet). |
| 17 | `payment_transactions`| Gateway Logs | `payment_id -> payments.id` | ID transaksi pihak ketiga dan respons payload. |
| 18 | `escrow_transactions`| Saldo Tertahan | `payment_id -> payments.id`, `booking_id -> rental_bookings.id` | Penahanan dana sewa hingga unit dikembalikan aman. |
| 19 | `fines` | Denda Sewa | `booking_id -> rental_bookings.id` | Denda keterlambatan/kerusakan barang sewa. |
| 20 | `inspections` | Uji Fisik Unit | `product_unit_id -> product_units.id`, `inspector_id -> users.id` | Hasil inspeksi fisik unit sebelum & sesudah rental. |
| 21 | `notifications` | Notifikasi Akun | `user_id -> users.id` | Pengingat transaksi, status KYC, dan pesanan. |
| 22 | `audit_logs` | Jejak Audit | `user_id -> users.id` | Log mutasi data kritis sistem untuk keperluan audit. |

---

## 02. Struktur Rinci Tabel Kunci

### `products` (Katalog Produk)
- `id` (INT AUTO_INCREMENT PK)
- `store_id` (INT FK -> `partner_stores.id`)
- `category_id` (INT FK -> `categories.id`)
- `name` (VARCHAR 200)
- `slug` (VARCHAR 220 UNIQUE INDEX)
- `description` (TEXT)
- `condition_note` (VARCHAR 150) — *Default: 'Kondisi sangat baik · Siap disewa'*
- `is_rental` (TINYINT 1) — *Default: 1*
- `is_sale` (TINYINT 1) — *Default: 0*
- `sale_price` (DECIMAL 15,2) — *Default: 0.00*
- `stock_quantity` (INT) — *Default: 0*
- `rating_avg` (DECIMAL 3,2) — *Default: 5.00*
- `review_count` (INT) — *Default: 0*

### `rental_bookings` (Transaksi Sewa & Booking)
- `id` (INT AUTO_INCREMENT PK)
- `user_id` (INT FK -> `users.id`)
- `product_unit_id` (INT FK -> `product_units.id`)
- `start_date` (DATE) — *Tanggal Ambil Unit*
- `end_date` (DATE) — *Tanggal Kembali Unit*
- `total_days` (INT) — *Durasi Sewa*
- `daily_price` (DECIMAL 15,2)
- `subtotal` (DECIMAL 15,2)
- `admin_fee` (DECIMAL 15,2) — *Biaya admin Rp 5.000*
- `total_amount` (DECIMAL 15,2) — *Total tagihan*
- `payment_status` (ENUM: 'unpaid', 'paid', 'refunded')
- `rental_status` (ENUM: 'pending', 'confirmed', 'active', 'returned', 'completed', 'cancelled')
- Index gabungan: `idx_bookings_dates` (`product_unit_id`, `start_date`, `end_date`) untuk optimasi kueri pencegahan double-booking.
