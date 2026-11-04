<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ConsignmentRepository;
use App\Validators\ConsignmentValidator;
use Exception;

class ConsignmentService
{
    private ConsignmentRepository $repo;
    private ConsignmentValidator $validator;

    public function __construct()
    {
        $this->repo = new ConsignmentRepository();
        $this->validator = new ConsignmentValidator();
    }

    public function getActiveStores(): array
    {
        return $this->repo->getActiveStores();
    }

    /**
     * Submit pengajuan titip jual baru
     */
    public function submitConsignment(array $data, int $userId): array
    {
        if (!$this->validator->validate($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        $payload = [
            'user_id' => $userId,
            'store_id' => (int) $data['store_id'],
            'item_name' => trim($data['item_name']),
            'item_description' => trim($data['item_description']),
            'requested_price' => (float) $data['requested_price'],
            'commission_percent' => 10.00,
        ];

        try {
            $id = $this->repo->create($payload);
            return [
                'success' => true,
                'consignment_id' => $id,
                'message' => 'Pengajuan titip jual berhasil dikirim ke Toko Mitra. Silakan pantau status inspeksi.'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'errors' => ['general' => 'Gagal menyimpan pengajuan: ' . $e->getMessage()]
            ];
        }
    }

    public function getUserConsignments(int $userId): array
    {
        return $this->repo->findByUserId($userId);
    }

    public function getStoreConsignments(int $storeId, ?string $status = null): array
    {
        return $this->repo->findByStoreId($storeId, $status);
    }

    public function getConsignmentDetail(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    /**
     * Persetujuan & penetapan harga kesepakatan oleh toko mitra
     */
    public function approveConsignment(int $id, float $agreedPrice, float $commissionPercent = 10.00, ?string $notes = null): bool
    {
        return $this->repo->updateInspectionAndApprove($id, $agreedPrice, $commissionPercent, $notes);
    }

    /**
     * Publikasi produk titip jual ke etalase publik
     */
    public function publishConsignment(int $consignmentId, int $categoryId = 1): int
    {
        return $this->repo->publishToCatalog($consignmentId, $categoryId);
    }
}
