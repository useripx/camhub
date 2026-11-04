<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\EscrowRepository;
use App\Core\Database;
use PDO;
use Exception;

class AdminService
{
    private AdminRepository $adminRepo;
    private EscrowRepository $escrowRepo;
    private PDO $db;

    public function __construct()
    {
        $this->adminRepo = new AdminRepository();
        $this->escrowRepo = new EscrowRepository();
        $this->db = Database::getConnection();
    }

    /**
     * Persetujuan Berkas KYC Pengguna
     */
    public function approveKyc(int $submissionId, int $adminId): array
    {
        $submission = $this->adminRepo->findKycById($submissionId);
        if (!$submission) {
            return ['success' => false, 'message' => 'Pengajuan KYC tidak ditemukan.'];
        }

        $this->db->beginTransaction();
        try {
            $this->adminRepo->approveKyc($submissionId, $adminId);

            // Audit log
            $stmtAudit = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, 'KYC_APPROVE', 'kyc_submissions', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $adminId,
                'target_id' => $submissionId,
                'changes_json' => json_encode([
                    'status' => 'approved',
                    'user_id' => $submission['user_id'],
                    'full_name' => $submission['full_name'],
                    'nik_ktp' => $submission['nik_ktp']
                ])
            ]);

            $this->db->commit();
            return [
                'success' => true,
                'message' => "Pengajuan KYC #{$submissionId} ({$submission['full_name']}) resmi disetujui."
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Gagal menyetujui KYC: ' . $e->getMessage()];
        }
    }

    /**
     * Penolakan Berkas KYC Pengguna
     */
    public function rejectKyc(int $submissionId, int $adminId, string $reason): array
    {
        $submission = $this->adminRepo->findKycById($submissionId);
        if (!$submission) {
            return ['success' => false, 'message' => 'Pengajuan KYC tidak ditemukan.'];
        }

        $reason = trim($reason);
        if ($reason === '') {
            $reason = 'Foto dokumen KTP / selfie tidak memenuhi standar verifikasi atau buram.';
        }

        $this->db->beginTransaction();
        try {
            $this->adminRepo->rejectKyc($submissionId, $adminId, $reason);

            // Audit log
            $stmtAudit = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, 'KYC_REJECT', 'kyc_submissions', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $adminId,
                'target_id' => $submissionId,
                'changes_json' => json_encode([
                    'status' => 'rejected',
                    'user_id' => $submission['user_id'],
                    'rejection_reason' => $reason
                ])
            ]);

            $this->db->commit();
            return [
                'success' => true,
                'message' => "Pengajuan KYC #{$submissionId} ({$submission['full_name']}) telah ditolak."
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Gagal menolak KYC: ' . $e->getMessage()];
        }
    }

    /**
     * Resolusi Sengketa Transaksi Escrow oleh Super Admin
     */
    public function resolveDispute(int $escrowId, string $resolution, int $adminId, string $notes = ''): array
    {
        $escrow = $this->escrowRepo->findById($escrowId);
        if (!$escrow) {
            return ['success' => false, 'message' => 'Transaksi rekening bersama tidak ditemukan.'];
        }

        if ($escrow['status'] !== 'disputed') {
            return ['success' => false, 'message' => "Transaksi tidak dalam status disputed (status saat ini: {$escrow['status']})."];
        }

        $this->db->beginTransaction();
        try {
            if ($resolution === 'release') {
                // Putusan: Cairkan dana paksa ke toko mitra
                $stmtUp = $this->db->prepare("
                    UPDATE escrow_transactions 
                    SET status = 'released', released_at = NOW() 
                    WHERE id = :id
                ");
                $stmtUp->execute(['id' => $escrowId]);

                if (!empty($escrow['booking_id'])) {
                    $this->db->prepare("UPDATE rental_bookings SET rental_status = 'completed' WHERE id = :id")
                             ->execute(['id' => $escrow['booking_id']]);
                } elseif (!empty($escrow['sales_order_id'])) {
                    $this->db->prepare("UPDATE sales_orders SET order_status = 'completed' WHERE id = :id")
                             ->execute(['id' => $escrow['sales_order_id']]);
                }

                $actionType = 'DISPUTE_FORCE_RELEASE';
                $msg = "Sengketa Escrow #{$escrowId} diputus untuk dicairkan ke toko mitra.";
            } else {
                // Putusan: Kembalikan dana penuh ke pelanggan
                $stmtUp = $this->db->prepare("
                    UPDATE escrow_transactions 
                    SET status = 'refunded' 
                    WHERE id = :id
                ");
                $stmtUp->execute(['id' => $escrowId]);

                if (!empty($escrow['booking_id'])) {
                    $this->db->prepare("UPDATE rental_bookings SET rental_status = 'cancelled' WHERE id = :id")
                             ->execute(['id' => $escrow['booking_id']]);
                } elseif (!empty($escrow['sales_order_id'])) {
                    $this->db->prepare("UPDATE sales_orders SET order_status = 'cancelled' WHERE id = :id")
                             ->execute(['id' => $escrow['sales_order_id']]);
                }

                $actionType = 'DISPUTE_FORCE_REFUND';
                $msg = "Sengketa Escrow #{$escrowId} diputus untuk dikembalikan (refund) penuh ke pelanggan.";
            }

            // Audit log
            $stmtAudit = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, :action, 'escrow_transactions', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $adminId,
                'action' => $actionType,
                'target_id' => $escrowId,
                'changes_json' => json_encode([
                    'resolution' => $resolution,
                    'notes' => $notes,
                    'amount' => (float)$escrow['amount_held']
                ])
            ]);

            $this->db->commit();
            return ['success' => true, 'message' => $msg];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Gagal memproses resolusi sengketa: ' . $e->getMessage()];
        }
    }

    /**
     * Memproses Pengajuan Pencairan Dana Saldo Mitra
     */
    public function processWithdrawal(int $withdrawalId, string $action, int $adminId): array
    {
        $wd = $this->adminRepo->findWithdrawalById($withdrawalId);
        if (!$wd) {
            return ['success' => false, 'message' => 'Pengajuan penarikan dana tidak ditemukan.'];
        }

        $newStatus = ($action === 'processed') ? 'processed' : 'rejected';
        $this->db->beginTransaction();
        try {
            $this->adminRepo->updateWithdrawalStatus($withdrawalId, $newStatus);

            // Audit log
            $stmtAudit = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, 'WITHDRAWAL_STATUS_CHANGE', 'partner_withdrawals', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $adminId,
                'target_id' => $withdrawalId,
                'changes_json' => json_encode([
                    'old_status' => $wd['status'],
                    'new_status' => $newStatus,
                    'amount' => (float)$wd['amount']
                ])
            ]);

            $this->db->commit();
            $statusLabel = ($newStatus === 'processed') ? 'disetujui & diproses transfer' : 'ditolak';
            return [
                'success' => true,
                'message' => "Penarikan dana #WD-{$withdrawalId} sebesar Rp " . number_format((float)$wd['amount'], 0, ',', '.') . " berhasil {$statusLabel}."
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Gagal memproses penarikan: ' . $e->getMessage()];
        }
    }

    /**
     * Penerbitan Denda Operasional Baru
     */
    public function storeFine(array $data, int $adminId): array
    {
        $bookingId = (int) ($data['booking_id'] ?? 0);
        $rawType = trim((string)($data['fine_type'] ?? 'late_return'));
        $fineTypeMap = [
            'late' => 'late_return',
            'late_return' => 'late_return',
            'damage' => 'damage',
            'lost_item' => 'missing_accessory',
            'missing_accessory' => 'missing_accessory'
        ];
        $fineType = $fineTypeMap[$rawType] ?? 'late_return';
        $amount = (float) ($data['amount'] ?? 0);
        $reason = trim($data['reason'] ?? 'Keterlambatan pengembalian unit');

        if ($bookingId <= 0 || $amount <= 0) {
            return ['success' => false, 'message' => 'Nomor pesanan sewa dan nominal denda wajib valid.'];
        }

        try {
            $id = $this->adminRepo->createFine($bookingId, $fineType, $amount, $reason);

            // Audit log
            $stmtAudit = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, 'FINE_CREATE', 'fines', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $adminId,
                'target_id' => $id,
                'changes_json' => json_encode([
                    'booking_id' => $bookingId,
                    'fine_type' => $fineType,
                    'amount' => $amount,
                    'reason' => $reason
                ])
            ]);

            return [
                'success' => true,
                'fine_id' => $id,
                'message' => "Denda baru ID #{$id} sebesar Rp " . number_format($amount, 0, ',', '.') . " berhasil diterbitkan."
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Gagal menerbitkan denda: ' . $e->getMessage()];
        }
    }

    /**
     * Pelunasan Denda Operasional
     */
    public function settleFine(int $fineId, int $adminId): array
    {
        try {
            $this->adminRepo->updateFineStatus($fineId, 'paid');

            // Audit log
            $stmtAudit = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, 'FINE_PAID', 'fines', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $adminId,
                'target_id' => $fineId,
                'changes_json' => json_encode(['status' => 'paid'])
            ]);

            return ['success' => true, 'message' => "Denda #{$fineId} berhasil ditandai lunas."];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Gagal memperbarui status denda: ' . $e->getMessage()];
        }
    }

    /**
     * Ubah Status Pengguna (Active / Suspended)
     */
    public function toggleUserStatus(int $userId, int $adminId): array
    {
        if ($userId === 1 || $userId === $adminId) {
            return ['success' => false, 'message' => 'Akun Super Administrator utama tidak dapat disuspensi.'];
        }

        $stmt = $this->db->prepare("SELECT id, username, status FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'Pengguna tidak ditemukan.'];
        }

        $newStatus = ($user['status'] === 'active') ? 'suspended' : 'active';
        $this->adminRepo->setUserStatus($userId, $newStatus);

        // Audit log
        $stmtAudit = $this->db->prepare("
            INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
            VALUES (:user_id, 'USER_STATUS_CHANGE', 'users', :target_id, :changes_json, NOW())
        ");
        $stmtAudit->execute([
            'user_id' => $adminId,
            'target_id' => $userId,
            'changes_json' => json_encode([
                'username' => $user['username'],
                'old_status' => $user['status'],
                'new_status' => $newStatus
            ])
        ]);

        return [
            'success' => true,
            'new_status' => $newStatus,
            'message' => "Status akun @{$user['username']} berhasil diubah menjadi {$newStatus}."
        ];
    }
}
