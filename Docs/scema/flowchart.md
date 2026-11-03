# ARSITEKTUR & DIAGRAM ALUR SISTEM (FLOWCHART) — CAMHUB

> **PROYEK:** CamHub – Camera Marketplace Platform  
> **TIM PENGEMBANG:** Kelompok 1  
> 1. **Yogi Ario Pratama** (Project Owner & Lead Developer)  
> 2. **Muhammad Regieyanto** (Backend & System Architect)  
> 3. **Ivan Farrel Nanda Kristanto** (Core Routing & Security Engineer)  
> 4. **Krisna Mukti** (Quality Assurance & Presentation Coordinator)  
>  
> **LOKASI DOKUMEN:** [`Docs/scema/flowchart.md`](flowchart.md)  
> **DOKUMEN TERKAIT:** [`Docs/scema/ERD.md`](ERD.md) & [`Docs/scema/database_dictionary.md`](database_dictionary.md)  
> **FORMAT:** Mermaid Diagram Code (G规范 GFM Compliant)

---

## 1. Diagram Alur Global Pengguna (Master Platform Flowchart)

Diagram ini menggambarkan alur perjalanan utama (*User Journey*) seluruh aktor dalam ekosistem CamHub: Pengunjung Umum (*Guest*), Pelanggan (*Customer*), Mitra Toko (*Partner*), dan Administrator (*Super Admin*).

```mermaid
flowchart TD
    Start(["Mulai Akses Platform CamHub"]) --> Home["Kunjungi Beranda Publik (/)"]
    Home --> Browse["Jelajahi Etalase & Katalog Produk (/katalog)"]
    
    Browse --> SearchFilter{"Gunakan Fitur?"}
    SearchFilter -->|"Pencarian Kata Kunci"| ResultList["Daftar Kamera Sesuai Kata Kunci"]
    SearchFilter -->|"Filter Kategori & Harga"| ResultList
    SearchFilter -->|"Sorting Rating / Harga"| ResultList
    
    ResultList --> ViewDetail["Buka Halaman Detail Produk (/produk/{slug})"]
    ViewDetail --> InspectUnit["Cek Spesifikasi, Foto Galeri & Profil Mitra"]
    InspectUnit --> RentCalc["Simulasi Kalkulator Tarif Sewa & Durasi"]
    
    RentCalc --> ActionChoice{"Aksi Pengguna"}
    ActionChoice -->|"Ingin Sewa / Beli"| CheckAuth{"Sudah Login?"}
    ActionChoice -->|"Buka Toko / Lapak"| CheckAuth
    
    CheckAuth -->|"Belum"| AuthPage["Halaman Login / Register (/login, /register)"]
    AuthPage --> AuthProcess["Proses Autentikasi & Verifikasi BCRYPT"]
    AuthProcess --> CheckRole{"Identifikasi Peran Akun"}
    
    CheckAuth -->|"Sudah"| CheckRole
    
    CheckRole -->|"Customer"| CustomerFlow["Dashboard Pelanggan (/dashboard/pelanggan)"]
    CheckRole -->|"Partner"| PartnerFlow["Dashboard Lapak Mitra (/dashboard/mitra)"]
    CheckRole -->|"Admin"| AdminFlow["Dashboard Administrator (/dashboard/admin)"]
    
    CustomerFlow --> CheckKYC{"Status Verifikasi KYC?"}
    CheckKYC -->|"Unverified / Rejected"| SubmitKYC["Formulir KYC (/kyc): NIK, Foto KTP & Swafoto"]
    SubmitKYC --> PendingReview["Status Pending: Menunggu Verifikasi Admin"]
    
    CheckKYC -->|"Verified"| BookingFlow["Pemesanan Sewa Kamera (/checkout)"]
    BookingFlow --> EscrowPay["Pembayaran Rekening Bersama Escrow"]
    EscrowPay --> UnitShipped["Unit Dikirim oleh Toko Mitra"]
    UnitShipped --> RentalUse["Masa Sewa Kamera Berjalan"]
    RentalUse --> UnitReturn["Pengembalian Unit & Inspeksi Kondisi"]
    UnitReturn --> EscrowRelease["Pelepasan Dana ke Mitra & Selesai"]
```

---

## 2. Diagram Alur Website Publik & Katalog Produk (Tahap 05 Flowchart)

Diagram ini mengilustrasikan alur pemrosesan data di sisi Front Controller, Router, Service, dan Basis Data saat pengguna menjelajah Beranda, Katalog, dan Detail Produk.

```mermaid
flowchart TD
    subgraph ClientLayer ["1. Antarmuka Klien (Web Browser)"]
        ReqHome["Request GET /"]
        ReqCatalog["Request GET /katalog?q=&category=&min=&max=&sort="]
        ReqDetail["Request GET /produk/{slug}"]
        ReqCalc["Request POST /api/rental/calculate"]
    end

    subgraph ControllerLayer ["2. Controller & Routing Pipeline"]
        Router{"Router Native App Core Router"}
        HController["HomeController@index"]
        CController["CatalogController@index"]
        DController["ProductDetailController@show"]
        CalcController["ProductDetailController@calculateEstimate"]
    end

    subgraph ServiceLayer ["3. Lapisan Bisnis & Layanan (Services)"]
        CatService["ProductCatalogService: Sanitasi, Parsing Filter & Pagination"]
        RentService["RentalCalculatorService: Kalkulasi Tarif, Diskon Durasi & Escrow"]
    end

    subgraph RepoLayer ["4. Repositori Data & Akses Basis Data"]
        PRepo["ProductRepository: Query Dinamis WHERE, JOIN, ORDER BY, LIMIT"]
        CRepo["CategoryRepository: Master Kategori & Total Unit"]
        DB[(MySQL camhub_db: products, categories, stores, units)]
    end

    ReqHome --> Router
    ReqCatalog --> Router
    ReqDetail --> Router
    ReqCalc --> Router

    Router -->|"URI: /"| HController
    Router -->|"URI: /katalog"| CController
    Router -->|"URI: /produk/{slug}"| DController
    Router -->|"URI: /api/rental/calculate"| CalcController

    HController --> CatService
    CController --> CatService
    DController --> CatService
    DController --> RentService
    CalcController --> RentService

    CatService --> PRepo
    CatService --> CRepo
    RentService --> PRepo

    PRepo --> DB
    CRepo --> DB

    DB -->|"Data Kamera, Toko, & Kategori"| PRepo
    PRepo -->|"DTO Entitas Produk"| CatService
    CatService -->|"View Model Payload"| CController
    CController -->|"Render HTML"| ResCatalog["View resources/views/public/catalog.php"]
```

---

## 3. Diagram Alur Logika Filter & Pencarian Katalog

Diagram alur pembuatan query SQL dinamis dan penyaringan data kamera di `CatalogController` dan `ProductRepository`.

```mermaid
flowchart TD
    StartFilter["Pengguna Mengirim Parameter Filter di Katalog"] --> ParseParams["Tangkap Input: keyword, category, min_price, max_price, sort, page"]
    
    ParseParams --> Sanitize["Sanitasi Input: htmlspecialchars & intval"]
    Sanitize --> InitQuery["Inisialisasi SQL: SELECT * FROM products JOIN stores JOIN categories"]
    
    InitQuery --> CheckQ{"Parameter keyword terisi?"}
    CheckQ -->|"Ya"| AddQ["Tambah: AND (name LIKE :kw OR description LIKE :kw)"]
    CheckQ -->|"Tidak"| CheckCat{"Parameter category terisi?"}
    
    AddQ --> CheckCat
    CheckCat -->|"Ya"| AddCat["Tambah: AND category_id = :catId"]
    CheckCat -->|"Tidak"| CheckType{"Parameter type sewa / jual?"}
    
    AddCat --> CheckType
    CheckType -->|"Sewa"| AddTypeSewa["Tambah: AND is_rental = 1"]
    CheckType -->|"Jual"| AddTypeJual["Tambah: AND is_sale = 1"]
    CheckType -->|"Semua"| CheckPrice{"Parameter rentang harga?"}
    
    AddTypeSewa --> CheckPrice
    AddTypeJual --> CheckPrice
    
    CheckPrice -->|"Ya"| AddPrice["Tambah: AND daily_price BETWEEN :min AND :max"]
    CheckPrice -->|"Tidak"| CheckSort{"Pilihan Sorting?"}
    
    AddPrice --> CheckSort
    CheckSort -->|"Harga Termurah"| SortAsc["ORDER BY daily_price ASC"]
    CheckSort -->|"Harga Termahal"| SortDesc["ORDER BY daily_price DESC"]
    CheckSort -->|"Rating Tertinggi"| SortRate["ORDER BY rating_avg DESC"]
    CheckSort -->|"Default / Populer"| SortDef["ORDER BY review_count DESC, id DESC"]
    
    SortAsc --> ExecPagination["Hitung Offset = (page - 1) * limit"]
    SortDesc --> ExecPagination
    SortRate --> ExecPagination
    SortDef --> ExecPagination
    
    ExecPagination --> RunQuery["Eksekusi Prepared Statement PDO MySQL"]
    RunQuery --> OutputGrid["Tampilkan Grid Produk & Pagination Control"]
```

---

## 4. Diagram Alur Kalkulator Estimasi Biaya Sewa (Rental Calculator)

Diagram logika perhitungan biaya sewa kamera harian yang transparan dan otomatis.

```mermaid
flowchart LR
    SelectProduct["Pilih Produk Kamera"] --> SelectDuration["Pilih Durasi Sewa (Hari)"]
    SelectDuration --> InputDates["Tentukan Tanggal Ambil & Kembali"]
    
    InputDates --> BaseCalc["Biaya Dasar = Durasi x Tarif Harian"]
    
    BaseCalc --> DiscRule{"Cek Diskon Durasi"}
    DiscRule -->|"Durasi >= 14 Hari"| Disc15["Diskon 15%"]
    DiscRule -->|"Durasi >= 7 Hari"| Disc10["Diskon 10%"]
    DiscRule -->|"Durasi >= 3 Hari"| Disc05["Diskon 5%"]
    DiscRule -->|"Durasi 1-2 Hari"| Disc00["Diskon 0%"]
    
    Disc15 --> SubTotal["Subtotal Sewa = Biaya Dasar - Diskon"]
    Disc10 --> SubTotal
    Disc05 --> SubTotal
    Disc00 --> SubTotal
    
    SubTotal --> EscrowFee["+ Biaya Layanan Rekber Escrow: Rp 5.000"]
    EscrowFee --> FinalTotal["Total Estimasi Biaya Sewa"]
    FinalTotal --> DisplayEstimate["Tampilkan Rincian Transparan di Layar"]
```

---

## 5. Diagram Status Unit Fisik Kamera (Unit Lifecycle)

Diagram transisi status unit fisik kamera dari inventaris lapak mitra hingga masa sewa selesai.

```mermaid
stateDiagram-v2
    [*] --> Available: Unit Didaftarkan & Diinspeksi
    Available --> Booked: Dipesan oleh Penyewa (Checkout)
    Booked --> InUse: Unit Diterima & Sewa Dimulai
    InUse --> Returned: Unit Dikembalikan ke Toko Mitra
    Returned --> Inspecting: Pemeriksaan Kondisi Fisik & Fungsi
    
    Inspecting --> Available: Kondisi Baik & Bersih
    Inspecting --> Maintenance: Ada Kerusakan (Klaim Denda / Asuransi)
    Maintenance --> Available: Selesai Perbaikan
    
    Available --> Sold: Terjual di Marketplace
    Sold --> [*]
```

---

## 6. Diagram Alur Keamanan & Verifikasi KYC (Tahap 04)

```mermaid
flowchart TD
    User["Pelanggan"] --> AccessKYC["Buka Formulir KYC (/kyc)"]
    AccessKYC --> FillBiodata["Isi Data Diri: NIK 16 Digit, Tanggal Lahir, Gender"]
    FillBiodata --> UploadFiles["Unggah Berkas: Foto KTP & Swafoto"]
    
    UploadFiles --> ServerValidate{"Validasi Server (KycValidator)"}
    ServerValidate -->|"NIK bukan 16 digit"| RejectForm["Kembalikan Pesan Galat"]
    ServerValidate -->|"Bukan Gambar JPG/PNG/WEBP"| RejectForm
    ServerValidate -->|"Ukuran File > 2MB"| RejectForm
    
    ServerValidate -->|"Valid & Aman"| SanitizeFile["Sanitasi Nama File (Kriptografi Hash Unik)"]
    SanitizeFile --> SecureDir["Simpan ke public/uploads/kyc/ (Eksekusi PHP Dimatikan)"]
    SecureDir --> InsertDB["Simpan ke kyc_submissions (Status: pending)"]
    InsertDB --> UpdateProf["Sinkronisasi user_profiles: status = pending"]
    UpdateProf --> AuditLog["Catat Log Keamanan: KYC_SUBMIT di audit_logs"]
    AuditLog --> NotifyUser["Tampilkan Lencana 'Menunggu Review' di Dashboard"]
```

---

## 7. Diagram Alur Transaksi Escrow Rekening Bersama (Tahap 10)

```mermaid
sequenceDiagram
    autonumber
    actor Pelanggan as Penyewa (Customer)
    participant Web as Sistem Web CamHub
    participant Escrow as Rekening Bersama (Escrow)
    actor Mitra as Toko Mitra Lapak
    actor Kurir as Logistik / Kurir

    Pelanggan->>Web: Buat Pemesanan Sewa Kamera
    Web->>Escrow: Buat Virtual Account / Tagihan Pembayaran
    Pelanggan->>Escrow: Bayar Biaya Sewa + Deposit Escrow
    Escrow-->>Web: Konfirmasi Pembayaran Berhasil
    Web->>Mitra: Notifikasi: "Pesanan Masuk, Segera Siapkan Unit"
    Mitra->>Kurir: Serahkan Unit Kamera Fisik
    Kurir->>Pelanggan: Kirim & Serahkan Unit Kamera
    Pelanggan->>Web: Konfirmasi Penerimaan Unit Sesuai Deskripsi
    Note over Pelanggan,Mitra: Masa Sewa Berjalan
    Pelanggan->>Mitra: Kembalikan Unit Kamera
    Mitra->>Web: Verifikasi Unit Diterima Utuh & Sesuai
    Web->>Escrow: Perintahkan Pelepasan Dana Sewa
    Escrow->>Mitra: Teruskan Saldo Penjualan / Sewa Bersih
    Web->>Pelanggan: Kembalikan Uang Jaminan / Deposit Selesai
```

---

## 8. Ringkasan Kepatuhan & Implementasi

| Modul Alur | Diagram Terkait | File Implementasi | Status Kesiapan |
|---|---|---|:---:|
| **Master User Journey** | Bagian 1 | `routes/web.php` | Terintegrasi |
| **Katalog & Website Publik** | Bagian 2 & 3 | `HomeController`, `CatalogController`, `ProductRepository` | **Tahap 05 (Aktif)** |
| **Kalkulator Tarif Sewa** | Bagian 4 | `RentalCalculatorService`, `product_detail.php` | **Tahap 05 (Aktif)** |
| **Siklus Unit Kamera** | Bagian 5 | `product_units`, `rental_bookings` | Siap di MySQL |
| **Verifikasi KYC** | Bagian 6 | `KycController`, `KycValidator`, `KycService` | **Tahap 04 (100% PASS)** |
| **Transaksi Escrow** | Bagian 7 | `payments`, `escrow_accounts`, `orders` | Terstruktur di DB |
