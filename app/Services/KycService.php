<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Repositories\KycRepository;
use PDO;
use Throwable;

class KycService
{
    private KycRepository $kycRepo;
    private PDO $pdo;
    private string $uploadDir;

    public function __construct(?KycRepository $kycRepo = null, ?PDO $pdo = null, ?string $uploadDir = null)
    {
        $this->kycRepo = $kycRepo ?? new KycRepository();
        $this->pdo = $pdo ?? Database::getConnection();
        $this->uploadDir = $uploadDir ?? (dirname(__DIR__, 2) . '/public/uploads/kyc');

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Submit new KYC verification application
     */
    public function submitKyc(int $userId, array $input, array $files): array
    {
        // 1. Prevent duplicate pending submissions
        $latest = $this->kycRepo->findLatestByUserId($userId);
        if ($latest && $latest['status'] === 'pending') {
            return [
                'success' => false,
                'message' => 'Pengajuan KYC Anda sebelumnya masih dalam antrean peninjauan oleh tim admin CamHub.',
            ];
        }

        if ($latest && $latest['status'] === 'approved') {
            return [
                'success' => false,
                'message' => 'Akun Anda telah terverifikasi resmi (KYC Approved). Tidak perlu mengajukan kembali.',
            ];
        }

        try {
            // 2. Save KTP & Selfie Files
            $ktpPath = $this->saveUploadedFile($files['ktp_file'], 'ktp_' . $userId);
            $selfiePath = $this->saveUploadedFile($files['selfie_file'], 'selfie_' . $userId);

            // 3. Save into database
            $submissionId = $this->kycRepo->createSubmission([
                'user_id' => $userId,
                'full_name' => trim($input['full_name']),
                'nik_ktp' => trim($input['nik_ktp']),
                'birth_date' => trim($input['birth_date']),
                'gender' => trim($input['gender']),
                'ktp_image_path' => $ktpPath,
                'selfie_image_path' => $selfiePath,
            ]);

            // 4. Log Audit
            $this->logAudit($userId, 'KYC_SUBMIT', 'kyc_submissions', $submissionId, [
                'nik_masked' => substr(trim($input['nik_ktp']), 0, 4) . '********' . substr(trim($input['nik_ktp']), -4),
                'full_name' => $input['full_name'],
            ]);

            return [
                'success' => true,
                'submission_id' => $submissionId,
                'message' => 'Pengajuan verifikasi identitas (KYC) Anda berhasil dikirim dan sedang menunggu tinjauan tim admin.',
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal memproses pengajuan KYC: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Securely store uploaded file with unique random filename
     */
    public function saveUploadedFile(array $file, string $prefix): string
    {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $randomHash = bin2hex(random_bytes(16));
        $fileName = "{$prefix}_{$randomHash}.{$extension}";
        $destination = $this->uploadDir . '/' . $fileName;

        if (is_uploaded_file($file['tmp_name'])) {
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new \RuntimeException("Gagal memindahkan berkas unggahan ke folder target.");
            }
        } else {
            // Support programmatic/testing simulated uploads
            if (!copy($file['tmp_name'], $destination)) {
                throw new \RuntimeException("Gagal menyalin berkas simulasi unggahan.");
            }
        }

        return 'uploads/kyc/' . $fileName;
    }

    /**
     * Get current KYC status summary of a user
     */
    public function getKycStatus(int $userId): ?array
    {
        return $this->kycRepo->findLatestByUserId($userId);
    }

    /**
     * Get all submissions history of a user
     */
    public function getSubmissionHistory(int $userId): array
    {
        return $this->kycRepo->findAllByUserId($userId);
    }

    /**
     * Record security audit log
     */
    private function logAudit(int $userId, string $action, string $targetTable, int $targetId, array $changes): void
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, ip_address, created_at)
                VALUES (:user_id, :action, :target_table, :target_id, :changes_json, :ip_address, NOW())
            ");
            $stmt->execute([
                'user_id' => $userId,
                'action' => $action,
                'target_table' => $targetTable,
                'target_id' => $targetId,
                'changes_json' => json_encode($changes, JSON_UNESCAPED_UNICODE),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            ]);
        } catch (Throwable $e) {
            // Silently ignore log failure
        }
    }
}
