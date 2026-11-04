<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;
use Throwable;

class SalesRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Cari produk yang tersedia untuk dibeli (is_for_sale = 1)
     */
    public function findProductForSale(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.*,
                c.name AS category_name,
                c.slug AS category_slug,
                s.id AS store_id,
                s.store_name,
                s.city AS store_city,
                s.address AS store_address,
                s.phone AS store_phone,
                COALESCE(img.image_path, '') AS primary_image
            FROM products p
            JOIN categories c ON c.id = p.category_id
            JOIN partner_stores s ON s.id = p.store_id
            LEFT JOIN product_images img ON img.product_id = p.id AND img.is_primary = 1
            WHERE p.slug = :slug AND p.is_sale = 1
            LIMIT 1
        ");
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Eksekusi transaksi pembelian kamera secara atomic (ACID)
     * Mengunci penulisan ke: sales_orders, sales_order_items, payments, escrow_transactions, audit_logs
     */
    public function createSalesOrderWithTransaction(array $orderData, array $itemData, array $paymentData): int
    {
        $this->pdo->beginTransaction();

        try {
            // 1. Simpan ke sales_orders dengan kode sementara
            $tempOrderNumber = 'TEMP-' . uniqid();
            $stmtOrder = $this->pdo->prepare("
                INSERT INTO sales_orders (
                    user_id, order_number, subtotal, admin_fee, total_amount,
                    order_status, shipping_address, created_at, updated_at
                ) VALUES (
                    :user_id, :order_number, :subtotal, :admin_fee, :total_amount,
                    :order_status, :shipping_address, NOW(), NOW()
                )
            ");

            $stmtOrder->execute([
                'user_id' => $orderData['user_id'],
                'order_number' => $tempOrderNumber,
                'subtotal' => $orderData['subtotal'],
                'admin_fee' => $orderData['admin_fee'] ?? 5000.00,
                'total_amount' => $orderData['total_amount'],
                'order_status' => $orderData['order_status'] ?? 'pending',
                'shipping_address' => $orderData['shipping_address'],
            ]);

            $orderId = (int)$this->pdo->lastInsertId();
            $officialOrderNumber = '#BUY-' . str_pad((string)$orderId, 5, '0', STR_PAD_LEFT);

            // Update order_number resmi
            $stmtUpdate = $this->pdo->prepare("UPDATE sales_orders SET order_number = :code WHERE id = :id");
            $stmtUpdate->execute(['code' => $officialOrderNumber, 'id' => $orderId]);

            // 2. Simpan ke sales_order_items
            $stmtItem = $this->pdo->prepare("
                INSERT INTO sales_order_items (
                    order_id, product_id, quantity, unit_price, subtotal
                ) VALUES (
                    :order_id, :product_id, :quantity, :unit_price, :subtotal
                )
            ");

            $stmtItem->execute([
                'order_id' => $orderId,
                'product_id' => $itemData['product_id'],
                'quantity' => $itemData['quantity'] ?? 1,
                'unit_price' => $itemData['unit_price'],
                'subtotal' => $itemData['subtotal'],
            ]);

            // 3. Simpan ke tabel payments (order_type = 'sale', reference_id = orderId)
            $stmtPayment = $this->pdo->prepare("
                INSERT INTO payments (
                    user_id, order_type, reference_id, payment_method,
                    amount, admin_fee, total_amount, payment_status, created_at
                ) VALUES (
                    :user_id, 'sale', :reference_id, :payment_method,
                    :amount, :admin_fee, :total_amount, :payment_status, NOW()
                )
            ");

            $stmtPayment->execute([
                'user_id' => $orderData['user_id'],
                'reference_id' => $orderId,
                'payment_method' => $paymentData['payment_method'] ?? 'escrow',
                'amount' => $orderData['subtotal'],
                'admin_fee' => $orderData['admin_fee'] ?? 5000.00,
                'total_amount' => $orderData['total_amount'],
                'payment_status' => $paymentData['payment_status'] ?? 'pending',
            ]);

            $paymentId = (int)$this->pdo->lastInsertId();

            // 4. Simpan ke escrow_transactions (Penahanan Dana Rekening Bersama Marketplace)
            $stmtEscrow = $this->pdo->prepare("
                INSERT INTO escrow_transactions (
                    payment_id, booking_id, sales_order_id, amount_held, status, held_at, created_at
                ) VALUES (
                    :payment_id, NULL, :sales_order_id, :amount_held, 'held', NOW(), NOW()
                )
            ");

            $stmtEscrow->execute([
                'payment_id' => $paymentId,
                'sales_order_id' => $orderId,
                'amount_held' => $orderData['subtotal'],
            ]);

            // 5. Catat aktivitas audit_logs
            $stmtAudit = $this->pdo->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, 'SALES_ORDER_CREATE', 'sales_orders', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $orderData['user_id'],
                'target_id' => $orderId,
                'changes_json' => json_encode([
                    'order_number' => $officialOrderNumber,
                    'product_id' => $itemData['product_id'],
                    'total_amount' => $orderData['total_amount'],
                    'payment_method' => $paymentData['payment_method'] ?? 'escrow',
                ]),
            ]);

            $this->pdo->commit();
            return $orderId;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Mengambil detail pesanan pembelian lengkap dengan rincian toko dan pembayaran
     */
    public function findOrderById(int $orderId, ?int $userId = null): ?array
    {
        $sql = "
            SELECT 
                so.*,
                soi.product_id,
                soi.quantity,
                soi.unit_price,
                soi.subtotal AS item_subtotal,
                p.name AS product_name,
                p.slug AS product_slug,
                s.id AS store_id,
                s.store_name,
                s.city AS store_city,
                s.address AS store_address,
                s.phone AS store_phone,
                u.full_name AS customer_name,
                u.phone AS customer_phone,
                u.email AS customer_email,
                pay.payment_method,
                pay.payment_status AS pay_status,
                COALESCE(img.image_path, '') AS product_image
            FROM sales_orders so
            JOIN sales_order_items soi ON soi.order_id = so.id
            JOIN products p ON p.id = soi.product_id
            JOIN partner_stores s ON s.id = p.store_id
            JOIN users u ON u.id = so.user_id
            LEFT JOIN payments pay ON pay.reference_id = so.id AND pay.order_type = 'sale'
            LEFT JOIN product_images img ON img.product_id = p.id AND img.is_primary = 1
            WHERE so.id = :order_id
        ";

        $params = ['order_id' => $orderId];
        if ($userId !== null) {
            $sql .= " AND so.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Mengambil riwayat seluruh pesanan pembelian milik pelanggan
     */
    public function getUserSalesOrders(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                so.*,
                soi.quantity,
                soi.unit_price,
                p.name AS product_name,
                p.slug AS product_slug,
                s.store_name,
                s.city AS store_city,
                COALESCE(img.image_path, '') AS product_image
            FROM sales_orders so
            JOIN sales_order_items soi ON soi.order_id = so.id
            JOIN products p ON p.id = soi.product_id
            JOIN partner_stores s ON s.id = p.store_id
            LEFT JOIN product_images img ON img.product_id = p.id AND img.is_primary = 1
            WHERE so.user_id = :user_id
            ORDER BY so.id DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
