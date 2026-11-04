<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ReportRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    private function buildDateCondition(string $column, string $period, ?string $start = null, ?string $end = null): array
    {
        $sql = "";
        $params = [];

        switch ($period) {
            case 'today':
                $sql = "DATE({$column}) = CURDATE()";
                break;
            case '7days':
                $sql = "{$column} >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $sql = "YEAR({$column}) = YEAR(CURDATE()) AND MONTH({$column}) = MONTH(CURDATE())";
                break;
            case 'custom':
                if (!empty($start) && !empty($end)) {
                    $sql = "{$column} >= :start_date AND {$column} <= :end_date";
                    $params['start_date'] = $start . ' 00:00:00';
                    $params['end_date'] = $end . ' 23:59:59';
                } else {
                    $sql = "1=1";
                }
                break;
            case 'all':
            default:
                $sql = "1=1";
                break;
        }

        return ['sql' => $sql, 'params' => $params];
    }

    public function getFinancialSummary(string $period = 'all', ?string $start = null, ?string $end = null): array
    {
        // 1. Rental Totals
        $rentalCond = $this->buildDateCondition('rb.created_at', $period, $start, $end);
        $stmtRental = $this->db->prepare("
            SELECT COUNT(*) as total_rentals, COALESCE(SUM(rb.total_amount), 0) as rental_gross
            FROM rental_bookings rb
            WHERE rb.rental_status != 'cancelled' AND {$rentalCond['sql']}
        ");
        $stmtRental->execute($rentalCond['params']);
        $rentalStats = $stmtRental->fetch(PDO::FETCH_ASSOC) ?: ['total_rentals' => 0, 'rental_gross' => 0];

        // 2. Sales Totals
        $salesCond = $this->buildDateCondition('so.created_at', $period, $start, $end);
        $stmtSales = $this->db->prepare("
            SELECT COUNT(*) as total_sales, COALESCE(SUM(so.total_amount), 0) as sales_gross
            FROM sales_orders so
            WHERE so.order_status != 'cancelled' AND {$salesCond['sql']}
        ");
        $stmtSales->execute($salesCond['params']);
        $salesStats = $stmtSales->fetch(PDO::FETCH_ASSOC) ?: ['total_sales' => 0, 'sales_gross' => 0];

        // 3. Fines Collected (Paid)
        $finesCond = $this->buildDateCondition('f.created_at', $period, $start, $end);
        $stmtFines = $this->db->prepare("
            SELECT COUNT(*) as paid_fines_count, COALESCE(SUM(f.amount), 0) as fines_total
            FROM fines f
            WHERE f.status = 'paid' AND {$finesCond['sql']}
        ");
        $stmtFines->execute($finesCond['params']);
        $finesStats = $stmtFines->fetch(PDO::FETCH_ASSOC) ?: ['paid_fines_count' => 0, 'fines_total' => 0];

        // 4. Consignment Commission (from sold items)
        $csgCond = $this->buildDateCondition('c.created_at', $period, $start, $end);
        $stmtCsg = $this->db->prepare("
            SELECT COALESCE(SUM(c.agreed_price * (c.commission_percent / 100)), 0) as csg_commission
            FROM consignments c
            WHERE c.status IN ('approved', 'listed', 'sold') AND {$csgCond['sql']}
        ");
        $stmtCsg->execute($csgCond['params']);
        $csgCommission = (float) $stmtCsg->fetchColumn();

        // 5. Total Withdrawn by Partners (Processed/Approved)
        $wdCond = $this->buildDateCondition('pw.created_at', $period, $start, $end);
        $stmtWd = $this->db->prepare("
            SELECT COALESCE(SUM(pw.amount), 0) as withdrawn_total
            FROM partner_withdrawals pw
            WHERE pw.status IN ('approved', 'processed') AND {$wdCond['sql']}
        ");
        $stmtWd->execute($wdCond['params']);
        $withdrawnTotal = (float) $stmtWd->fetchColumn();

        // Calculations
        $totalTransactions = (int)$rentalStats['total_rentals'] + (int)$salesStats['total_sales'];
        $grossTurnover = (float)$rentalStats['rental_gross'] + (float)$salesStats['sales_gross'];
        
        // Fee admin rekening bersama Rp 5.000 per transaksi aktif
        $escrowAdminFee = $totalTransactions * 5000.0;
        
        // Total platform net revenue
        $platformNetRevenue = $escrowAdminFee + $csgCommission + (float)$finesStats['fines_total'];

        return [
            'period' => $period,
            'total_transactions' => $totalTransactions,
            'total_rentals' => (int)$rentalStats['total_rentals'],
            'total_sales' => (int)$salesStats['total_sales'],
            'rental_gross' => (float)$rentalStats['rental_gross'],
            'sales_gross' => (float)$salesStats['sales_gross'],
            'gross_turnover' => $grossTurnover,
            'escrow_admin_fee' => $escrowAdminFee,
            'csg_commission' => $csgCommission,
            'fines_collected' => (float)$finesStats['fines_total'],
            'platform_net_revenue' => $platformNetRevenue,
            'withdrawn_total' => $withdrawnTotal,
        ];
    }

    public function getCategoryBreakdown(string $period = 'all', ?string $start = null, ?string $end = null): array
    {
        $cond = $this->buildDateCondition('rb.created_at', $period, $start, $end);

        $stmt = $this->db->prepare("
            SELECT c.id, c.name, COUNT(rb.id) as booking_count, COALESCE(SUM(rb.total_amount), 0) as total_revenue
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            LEFT JOIN product_units pu ON pu.product_id = p.id
            LEFT JOIN rental_bookings rb ON rb.product_unit_id = pu.id AND rb.rental_status != 'cancelled' AND {$cond['sql']}
            GROUP BY c.id, c.name
            ORDER BY total_revenue DESC
        ");
        $stmt->execute($cond['params']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTransactionsForReport(string $period = 'all', ?string $start = null, ?string $end = null, int $limit = 100): array
    {
        // Rentals
        $rCond = $this->buildDateCondition('rb.created_at', $period, $start, $end);
        $stmtR = $this->db->prepare("
            SELECT 
                rb.id,
                CONCAT('#RENT-', LPAD(rb.id, 5, '0')) as code,
                rb.created_at as tx_date,
                'Sewa' as tx_type,
                COALESCE(u.full_name, 'Pelanggan') as customer_name,
                COALESCE(ps.store_name, 'Mitra CamHub') as store_name,
                p.name as item_name,
                rb.total_amount as amount,
                rb.admin_fee as platform_fee,
                rb.payment_status as payment_status,
                COALESCE(et.status, 'held') as escrow_status
            FROM rental_bookings rb
            JOIN product_units pu ON rb.product_unit_id = pu.id
            JOIN products p ON pu.product_id = p.id
            JOIN partner_stores ps ON p.store_id = ps.id
            JOIN users u ON rb.user_id = u.id
            LEFT JOIN escrow_transactions et ON et.booking_id = rb.id
            WHERE {$rCond['sql']}
            ORDER BY rb.created_at DESC
            LIMIT :limit
        ");
        foreach ($rCond['params'] as $k => $v) {
            $stmtR->bindValue(':' . $k, $v);
        }
        $stmtR->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmtR->execute();
        $rentals = $stmtR->fetchAll(PDO::FETCH_ASSOC);

        // Sales
        $sCond = $this->buildDateCondition('so.created_at', $period, $start, $end);
        $stmtS = $this->db->prepare("
            SELECT 
                so.id,
                CONCAT('#BUY-', LPAD(so.id, 5, '0')) as code,
                so.created_at as tx_date,
                'Jual Beli' as tx_type,
                COALESCE(u.full_name, 'Pembeli') as customer_name,
                COALESCE(ps.store_name, 'Mitra CamHub') as store_name,
                COALESCE(p.name, 'Kamera Marketplace') as item_name,
                so.total_amount as amount,
                so.admin_fee as platform_fee,
                'paid' as payment_status,
                COALESCE(et.status, 'held') as escrow_status
            FROM sales_orders so
            JOIN users u ON so.user_id = u.id
            LEFT JOIN sales_order_items soi ON soi.order_id = so.id
            LEFT JOIN products p ON soi.product_id = p.id
            LEFT JOIN partner_stores ps ON p.store_id = ps.id
            LEFT JOIN escrow_transactions et ON et.sales_order_id = so.id
            WHERE {$sCond['sql']}
            ORDER BY so.created_at DESC
            LIMIT :limit
        ");
        foreach ($sCond['params'] as $k => $v) {
            $stmtS->bindValue(':' . $k, $v);
        }
        $stmtS->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmtS->execute();
        $sales = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        // Merge and sort by tx_date descending
        $all = array_merge($rentals, $sales);
        usort($all, function ($a, $b) {
            return strcmp($b['tx_date'], $a['tx_date']);
        });

        return array_slice($all, 0, $limit);
    }

    public function getPartnerTransactionHistory(int $partnerId): array
    {
        // Temukan store milik partner
        $stmtStore = $this->db->prepare("SELECT id FROM partner_stores WHERE partner_id = :partner_id");
        $stmtStore->execute(['partner_id' => $partnerId]);
        $storeIds = $stmtStore->fetchAll(PDO::FETCH_COLUMN);

        if (empty($storeIds)) {
            return [];
        }

        $inClause = implode(',', array_map('intval', $storeIds));

        $sql = "
            SELECT 
                e.id,
                e.amount_held as amount,
                e.status as escrow_status,
                e.held_at,
                e.released_at,
                CONCAT('#RENT-', LPAD(b.id, 5, '0')) as code,
                'Penyewaan Kamera' as tx_type
            FROM escrow_transactions e
            JOIN rental_bookings b ON e.booking_id = b.id
            JOIN product_units u ON b.product_unit_id = u.id
            JOIN products p ON u.product_id = p.id
            WHERE p.store_id IN ({$inClause})
            
            UNION ALL
            
            SELECT 
                e.id,
                e.amount_held as amount,
                e.status as escrow_status,
                e.held_at,
                e.released_at,
                CONCAT('#BUY-', LPAD(s.id, 5, '0')) as code,
                'Jual Beli Kamera' as tx_type
            FROM escrow_transactions e
            JOIN sales_orders s ON e.sales_order_id = s.id
            JOIN sales_order_items si ON si.order_id = s.id
            JOIN products p ON si.product_id = p.id
            WHERE p.store_id IN ({$inClause})
            
            ORDER BY held_at DESC
        ";

        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
}
