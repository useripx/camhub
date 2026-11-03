<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class KycRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Create a new KYC submission record
     */
    public function createSubmission(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO kyc_submissions (
                user_id, full_name, nik_ktp, birth_date, gender, 
                ktp_image_path, selfie_image_path, status, created_at
            ) VALUES (
                :user_id, :full_name, :nik_ktp, :birth_date, :gender, 
                :ktp_image_path, :selfie_image_path, 'pending', NOW()
            )
        ");

        $stmt->execute([
            'user_id' => $data['user_id'],
            'full_name' => $data['full_name'],
            'nik_ktp' => $data['nik_ktp'],
            'birth_date' => $data['birth_date'],
            'gender' => $data['gender'],
            'ktp_image_path' => $data['ktp_image_path'],
            'selfie_image_path' => $data['selfie_image_path'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Get the latest KYC submission of a user
     */
    public function findLatestByUserId(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM kyc_submissions 
            WHERE user_id = :user_id 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Get all KYC submissions history of a user
     */
    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM kyc_submissions 
            WHERE user_id = :user_id 
            ORDER BY id DESC
        ");
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get user profile data joined with users table
     */
    public function findProfileByUserId(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT u.id AS user_id, u.username, u.full_name, u.email, u.phone, u.status AS user_status,
                   p.address, p.city, p.avatar_url, p.updated_at AS profile_updated_at
            FROM users u
            LEFT JOIN user_profiles p ON u.id = p.user_id
            WHERE u.id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        return $profile ?: null;
    }

    /**
     * Update or insert user profile biodata
     */
    public function updateProfile(int $userId, array $data): bool
    {
        // Update user full_name and phone if provided
        if (isset($data['full_name']) || isset($data['phone'])) {
            $stmtUser = $this->pdo->prepare("
                UPDATE users 
                SET full_name = COALESCE(:full_name, full_name),
                    phone = COALESCE(:phone, phone)
                WHERE id = :id
            ");
            $stmtUser->execute([
                'full_name' => $data['full_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'id' => $userId,
            ]);
        }

        // Upsert user_profiles
        $stmtProfile = $this->pdo->prepare("
            INSERT INTO user_profiles (user_id, address, city, avatar_url)
            VALUES (:user_id, :address, :city, :avatar_url)
            ON DUPLICATE KEY UPDATE 
                address = VALUES(address),
                city = VALUES(city),
                avatar_url = COALESCE(VALUES(avatar_url), avatar_url),
                updated_at = NOW()
        ");

        return $stmtProfile->execute([
            'user_id' => $userId,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'avatar_url' => $data['avatar_url'] ?? null,
        ]);
    }

    /**
     * Update verification status of a KYC submission
     */
    public function updateKycStatus(int $id, string $status, ?string $reason = null, ?int $verifiedBy = null): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE kyc_submissions 
            SET status = :status,
                rejection_reason = :reason,
                verified_by = :verified_by,
                verified_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            'status' => $status,
            'reason' => $reason,
            'verified_by' => $verifiedBy,
            'id' => $id,
        ]);
    }
}

