<?php

namespace App\Controllers;

use App\Repositories\PartnerRepository;
use App\Repositories\CategoryRepository;
use App\Services\PartnerDashboardService;
use App\Services\PartnerProductService;

/**
 * Controller PartnerDashboardController
 * 
 * Mengelola antarmuka terpusat Dashboard Mitra Lapak,
 * inventaris kamera & unit fisik, jadwal sewa, transaksi, saldo, dan pengaturan toko.
 */
class PartnerDashboardController
{
    private PartnerRepository $partnerRepo;
    private CategoryRepository $categoryRepo;
    private PartnerDashboardService $dashboardService;
    private PartnerProductService $productService;

    public function __construct(
        ?PartnerRepository $partnerRepo = null,
        ?CategoryRepository $categoryRepo = null,
        ?PartnerDashboardService $dashboardService = null,
        ?PartnerProductService $productService = null
    ) {
        $this->partnerRepo = $partnerRepo ?? new PartnerRepository();
        $this->categoryRepo = $categoryRepo ?? new CategoryRepository();
        $this->dashboardService = $dashboardService ?? new PartnerDashboardService($this->partnerRepo);
        $this->productService = $productService ?? new PartnerProductService($this->partnerRepo);
    }

    /**
     * Halaman Utama Dashboard Mitra Lapak (100% Mockup CamHub — Dashboard Mitra Lapak.png)
     */
    public function index(): string
    {
        $userId = auth()['id'] ?? 0;
        $data = $this->dashboardService->getDashboardData($userId);

        if (!$data['has_store']) {
            return view('partner/no_store', ['user' => auth()]);
        }

        return view('partner/dashboard', [
            'partner' => $data['partner'],
            'metrics' => $data['metrics'],
            'products' => $data['products'],
            'activeMenu' => 'beranda',
        ]);
    }

    /**
     * Halaman Kelola Etalase Produk Mitra
     */
    public function products(): string
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        if (!$partner) {
            redirect('/login');
            return '';
        }

        $storeId = (int)$partner['store_id'];
        $categories = $this->categoryRepo->getAllWithCounts();
        $products = $this->partnerRepo->getPartnerProducts($storeId);

        return view('partner/products', [
            'partner' => $partner,
            'categories' => $categories,
            'products' => $products,
            'activeMenu' => 'produk',
        ]);
    }

    /**
     * Formulir Tambah Kamera / Lensa Baru
     */
    public function createProduct(): string
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        $categories = $this->categoryRepo->getAllWithCounts();

        return view('partner/product_form', [
            'partner' => $partner,
            'categories' => $categories,
            'product' => null,
            'isEdit' => false,
            'activeMenu' => 'produk',
        ]);
    }

    /**
     * Simpan Produk Baru
     */
    public function storeProduct(): void
    {
        verify_csrf();

        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        if (!$partner) {
            redirect('/login');
            return;
        }

        $storeId = (int)$partner['store_id'];
        $file = $_FILES['product_image'] ?? null;

        $result = $this->productService->createProduct($storeId, $_POST, $file);

        if (!$result['success']) {
            flash('error', implode('<br>', $result['errors']));
            redirect('/dashboard/mitra/produk/tambah');
            return;
        }

        flash('success', 'Produk kamera baru berhasil ditambahkan ke etalase lapak Anda!');
        redirect('/dashboard/mitra/produk');
    }

    /**
     * Formulir Edit Produk
     */
    public function editProduct(int $productId): string
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        $storeId = (int)($partner['store_id'] ?? 0);

        $product = $this->partnerRepo->findProductByIdAndStore($productId, $storeId);
        if (!$product) {
            flash('error', 'Produk tidak ditemukan atau bukan milik toko Anda.');
            redirect('/dashboard/mitra/produk');
            return '';
        }

        $categories = $this->categoryRepo->getAllWithCounts();

        return view('partner/product_form', [
            'partner' => $partner,
            'categories' => $categories,
            'product' => $product,
            'isEdit' => true,
            'activeMenu' => 'produk',
        ]);
    }

    /**
     * Update Produk
     */
    public function updateProduct(int $productId): void
    {
        verify_csrf();

        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        $storeId = (int)($partner['store_id'] ?? 0);

        $file = $_FILES['product_image'] ?? null;
        $result = $this->productService->updateProduct($productId, $storeId, $_POST, $file);

        if (!$result['success']) {
            flash('error', implode('<br>', $result['errors']));
            redirect('/dashboard/mitra/produk/' . $productId . '/edit');
            return;
        }

        flash('success', 'Data produk berhasil diperbarui.');
        redirect('/dashboard/mitra/produk');
    }

    /**
     * Hapus Produk
     */
    public function deleteProduct(int $productId): void
    {
        verify_csrf();

        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        $storeId = (int)($partner['store_id'] ?? 0);

        $success = $this->productService->deleteProduct($productId, $storeId);
        if ($success) {
            flash('success', 'Produk berhasil dihapus dari etalase toko.');
        } else {
            flash('error', 'Gagal menghapus produk atau akses ditolak.');
        }

        redirect('/dashboard/mitra/produk');
    }

    /**
     * Kelola Unit Fisik Kamera (Serial Number)
     */
    public function manageUnits(int $productId): string
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        $storeId = (int)($partner['store_id'] ?? 0);

        $product = $this->partnerRepo->findProductByIdAndStore($productId, $storeId);
        if (!$product) {
            flash('error', 'Produk tidak ditemukan.');
            redirect('/dashboard/mitra/produk');
            return '';
        }

        $units = $this->partnerRepo->getUnitsByProductId($productId, $storeId);

        return view('partner/units', [
            'partner' => $partner,
            'product' => $product,
            'units' => $units,
            'activeMenu' => 'produk',
        ]);
    }

    /**
     * Simpan Unit Fisik Baru
     */
    public function storeUnit(int $productId): void
    {
        verify_csrf();

        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        $storeId = (int)($partner['store_id'] ?? 0);

        $result = $this->productService->addUnit($productId, $storeId, $_POST);
        if (!$result['success']) {
            flash('error', implode('<br>', $result['errors']));
        } else {
            flash('success', 'Unit fisik baru dengan nomor seri berhasil didaftarkan.');
        }

        redirect('/dashboard/mitra/produk/' . $productId . '/unit');
    }

    /**
     * Jadwal Sewa Unit Kamera
     */
    public function schedule(): string
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        $storeId = (int)($partner['store_id'] ?? 0);

        $schedules = $this->partnerRepo->getSchedule($storeId);

        return view('partner/schedule', [
            'partner' => $partner,
            'schedules' => $schedules,
            'activeMenu' => 'jadwal',
        ]);
    }

    /**
     * Daftar Transaksi Masuk
     */
    public function transactions(): string
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        $storeId = (int)($partner['store_id'] ?? 0);

        $transactions = $this->partnerRepo->getTransactions($storeId);

        return view('partner/transactions', [
            'partner' => $partner,
            'transactions' => $transactions,
            'activeMenu' => 'transaksi',
        ]);
    }

    /**
     * Saldo Dompet & Pencairan
     */
    public function wallet(): string
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        $storeId = (int)($partner['store_id'] ?? 0);

        $metrics = $this->partnerRepo->getDashboardMetrics($storeId);

        return view('partner/wallet', [
            'partner' => $partner,
            'metrics' => $metrics,
            'activeMenu' => 'saldo',
        ]);
    }

    /**
     * Pengaturan Toko Lapak
     */
    public function settings(): string
    {
        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);

        return view('partner/settings', [
            'partner' => $partner,
            'activeMenu' => 'pengaturan',
        ]);
    }

    /**
     * Update Pengaturan Toko Lapak
     */
    public function updateSettings(): void
    {
        verify_csrf();

        $userId = auth()['id'] ?? 0;
        $partner = $this->partnerRepo->findByUserId($userId);
        $storeId = (int)($partner['store_id'] ?? 0);

        $result = $this->productService->updateStoreProfile($storeId, $_POST);
        if (!$result['success']) {
            flash('error', implode('<br>', $result['errors']));
        } else {
            flash('success', 'Profil toko lapak Anda berhasil diperbarui!');
        }

        redirect('/dashboard/mitra/pengaturan');
    }
}
