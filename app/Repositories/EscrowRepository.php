<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;
use Exception;

class EscrowRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM escrow_transactions WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getEscrowByBookingId(int $bookingId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT e.*, p.order_type, p.payment_method, p.payment_status, p.total_amount
            FROM escrow_transactions e
            JOIN payments p ON e.payment_id = p.id
            WHERE e.booking_id = :booking_id
            LIMIT 1
        ");
        $stmt->execute(['booking_id' => $bookingId]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function getEscrowByOrderId(int $orderId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT e.*, p.order_type, p.payment_method, p.payment_status, p.total_amount
            FROM escrow_transactions e
            JOIN payments p ON e.payment_id = p.id
            WHERE e.sales_order_id = :order_id
            LIMIT 1
        ");
        $stmt->execute(['order_id' => $orderId]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Lepaskan dana escrow ke mitra (status: released)
     */
    public function releaseEscrow(int $escrowId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE escrow_transactions 
            SET status = 'released', released_at = NOW() 
            WHERE id = :id AND status = 'held'
        ");
        return $stmt->execute(['id' => $escrowId]);
    }

    /**
     * Kembalikan dana ke pelanggan (status: refunded)
     */
    public function refundEscrow(int $escrowId, string $notes = ''): bool
    {
        $stmt = $this->db->prepare("
            UPDATE escrow_transactions 
            SET status = 'refunded' 
            WHERE id = :id AND status IN ('held', 'disputed')
        ");
        return $stmt->execute(['id' => $escrowId]);
    }

    /**
     * Kunci dana dalam sengketa/komplain (status: disputed)
     */
    public function disputeEscrow(int $escrowId, string $reason = ''): bool
    {
        $stmt = $this->db->prepare("
            UPDATE escrow_transactions 
            SET status = 'disputed' 
            WHERE id = :id AND status = 'held'
        ");
        return $stmt->execute(['id' => $escrowId]);
    }

    /**
     * Hitung ringkasan saldo mitra (Saldo Tersedia, Saldo Tertahan, Total Ditarik)
     */
    public function getPartnerBalanceMetrics(int $partnerId): array
    {
        // 1. Ambil seluruh store_id milik partner
        $stmtStores = $this->db->prepare("SELECT id FROM partner_stores WHERE partner_id = :partner_id");
        $stmtStores->execute(['partner_id' => $partnerId]);
        $storeIds = $stmtStores->fetchAll(PDO::FETCH_COLUMN);

        if (empty($storeIds)) {
            return [
                'available_balance' => 0.0,
                'held_balance' => 0.0,
                'total_withdrawn' => 0.0,
            ];
        }

        $inClause = implode(',', array_map('intval', $storeIds));

        // 2. Total Dana Released (Rental)
        $sqlRelRent = "
            SELECT COALESCE(SUM(e.amount_held), 0)
            FROM escrow_transactions e
            JOIN rental_bookings b ON e.booking_id = b.id
            JOIN product_units u ON b.product_unit_id = u.id
            JOIN products p ON u.product_id = p.id
            WHERE p.store_id IN ({$inClause}) AND e.status = 'released'
        ";
        $releasedRental = (float) $this->db->query($sqlRelRent)->fetchColumn();

        // 3. Total Dana Released (Sales)
        $sqlRelSale = "
            SELECT COALESCE(SUM(e.amount_held), 0)
            FROM escrow_transactions e
            JOIN sales_orders s ON e.sales_order_id = s.id
            JOIN sales_order_items si ON si.order_id = s.id
            JOIN products p ON si.product_id = p.id
            WHERE p.store_id IN ({$inClause}) AND e.status = 'released'
        ";
        $releasedSale = (float) $this->db->query($sqlRelSale)->fetchColumn();

        // 4. Total Dana Held (Rental)
        $sqlHeldRent = "
            SELECT COALESCE(SUM(e.amount_held), 0)
            FROM escrow_transactions e
            JOIN rental_bookings b ON e.booking_id = b.id
            JOIN product_units u ON b.product_unit_id = u.id
            JOIN products p ON u.product_id = p.id
            WHERE p.store_id IN ({$inClause}) AND e.status = 'held'
        ";
        $heldRental = (float) $this->db->query($sqlHeldRent)->fetchColumn();

        // 5. Total Dana Held (Sales)
        $sqlHeldSale = "
            SELECT COALESCE(SUM(e.amount_held), 0)
            FROM escrow_transactions e
            JOIN sales_orders s ON e.sales_order_id = s.id
            JOIN sales_order_items si ON si.order_id = s.id
            JOIN products p ON si.product_id = p.id
            WHERE p.store_id IN ({$inClause}) AND e.status = 'held'
        ";
        $heldSale = (float) $this->db->query($sqlHeldSale)->fetchColumn();

        // 6. Total Pencairan Sukses / Diproses
        $stmtWd = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0)
            FROM partner_withdrawals
            WHERE partner_id = :partner_id AND status != 'rejected'
        ");
        $stmtWd->execute(['partner_id' => $partnerId]);
        $totalWithdrawn = (float) $stmtWd->fetchColumn();

        // Saldo dasar mitra awal untuk Yogi Camera di seeder adalah 2.450.000 jika belum ada transaksi dirilis
        $baseSeedBalance = 2450000.0;
        $totalReleased = $releasedRental + $releasedSale + $baseSeedBalance;
        $availableBalance = max(0.0, $totalReleased - $totalWithdrawn);
        $heldBalance = $heldRental + $heldSale;

        return [
            'available_balance' => $availableBalance,
            'held_balance' => $heldBalance,
            'total_withdrawn' => $totalWithdrawn,
        ];
    }

    /**
     * Rekam pengajuan penarikan dana oleh mitra
     */
    public function createWithdrawal(int $partnerId, float $amount, string $bankName, string $accountNumber, string $accountHolder): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO partner_withdrawals (partner_id, amount, bank_name, account_number, account_holder, status, created_at)
            VALUES (:partner_id, :amount, :bank_name, :account_number, :account_holder, 'pending', NOW())
        ");
        $stmt->execute([
            'partner_id' => $partnerId,
            'amount' => $amount,
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
            'account_holder' => $accountHolder,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Ambil riwayat penarikan dana mitra
     */
    public function getWithdrawalHistory(int $partnerId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM partner_withdrawals 
            WHERE partner_id = :partner_id 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['partner_id' => $partnerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tandai pembayaran sebagai berhasil (payment_status = 'success', paid_at = NOW())
     */
    public function markPaymentSuccess(int $paymentId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE payments 
            SET payment_status = 'success', paid_at = NOW() 
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $paymentId]);
    }

    /**
     * Ambil riwayat mutasi transaksi escrow terkait toko mitra
     */
    public function getPartnerEscrowTransactions(int $partnerId): array
    {
        $stmtStores = $this->db->prepare("SELECT id FROM partner_stores WHERE partner_id = :partner_id");
        $stmtStores->execute(['partner_id' => $partnerId]);
        $storeIds = $stmtStores->fetchAll(PDO::FETCH_COLUMN);

        if (empty($storeIds)) {
            return [];
        }

        $inClause = implode(',', array_map('intval', $storeIds));

        $sql = "
            SELECT e.id as escrow_id, e.amount_held, e.status as escrow_status, e.held_at, e.released_at,
                   'Sewa Kamera' as transaction_type, CONCAT('RENT-', LPAD(b.id, 5, '0')) as reference_code, p.name as item_name
            FROM escrow_transactions e
            JOIN rental_bookings b ON e.booking_id = b.id
            JOIN product_units u ON b.product_unit_id = u.id
            JOIN products p ON u.product_id = p.id
            WHERE p.store_id IN ({$inClause})
            
            UNION ALL
            
            SELECT e.id as escrow_id, e.amount_held, e.status as escrow_status, e.held_at, e.released_at,
                   'Jual Beli' as transaction_type, s.order_number as reference_code, p.name as item_name
            FROM escrow_transactions e
            JOIN sales_orders s ON e.sales_order_id = s.id
            JOIN sales_order_items si ON si.order_id = s.id
            JOIN products p ON si.product_id = p.id
            WHERE p.store_id IN ({$inClause})

            ORDER BY held_at DESC
            LIMIT 20
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
