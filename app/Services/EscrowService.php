<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\EscrowRepository;
use App\Core\Database;
use PDO;
use Exception;

class EscrowService
{
    private EscrowRepository $repo;
    private PDO $db;

    public function __construct()
    {
        $this->repo = new EscrowRepository();
        $this->db = Database::getConnection();
    }

    /**
     * Pelanggan konfirmasi sewa selesai -> melepaskan dana escrow ke toko mitra
     */
    public function releaseRentalEscrow(int $bookingId, int $userId): array
    {
        // Validasi kepemilikan pesanan sewa
        $stmt = $this->db->prepare("SELECT * FROM rental_bookings WHERE id = :id AND user_id = :user_id LIMIT 1");
        $stmt->execute(['id' => $bookingId, 'user_id' => $userId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            return ['success' => false, 'message' => 'Pesanan sewa tidak ditemukan atau bukan milik akun Anda.'];
        }

        $escrow = $this->repo->getEscrowByBookingId($bookingId);
        if (!$escrow) {
            return ['success' => false, 'message' => 'Data rekening bersama (escrow) tidak ditemukan.'];
        }

        if ($escrow['status'] === 'released') {
            return ['success' => true, 'message' => 'Dana escrow untuk pesanan ini telah berhasil dirilis sebelumnya.'];
        }

        if ($escrow['status'] !== 'held') {
            return ['success' => false, 'message' => "Dana tidak dapat dirilis karena status escrow saat ini adalah: {$escrow['status']}."];
        }

        $this->db->beginTransaction();
        try {
            $this->repo->releaseEscrow((int) $escrow['id']);

            // Update booking status ke completed jika masih aktif
            $stmtUp = $this->db->prepare("UPDATE rental_bookings SET rental_status = 'completed' WHERE id = :id");
            $stmtUp->execute(['id' => $bookingId]);

            // Audit log
            $stmtAudit = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, 'ESCROW_RELEASE', 'escrow_transactions', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $userId,
                'target_id' => $escrow['id'],
                'changes_json' => json_encode([
                    'action' => 'release',
                    'type' => 'rental',
                    'booking_id' => $bookingId,
                    'amount_released' => (float)$escrow['amount_held']
                ])
            ]);

            $this->db->commit();
            return ['success' => true, 'message' => 'Terima kasih! Konfirmasi selesai berhasil, dana sewa telah diteruskan ke mitra.'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Gagal melepaskan dana escrow: ' . $e->getMessage()];
        }
    }

    /**
     * Pembeli konfirmasi barang diterima -> melepaskan dana escrow ke toko mitra
     */
    public function releaseSalesEscrow(int $orderId, int $userId): array
    {
        // Validasi kepemilikan pesanan beli
        $stmt = $this->db->prepare("SELECT * FROM sales_orders WHERE id = :id AND user_id = :user_id LIMIT 1");
        $stmt->execute(['id' => $orderId, 'user_id' => $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return ['success' => false, 'message' => 'Pesanan pembelian tidak ditemukan atau bukan milik akun Anda.'];
        }

        $escrow = $this->repo->getEscrowByOrderId($orderId);
        if (!$escrow) {
            return ['success' => false, 'message' => 'Data rekening bersama (escrow) tidak ditemukan.'];
        }

        if ($escrow['status'] === 'released') {
            return ['success' => true, 'message' => 'Dana escrow untuk pesanan ini telah berhasil dirilis sebelumnya.'];
        }

        if ($escrow['status'] !== 'held') {
            return ['success' => false, 'message' => "Dana tidak dapat dirilis karena status escrow saat ini adalah: {$escrow['status']}."];
        }

        $this->db->beginTransaction();
        try {
            $this->repo->releaseEscrow((int) $escrow['id']);

            // Update order status ke completed
            $stmtUp = $this->db->prepare("UPDATE sales_orders SET order_status = 'completed' WHERE id = :id");
            $stmtUp->execute(['id' => $orderId]);

            // Audit log
            $stmtAudit = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, 'ESCROW_RELEASE', 'escrow_transactions', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $userId,
                'target_id' => $escrow['id'],
                'changes_json' => json_encode([
                    'action' => 'release',
                    'type' => 'sales',
                    'order_id' => $orderId,
                    'amount_released' => (float)$escrow['amount_held']
                ])
            ]);

            $this->db->commit();
            return ['success' => true, 'message' => 'Konfirmasi penerimaan berhasil! Dana pembelian telah diteruskan ke mitra lapak.'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Gagal melepaskan dana escrow: ' . $e->getMessage()];
        }
    }

    /**
     * Pengajuan sengketa / komplain transaksi oleh pelanggan
     */
    public function requestDispute(string $type, int $referenceId, int $userId, string $reason): array
    {
        $escrow = ($type === 'sale') 
            ? $this->repo->getEscrowByOrderId($referenceId) 
            : $this->repo->getEscrowByBookingId($referenceId);

        if (!$escrow) {
            return ['success' => false, 'message' => 'Data transaksi escrow tidak ditemukan.'];
        }

        if ($escrow['status'] !== 'held') {
            return ['success' => false, 'message' => 'Hanya transaksi berstatus held yang dapat diajukan sengketa.'];
        }

        try {
            $this->repo->disputeEscrow((int) $escrow['id'], $reason);

            // Audit log
            $stmtAudit = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, 'ESCROW_DISPUTE', 'escrow_transactions', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $userId,
                'target_id' => $escrow['id'],
                'changes_json' => json_encode([
                    'action' => 'dispute',
                    'type' => $type,
                    'reference_id' => $referenceId,
                    'reason' => $reason
                ])
            ]);

            return ['success' => true, 'message' => 'Sengketa berhasil diajukan. Dana ditangguhkan dan Tim Admin akan segera memverifikasi.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Gagal mengajukan sengketa: ' . $e->getMessage()];
        }
    }

    /**
     * Pengembalian dana escrow ke pelanggan (refund) jika terjadi pembatalan
     */
    public function refundEscrow(int $escrowId, int $adminUserId = 1, string $notes = 'Pembatalan transaksi'): array
    {
        $escrow = $this->repo->findById($escrowId);
        if (!$escrow) {
            return ['success' => false, 'message' => 'Data escrow tidak ditemukan.'];
        }

        if (!in_array($escrow['status'], ['held', 'disputed'], true)) {
            return ['success' => false, 'message' => "Escrow dengan status {$escrow['status']} tidak dapat direfund."];
        }

        $this->db->beginTransaction();
        try {
            $this->repo->refundEscrow($escrowId, $notes);

            if (!empty($escrow['booking_id'])) {
                $stmt = $this->db->prepare("UPDATE rental_bookings SET rental_status = 'cancelled' WHERE id = :id");
                $stmt->execute(['id' => $escrow['booking_id']]);
            } elseif (!empty($escrow['sales_order_id'])) {
                $stmt = $this->db->prepare("UPDATE sales_orders SET order_status = 'cancelled' WHERE id = :id");
                $stmt->execute(['id' => $escrow['sales_order_id']]);
            }

            // Audit log
            $stmtAudit = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, 'ESCROW_REFUND', 'escrow_transactions', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $adminUserId,
                'target_id' => $escrowId,
                'changes_json' => json_encode([
                    'action' => 'refund',
                    'amount_refunded' => (float)$escrow['amount_held'],
                    'reason' => $notes
                ])
            ]);

            $this->db->commit();
            return ['success' => true, 'message' => 'Dana escrow berhasil direfund ke pelanggan.'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Gagal memproses refund: ' . $e->getMessage()];
        }
    }

    /**
     * Simulasi callback/konfirmasi pembayaran sukses dari gateway
     */
    public function simulatePaymentSuccess(int $paymentId): bool
    {
        return $this->repo->markPaymentSuccess($paymentId);
    }

    /**
     * Pengajuan pencairan saldo oleh toko mitra
     */
    public function requestWithdrawal(int $partnerId, float $amount, array $bankData): array
    {
        $metrics = $this->repo->getPartnerBalanceMetrics($partnerId);
        $available = $metrics['available_balance'];

        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Nominal penarikan harus berupa angka positif.'];
        }

        if ($amount < 50000) {
            return ['success' => false, 'message' => 'Batas minimal penarikan saldo adalah Rp 50.000.'];
        }

        if ($amount > $available) {
            return ['success' => false, 'message' => 'Nominal penarikan (Rp ' . number_format($amount, 0, ',', '.') . ') melebihi saldo tersedia Anda (Rp ' . number_format($available, 0, ',', '.') . ').'];
        }

        $bankName = trim($bankData['bank_name'] ?? '');
        $accNumber = trim($bankData['account_number'] ?? '');
        $accHolder = trim($bankData['account_holder'] ?? '');

        if ($bankName === '' || $accNumber === '' || $accHolder === '') {
            return ['success' => false, 'message' => 'Nama bank, nomor rekening, dan nama pemilik rekening wajib diisi lengkap.'];
        }

        try {
            $id = $this->repo->createWithdrawal($partnerId, $amount, $bankName, $accNumber, $accHolder);
            return [
                'success' => true,
                'withdrawal_id' => $id,
                'message' => 'Permintaan penarikan dana sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil diajukan dan sedang diproses admin.'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Gagal memproses penarikan: ' . $e->getMessage()];
        }
    }

    /**
     * Mengambil data dompet & riwayat saldo mitra
     */
    public function getPartnerWalletData(int $partnerId): array
    {
        $metrics = $this->repo->getPartnerBalanceMetrics($partnerId);
        $transactions = $this->repo->getPartnerEscrowTransactions($partnerId);
        $withdrawals = $this->repo->getWithdrawalHistory($partnerId);

        return [
            'metrics' => $metrics,
            'transactions' => $transactions,
            'withdrawals' => $withdrawals
        ];
    }
}
