<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\CustomerProfileController;
use App\Controllers\KycController;
use App\Controllers\HomeController;
use App\Controllers\CatalogController;
use App\Controllers\ProductDetailController;
use App\Controllers\PartnerDashboardController;
use App\Controllers\BookingController;
use App\Controllers\SalesController;
use App\Controllers\AdminDashboardController;
use App\Controllers\ConsignmentController;
use App\Controllers\EscrowController;
use App\Controllers\PartnerWalletController;
use App\Controllers\NotificationController;

/**
 * Web Routes - CamHub
 */

// 1. Halaman Beranda Publik Dinamis (Tahap 05: Mockup Beranda.png)
Router::get('/', [HomeController::class, 'index']);

// 2. Katalog Produk & Filter Multi-Kriteria (Tahap 05: Mockup Katalog Produk.png)
Router::get('/katalog', [CatalogController::class, 'index']);

// 3. Detail Produk & Kalkulator Sewa Interaktif (Tahap 05: Mockup Detail Produk.png)
Router::get('/produk/{slug}', [ProductDetailController::class, 'show']);
Router::post('/api/rental/calculate', [ProductDetailController::class, 'calculateEstimate']);

// 4. Endpoint Health Check API
Router::get('/api/health', function () {
    json_response([
        'status' => 'OK',
        'app' => 'CamHub',
        'environment' => env('APP_ENV', 'local'),
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0.0',
    ]);
});

// 5. Autentikasi Pengguna (Tamu / Guest Only)
Router::get('/login', [AuthController::class, 'showLoginForm'], ['guest']);
Router::post('/login', [AuthController::class, 'login'], ['guest']);
Router::get('/register', [AuthController::class, 'showRegisterForm'], ['guest']);
Router::post('/register', [AuthController::class, 'register'], ['guest']);

// 6. Logout Pengguna (Wajib Terautentikasi / Auth)
Router::post('/logout', [AuthController::class, 'logout'], ['auth']);
Router::get('/logout', [AuthController::class, 'logout'], ['auth']);

// 7. Dashboard Peran Terproteksi (RBAC Route Guard)
Router::get('/dashboard/pelanggan', [CustomerProfileController::class, 'dashboard'], ['auth', 'role:customer']);

// 8. Dashboard Administrator (Tahap 09, 11 & 12: Pusat Kendali Super Administrator)
Router::get('/dashboard/admin', [AdminDashboardController::class, 'index'], ['auth', 'role:admin']);
Router::get('/dashboard/admin/kyc', [AdminDashboardController::class, 'kyc'], ['auth', 'role:admin']);
Router::post('/dashboard/admin/kyc/{id}/approve', [AdminDashboardController::class, 'approveKyc'], ['auth', 'role:admin']);
Router::post('/dashboard/admin/kyc/{id}/reject', [AdminDashboardController::class, 'rejectKyc'], ['auth', 'role:admin']);
Router::get('/dashboard/admin/transaksi', [AdminDashboardController::class, 'transactions'], ['auth', 'role:admin']);
Router::post('/dashboard/admin/transaksi/sengketa/{id}/resolusi', [AdminDashboardController::class, 'resolveDispute'], ['auth', 'role:admin']);
Router::post('/dashboard/admin/transaksi/pencairan/{id}/proses', [AdminDashboardController::class, 'processWithdrawal'], ['auth', 'role:admin']);
Router::get('/dashboard/admin/jadwal', [AdminDashboardController::class, 'schedule'], ['auth', 'role:admin']);
Router::get('/dashboard/admin/denda', [AdminDashboardController::class, 'fines'], ['auth', 'role:admin']);
Router::post('/dashboard/admin/denda/simpan', [AdminDashboardController::class, 'storeFine'], ['auth', 'role:admin']);
Router::post('/dashboard/admin/denda/{id}/lunas', [AdminDashboardController::class, 'payFine'], ['auth', 'role:admin']);
Router::get('/dashboard/admin/pengaturan', [AdminDashboardController::class, 'settings'], ['auth', 'role:admin']);
Router::post('/dashboard/admin/pengguna/{id}/status', [AdminDashboardController::class, 'toggleUserStatus'], ['auth', 'role:admin']);
Router::get('/dashboard/admin/laporan', [AdminDashboardController::class, 'reports'], ['auth', 'role:admin']);
Router::get('/dashboard/admin/laporan/export', [AdminDashboardController::class, 'exportReport'], ['auth', 'role:admin']);

// 9. Dashboard Mitra Lapak (Tahap 06 & 10: Mockup CamHub — Dashboard Mitra Lapak.png)
Router::get('/dashboard/mitra', [PartnerDashboardController::class, 'index'], ['auth', 'role:partner']);
Router::get('/dashboard/mitra/produk', [PartnerDashboardController::class, 'products'], ['auth', 'role:partner']);
Router::get('/dashboard/mitra/produk/tambah', [PartnerDashboardController::class, 'createProduct'], ['auth', 'role:partner']);
Router::post('/dashboard/mitra/produk/simpan', [PartnerDashboardController::class, 'storeProduct'], ['auth', 'role:partner']);
Router::get('/dashboard/mitra/produk/{id}/edit', [PartnerDashboardController::class, 'editProduct'], ['auth', 'role:partner']);
Router::post('/dashboard/mitra/produk/{id}/update', [PartnerDashboardController::class, 'updateProduct'], ['auth', 'role:partner']);
Router::post('/dashboard/mitra/produk/{id}/hapus', [PartnerDashboardController::class, 'deleteProduct'], ['auth', 'role:partner']);
Router::get('/dashboard/mitra/produk/{id}/unit', [PartnerDashboardController::class, 'manageUnits'], ['auth', 'role:partner']);
Router::post('/dashboard/mitra/produk/{id}/unit/simpan', [PartnerDashboardController::class, 'storeUnit'], ['auth', 'role:partner']);
Router::get('/dashboard/mitra/jadwal', [PartnerDashboardController::class, 'schedule'], ['auth', 'role:partner']);
Router::get('/dashboard/mitra/transaksi', [PartnerDashboardController::class, 'transactions'], ['auth', 'role:partner']);
Router::get('/dashboard/mitra/saldo', [PartnerWalletController::class, 'index'], ['auth', 'role:partner']);
Router::post('/dashboard/mitra/saldo/tarik', [PartnerWalletController::class, 'withdraw'], ['auth', 'role:partner']);
Router::get('/dashboard/mitra/saldo/export', [PartnerWalletController::class, 'export'], ['auth', 'role:partner']);
Router::get('/dashboard/mitra/pengaturan', [PartnerDashboardController::class, 'settings'], ['auth', 'role:partner']);
Router::post('/dashboard/mitra/pengaturan/update', [PartnerDashboardController::class, 'updateSettings'], ['auth', 'role:partner']);

// 10. Profil Pelanggan & Verifikasi KYC (Khusus Customer)
Router::get('/profil', [CustomerProfileController::class, 'showProfile'], ['auth', 'role:customer']);
Router::post('/profil/edit', [CustomerProfileController::class, 'updateProfile'], ['auth', 'role:customer']);

Router::get('/kyc', [KycController::class, 'showKycForm'], ['auth', 'role:customer']);
Router::post('/kyc/submit', [KycController::class, 'submit'], ['auth', 'role:customer']);
Router::get('/kyc/status', [KycController::class, 'showKycStatus'], ['auth', 'role:customer']);

// 11. Penyewaan & Pemesanan Kamera (Tahap 07: Mockup CamHub — Booking & Checkout.png)
Router::get('/checkout/{slug}', [BookingController::class, 'checkout'], ['auth']);
Router::post('/checkout/{slug}/proses', [BookingController::class, 'process'], ['auth']);
Router::get('/booking/{id}/sukses', [BookingController::class, 'success'], ['auth']);
Router::get('/pesanan-saya', [BookingController::class, 'customerOrders'], ['auth', 'role:customer']);

// 12. Marketplace Jual Beli Kamera (Tahap 08: Direct Purchase & Checkout)
Router::get('/beli/{slug}', [SalesController::class, 'checkout'], ['auth']);
Router::post('/beli/{slug}/proses', [SalesController::class, 'processCheckout'], ['auth']);
Router::get('/pesanan/beli/{id}/sukses', [SalesController::class, 'success'], ['auth']);
Router::get('/jual-beli-saya', [SalesController::class, 'customerPurchases'], ['auth', 'role:customer']);

// 13. Sistem Titip Jual Kamera — Consignment (Tahap 09)
Router::get('/titip-jual/ajukan', [ConsignmentController::class, 'create'], ['auth', 'role:customer']);
Router::post('/titip-jual/ajukan', [ConsignmentController::class, 'store'], ['auth', 'role:customer']);
Router::get('/titip-jual/saya', [ConsignmentController::class, 'customerConsignments'], ['auth', 'role:customer']);
Router::get('/dashboard/mitra/titip-jual', [ConsignmentController::class, 'partnerConsignments'], ['auth', 'role:partner']);
Router::post('/dashboard/mitra/titip-jual/{id}/inspeksi', [ConsignmentController::class, 'partnerInspect'], ['auth', 'role:partner']);
Router::post('/dashboard/mitra/titip-jual/{id}/setujui', [ConsignmentController::class, 'partnerApprove'], ['auth', 'role:partner']);

// 14. Penyelesaian Transaksi, Rekening Bersama Escrow & Sengketa (Tahap 10)
Router::post('/pesanan/sewa/{id}/konfirmasi-selesai', [EscrowController::class, 'confirmRental'], ['auth', 'role:customer']);
Router::post('/pesanan/beli/{id}/konfirmasi-terima', [EscrowController::class, 'confirmSale'], ['auth', 'role:customer']);
Router::post('/pesanan/dispute', [EscrowController::class, 'dispute'], ['auth', 'role:customer']);

// 15. Notifikasi & Cetak Dokumen Resmi (Tahap 12: Print-to-PDF & In-App Notifications)
Router::get('/pesanan/sewa/{id}/cetak', [BookingController::class, 'printReceipt'], ['auth']);
Router::get('/pesanan/beli/{id}/cetak', [SalesController::class, 'printInvoice'], ['auth']);
Router::get('/notifikasi', [NotificationController::class, 'index'], ['auth']);
Router::post('/notifikasi/{id}/baca', [NotificationController::class, 'markAsRead'], ['auth']);
Router::post('/notifikasi/baca-semua', [NotificationController::class, 'markAllAsRead'], ['auth']);
Router::get('/api/notifikasi/unread', [NotificationController::class, 'apiUnreadCount'], ['auth']);
