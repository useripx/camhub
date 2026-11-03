<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Repositories\KycRepository;
use PDO;
use Throwable;

class ProfileService
{
    private KycRepository $kycRepo;
    private PDO $pdo;

    public function __construct(?KycRepository $kycRepo = null, ?PDO $pdo = null)
    {
        $this->kycRepo = $kycRepo ?? new KycRepository();
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Retrieve full profile and KYC status for customer
     */
    public function getProfile(int $userId): ?array
    {
        $profile = $this->kycRepo->findProfileByUserId($userId);
        if (!$profile) {
            return null;
        }

        $latestKyc = $this->kycRepo->findLatestByUserId($userId);
        $profile['kyc'] = $latestKyc;
        $profile['kyc_status'] = $latestKyc['status'] ?? 'unverified';

        return $profile;
    }

    /**
     * Update customer profile details (address, city, phone, name)
     */
    public function updateProfile(int $userId, array $data): array
    {
        try {
            $updated = $this->kycRepo->updateProfile($userId, [
                'full_name' => trim($data['full_name'] ?? ''),
                'phone' => trim($data['phone'] ?? ''),
                'city' => trim($data['city'] ?? ''),
                'address' => trim($data['address'] ?? ''),
            ]);

            if ($updated) {
                // Also sync session full_name and phone if active
                session_start_safe();
                if (isset($_SESSION['user']) && $_SESSION['user']['id'] === $userId) {
                    if (!empty($data['full_name'])) $_SESSION['user']['full_name'] = trim($data['full_name']);
                    if (!empty($data['phone'])) $_SESSION['user']['phone'] = trim($data['phone']);
                }

                return [
                    'success' => true,
                    'message' => 'Profil dan data domisili Anda berhasil diperbarui!',
                ];
            }

            return [
                'success' => false,
                'message' => 'Tidak ada perubahan data yang disimpan.',
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage(),
            ];
        }
    }
}

