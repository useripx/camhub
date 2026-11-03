<?php

namespace App\Services;

use App\Repositories\PartnerRepository;
use App\Validators\PartnerProductValidator;

/**
 * Service PartnerProductService
 * 
 * Menangani logika bisnis inventaris produk mitra, nomor seri unit fisik,
 * proteksi kepemilikan anti-IDOR, dan sanitasi berkas foto.
 */
class PartnerProductService
{
    private PartnerRepository $partnerRepo;
    private PartnerProductValidator $validator;

    public function __construct(
        ?PartnerRepository $partnerRepo = null,
        ?PartnerProductValidator $validator = null
    ) {
        $this->partnerRepo = $partnerRepo ?? new PartnerRepository();
        $this->validator = $validator ?? new PartnerProductValidator();
    }

    /**
     * Membuat produk baru milik mitra
     */
    public function createProduct(int $storeId, array $data, ?array $file = null): array
    {
        if (!$this->validator->validateProduct($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ];
        }

        // Generate slug unik
        $baseSlug = $this->slugify($data['name']);
        $slug = $baseSlug . '-' . substr(bin2hex(random_bytes(3)), 0, 5);

        // Upload foto jika ada
        $imagePath = 'assets/img/canon_r50.png'; // default fallback
        if ($file && !empty($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleImageUpload($file);
            if ($uploaded) {
                $imagePath = $uploaded;
            }
        }

        $payload = [
            'store_id' => $storeId,
            'category_id' => (int)$data['category_id'],
            'name' => trim($data['name']),
            'slug' => $slug,
            'description' => trim($data['description'] ?? ''),
            'condition_note' => trim($data['condition_note'] ?? 'Kondisi sangat baik · Siap disewa'),
            'is_rental' => !empty($data['is_rental']) ? 1 : 0,
            'is_sale' => !empty($data['is_sale']) ? 1 : 0,
            'sale_price' => (float)($data['sale_price'] ?? 0),
            'daily_price' => (float)($data['daily_price'] ?? 0),
            'stock_quantity' => (int)($data['stock_quantity'] ?? 1),
            'image_path' => $imagePath,
        ];

        $productId = $this->partnerRepo->createProduct($payload);

        // Buat unit fisik perdana jika disediakan serial_number
        if (!empty($data['serial_number'])) {
            $this->partnerRepo->addUnit($productId, $storeId, [
                'serial_number' => trim($data['serial_number']),
                'condition_status' => $payload['condition_note'],
                'availability_status' => 'available',
            ]);
        }

        return [
            'success' => true,
            'product_id' => $productId,
            'slug' => $slug,
        ];
    }

    /**
     * Memperbarui produk dengan verifikasi anti-IDOR
     */
    public function updateProduct(int $productId, int $storeId, array $data, ?array $file = null): array
    {
        $existing = $this->partnerRepo->findProductByIdAndStore($productId, $storeId);
        if (!$existing) {
            return [
                'success' => false,
                'errors' => ['auth' => 'Anda tidak memiliki hak akses untuk memodifikasi produk ini.'],
            ];
        }

        if (!$this->validator->validateProduct($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ];
        }

        $imagePath = null;
        if ($file && !empty($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleImageUpload($file);
            if ($uploaded) {
                $imagePath = $uploaded;
            }
        }

        $payload = [
            'category_id' => (int)$data['category_id'],
            'name' => trim($data['name']),
            'description' => trim($data['description'] ?? ''),
            'condition_note' => trim($data['condition_note'] ?? 'Kondisi sangat baik · Siap disewa'),
            'is_rental' => !empty($data['is_rental']) ? 1 : 0,
            'is_sale' => !empty($data['is_sale']) ? 1 : 0,
            'sale_price' => (float)($data['sale_price'] ?? 0),
            'daily_price' => (float)($data['daily_price'] ?? 0),
            'stock_quantity' => (int)($data['stock_quantity'] ?? 1),
            'image_path' => $imagePath,
        ];

        $this->partnerRepo->updateProduct($productId, $storeId, $payload);

        return ['success' => true];
    }

    /**
     * Menghapus produk dengan proteksi toko
     */
    public function deleteProduct(int $productId, int $storeId): bool
    {
        return $this->partnerRepo->deleteProduct($productId, $storeId);
    }

    /**
     * Menambahkan nomor seri unit fisik
     */
    public function addUnit(int $productId, int $storeId, array $data): array
    {
        if (!$this->validator->validateUnit($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ];
        }

        $unitId = $this->partnerRepo->addUnit($productId, $storeId, $data);
        if ($unitId === 0) {
            return [
                'success' => false,
                'errors' => ['auth' => 'Produk tidak ditemukan atau bukan milik toko Anda.'],
            ];
        }

        return ['success' => true, 'unit_id' => $unitId];
    }

    /**
     * Memperbarui profil lapak toko mitra
     */
    public function updateStoreProfile(int $storeId, array $data): array
    {
        if (!$this->validator->validateStore($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ];
        }

        $this->partnerRepo->updateStoreProfile($storeId, $data);
        return ['success' => true];
    }

    /**
     * Mengubah nama menjadi URL slug aman
     */
    private function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'item' : $text;
    }

    /**
     * Menangani upload foto produk ke direktori aman
     */
    private function handleImageUpload(array $file): ?string
    {
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts, true)) {
            return null;
        }

        $targetDir = __DIR__ . '/../../public/uploads/products';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = 'prod_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $destPath = $targetDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return 'uploads/products/' . $filename;
        }

        return null;
    }
}
