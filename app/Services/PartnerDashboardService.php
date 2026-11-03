<?php

namespace App\Services;

use App\Repositories\PartnerRepository;

/**
 * Service PartnerDashboardService
 * 
 * Mengorkestrasi data agregat metrik lapak, data toko,
 * dan daftar inventaris untuk halaman utama Dashboard Mitra.
 */
class PartnerDashboardService
{
    private PartnerRepository $partnerRepo;

    public function __construct(?PartnerRepository $partnerRepo = null)
    {
        $this->partnerRepo = $partnerRepo ?? new PartnerRepository();
    }

    /**
     * Mempersiapkan seluruh payload data untuk Dashboard Mitra Lapak
     */
    public function getDashboardData(int $userId): array
    {
        $partnerInfo = $this->partnerRepo->findByUserId($userId);
        if (!$partnerInfo || empty($partnerInfo['store_id'])) {
            return [
                'has_store' => false,
                'partner' => null,
                'metrics' => [
                    'total_products' => 0,
                    'active_orders' => 0,
                    'available_balance' => 0,
                    'available_balance_formatted' => 'Rp 0',
                ],
                'products' => [],
            ];
        }

        $storeId = (int)$partnerInfo['store_id'];
        $metrics = $this->partnerRepo->getDashboardMetrics($storeId);
        $products = $this->partnerRepo->getPartnerProducts($storeId);

        return [
            'has_store' => true,
            'partner' => $partnerInfo,
            'metrics' => $metrics,
            'products' => $products,
        ];
    }
}
