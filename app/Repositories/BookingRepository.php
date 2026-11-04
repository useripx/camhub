<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;
use Throwable;

/**
 * Repository BookingRepository
 * 
 * Mengelola akses basis data transaksi pemesanan sewa kamera,
 * alokasi nomor seri unit fisik bebas benturan tanggal, serta pencatatan rekening bersama escrow.
 */
class BookingRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Mencari nomor seri unit fisik kamera yang tersedia dan bebas dari benturan jadwal
     * pada rentang tanggal sewa yang diminta.
     */
    public function findAvailableUnit(int $productId, string $startDate, string $endDate): ?array
    {
        $sql = "
            SELECT pu.id, pu.product_id, pu.serial_number, pu.condition_status, pu.availability_status
            FROM product_units pu
            WHERE pu.product_id = :product_id
              AND pu.availability_status != 'maintenance'
              AND pu.id NOT IN (
                  SELECT rb.product_unit_id
                  FROM rental_bookings rb
                  WHERE rb.rental_status NOT IN ('cancelled', 'returned', 'completed')
                    AND (rb.start_date <= :end_date AND rb.end_date >= :start_date)
              )
            ORDER BY pu.id ASC
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'product_id' => $productId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $unit = $stmt->fetch(PDO::FETCH_ASSOC);
        return $unit ?: null;
    }

    /**
     * Menyimpan pemesanan sewa dalam transaksi ACID PDO
     * (rental_bookings + payments + escrow_transactions + audit_logs)
     */
    public function createBookingWithTransaction(array $b, array $p): int
    {
        $this->pdo->beginTransaction();

        try {
            // Pessimistic Concurrency Guard (Anti-Race Condition & Anti-Double Booking)
            $stmtCheck = $this->pdo->prepare("
                SELECT id FROM rental_bookings 
                WHERE product_unit_id = :unit_id 
                  AND rental_status NOT IN ('cancelled', 'returned', 'completed')
                  AND (start_date <= :end_date AND end_date >= :start_date)
                FOR UPDATE
            ");
            $stmtCheck->execute([
                'unit_id' => $b['product_unit_id'],
                'start_date' => $b['start_date'],
                'end_date' => $b['end_date'],
            ]);
            if ($stmtCheck->fetchColumn()) {
                throw new \RuntimeException('Unit fisik kamera telah terbooking oleh transaksi konkuren lain pada tanggal tersebut.');
            }

            // 1. Simpan ke tabel rental_bookings
            $stmtBooking = $this->pdo->prepare("
                INSERT INTO rental_bookings (
                    user_id, product_unit_id, start_date, end_date, total_days,
                    daily_price, subtotal, admin_fee, total_amount, payment_status,
                    rental_status, created_at, updated_at
                ) VALUES (
                    :user_id, :product_unit_id, :start_date, :end_date, :total_days,
                    :daily_price, :subtotal, :admin_fee, :total_amount, :payment_status,
                    :rental_status, NOW(), NOW()
                )
            ");

            $stmtBooking->execute([
                'user_id' => $b['user_id'],
                'product_unit_id' => $b['product_unit_id'],
                'start_date' => $b['start_date'],
                'end_date' => $b['end_date'],
                'total_days' => $b['total_days'],
                'daily_price' => $b['daily_price'],
                'subtotal' => $b['subtotal'],
                'admin_fee' => $b['admin_fee'] ?? 5000.00,
                'total_amount' => $b['total_amount'],
                'payment_status' => $b['payment_status'] ?? 'unpaid',
                'rental_status' => $b['rental_status'] ?? 'pending',
            ]);

            $bookingId = (int)$this->pdo->lastInsertId();

            // 2. Simpan ke tabel payments
            $stmtPayment = $this->pdo->prepare("
                INSERT INTO payments (
                    user_id, order_type, reference_id, payment_method,
                    amount, admin_fee, total_amount, payment_status, created_at
                ) VALUES (
                    :user_id, 'rental', :reference_id, :payment_method,
                    :amount, :admin_fee, :total_amount, :payment_status, NOW()
                )
            ");

            $stmtPayment->execute([
                'user_id' => $b['user_id'],
                'reference_id' => $bookingId,
                'payment_method' => $p['payment_method'] ?? 'escrow',
                'amount' => $b['subtotal'],
                'admin_fee' => $b['admin_fee'] ?? 5000.00,
                'total_amount' => $b['total_amount'],
                'payment_status' => $p['payment_status'] ?? 'pending',
            ]);

            $paymentId = (int)$this->pdo->lastInsertId();

            // 3. Simpan ke tabel escrow_transactions (Penahanan Dana Rekening Bersama)
            $stmtEscrow = $this->pdo->prepare("
                INSERT INTO escrow_transactions (
                    payment_id, booking_id, amount_held, status, held_at, created_at
                ) VALUES (
                    :payment_id, :booking_id, :amount_held, 'held', NOW(), NOW()
                )
            ");

            $stmtEscrow->execute([
                'payment_id' => $paymentId,
                'booking_id' => $bookingId,
                'amount_held' => $b['subtotal'],
            ]);

            // 4. Catat aktivitas audit log
            $stmtAudit = $this->pdo->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, created_at)
                VALUES (:user_id, 'RENTAL_BOOKING_CREATE', 'rental_bookings', :target_id, :changes_json, NOW())
            ");
            $stmtAudit->execute([
                'user_id' => $b['user_id'],
                'target_id' => $bookingId,
                'changes_json' => json_encode([
                    'booking_code' => '#RENT-' . str_pad((string)$bookingId, 5, '0', STR_PAD_LEFT),
                    'product_unit_id' => $b['product_unit_id'],
                    'start_date' => $b['start_date'],
                    'end_date' => $b['end_date'],
                    'total_amount' => $b['total_amount'],
                ]),
            ]);

            $this->pdo->commit();
            return $bookingId;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Mengambil detail pesanan sewa lengkap dengan relasi produk, toko, dan pembayaran
     */
    public function findBookingById(int $bookingId, ?int $userId = null): ?array
    {
        $sql = "
            SELECT 
                rb.*,
                p.id AS product_id,
                p.name AS product_name,
                p.slug AS product_slug,
                pu.serial_number,
                pu.condition_status AS unit_condition,
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
            FROM rental_bookings rb
            JOIN product_units pu ON pu.id = rb.product_unit_id
            JOIN products p ON p.id = pu.product_id
            JOIN partner_stores s ON s.id = p.store_id
            JOIN users u ON u.id = rb.user_id
            LEFT JOIN payments pay ON pay.reference_id = rb.id AND pay.order_type = 'rental'
            LEFT JOIN product_images img ON img.product_id = p.id AND img.is_primary = 1
            WHERE rb.id = :booking_id
        ";

        $params = ['booking_id' => $bookingId];
        if ($userId !== null) {
            $sql .= " AND rb.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Mengambil riwayat seluruh pesanan sewa milik pelanggan
     */
    public function getUserBookings(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                rb.*,
                p.name AS product_name,
                p.slug AS product_slug,
                pu.serial_number,
                s.store_name,
                s.city AS store_city,
                COALESCE(img.image_path, '') AS product_image
            FROM rental_bookings rb
            JOIN product_units pu ON pu.id = rb.product_unit_id
            JOIN products p ON p.id = pu.product_id
            JOIN partner_stores s ON s.id = p.store_id
            LEFT JOIN product_images img ON img.product_id = p.id AND img.is_primary = 1
            WHERE rb.user_id = :user_id
            ORDER BY rb.id DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

