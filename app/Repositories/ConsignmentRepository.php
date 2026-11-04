<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;
use Exception;

class ConsignmentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Ambil daftar toko mitra aktif untuk opsi dropdown formulir pengajuan
     */
    public function getActiveStores(): array
    {
        $stmt = $this->db->query("
            SELECT s.id, s.store_name, s.city, s.address
            FROM partner_stores s
            ORDER BY s.store_name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Simpan pengajuan titip jual baru dari pelanggan
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO consignments 
            (user_id, store_id, item_name, item_description, requested_price, commission_percent, status, created_at)
            VALUES (:user_id, :store_id, :item_name, :item_description, :requested_price, :commission_percent, 'submitted', NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $data['user_id'],
            'store_id' => $data['store_id'],
            'item_name' => $data['item_name'],
            'item_description' => $data['item_description'],
            'requested_price' => $data['requested_price'],
            'commission_percent' => $data['commission_percent'] ?? 10.00,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Ambil data titip jual berdasarkan ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT c.*, 
                       ps.store_name, ps.city as store_city, ps.address as store_address,
                       u.full_name as user_name, u.phone as user_phone, u.email as user_email
                FROM consignments c
                JOIN partner_stores ps ON c.store_id = ps.id
                JOIN users u ON c.user_id = u.id
                WHERE c.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res ?: null;
    }

    /**
     * Ambil seluruh riwayat titip jual milik akun pengguna tertentu
     */
    public function findByUserId(int $userId): array
    {
        $sql = "SELECT c.*, ps.store_name, ps.city as store_city
                FROM consignments c
                JOIN partner_stores ps ON c.store_id = ps.id
                WHERE c.user_id = :user_id
                ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil seluruh pengajuan titip jual yang masuk ke toko mitra tertentu
     */
    public function findByStoreId(int $storeId, ?string $status = null): array
    {
        $sql = "SELECT c.*, u.full_name as user_name, u.phone as user_phone
                FROM consignments c
                JOIN users u ON c.user_id = u.id
                WHERE c.store_id = :store_id";

        $params = ['store_id' => $storeId];

        if ($status !== null) {
            $sql .= " AND c.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Toko mitra memperbarui status inspeksi dan persetujuan harga final
     */
    public function updateInspectionAndApprove(int $id, float $agreedPrice, float $commissionPercent = 10.00, ?string $notes = null): bool
    {
        $sql = "UPDATE consignments 
                SET agreed_price = :agreed_price,
                    commission_percent = :commission_percent,
                    status = 'approved',
                    approved_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'agreed_price' => $agreedPrice,
            'commission_percent' => $commissionPercent,
        ]);
    }

    /**
     * Publikasikan barang titip jual yang telah disetujui menjadi produk aktif di katalog
     */
    public function publishToCatalog(int $consignmentId, int $categoryId = 1): int
    {
        $consign = $this->findById($consignmentId);
        if (!$consign) {
            throw new Exception("Data titip jual tidak ditemukan.");
        }

        $this->db->beginTransaction();

        try {
            // Buat slug produk unik
            $rawSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $consign['item_name'])));
            $slug = $rawSlug . '-tj-' . $consignmentId;

            $price = $consign['agreed_price'] ?? $consign['requested_price'];

            // 1. Masukkan ke tabel products
            $sqlProd = "INSERT INTO products 
                (store_id, category_id, name, slug, description, condition_note, is_rental, is_sale, is_consignment, sale_price, stock_quantity, created_at, updated_at)
                VALUES (:store_id, :category_id, :name, :slug, :description, :condition_note, 0, 1, 1, :sale_price, 1, NOW(), NOW())";

            $stmtProd = $this->db->prepare($sqlProd);
            $stmtProd->execute([
                'store_id' => $consign['store_id'],
                'category_id' => $categoryId,
                'name' => $consign['item_name'],
                'slug' => $slug,
                'description' => $consign['item_description'],
                'condition_note' => 'Titip Jual Terverifikasi Toko Mitra',
                'sale_price' => $price,
            ]);

            $productId = (int) $this->db->lastInsertId();

            // 2. Tambahkan gambar default produk
            $sqlImg = "INSERT INTO product_images (product_id, image_path, is_primary) VALUES (:product_id, :image_path, 1)";
            $stmtImg = $this->db->prepare($sqlImg);
            $stmtImg->execute([
                'product_id' => $productId,
                'image_path' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=600&q=80',
            ]);

            // 3. Perbarui status consignment menjadi listed
            $sqlUp = "UPDATE consignments SET status = 'listed' WHERE id = :id";
            $stmtUp = $this->db->prepare($sqlUp);
            $stmtUp->execute(['id' => $consignmentId]);

            $this->db->commit();
            return $productId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Tandai titip jual telah terjual dan sukses
     */
    public function markAsSold(int $consignmentId): bool
    {
        $stmt = $this->db->prepare("UPDATE consignments SET status = 'sold' WHERE id = :id");
        return $stmt->execute(['id' => $consignmentId]);
    }

    /**
     * Tolak pengajuan titip jual
     */
    public function markAsRejected(int $consignmentId): bool
    {
        $stmt = $this->db->prepare("UPDATE consignments SET status = 'rejected' WHERE id = :id");
        return $stmt->execute(['id' => $consignmentId]);
    }
}
