<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\SalesRepository;
use App\Repositories\UserRepository;
use App\Validators\SalesValidator;
use Throwable;

class SalesService
{
    public const SHIPPING_FEE = 25000.00; // Flat Rp 25.000 Ongkir Aman Berasuransi
    public const ESCROW_ADMIN_FEE = 5000.00; // Flat Rp 5.000 Biaya Layanan Rekber Escrow

    private SalesRepository $salesRepo;
    private UserRepository $userRepo;
    private SalesValidator $validator;

    public function __construct(
        ?SalesRepository $salesRepo = null,
        ?UserRepository $userRepo = null,
        ?SalesValidator $validator = null
    ) {
        $this->salesRepo = $salesRepo ?? new SalesRepository();
        $this->userRepo = $userRepo ?? new UserRepository();
        $this->validator = $validator ?? new SalesValidator();
    }

    /**
     * Mempersiapkan data checkout pembelian kamera
     */
    public function prepareCheckout(int $userId, string $productSlug): ?array
    {
        $product = $this->salesRepo->findProductForSale($productSlug);
        if (!$product) {
            return null;
        }

        $user = $this->userRepo->findById($userId);
        if (!$user) {
            return null;
        }

        $profile = $this->userRepo->getProfile($userId);

        $salePrice = (float)($product['sale_price'] ?? 0);
        $shippingFee = self::SHIPPING_FEE;
        $adminFee = self::ESCROW_ADMIN_FEE;
        $totalAmount = $salePrice + $shippingFee + $adminFee;

        return [
            'product' => $product,
            'user' => $user,
            'profile' => $profile,
            'pricing' => [
                'sale_price' => $salePrice,
                'shipping_fee' => $shippingFee,
                'admin_fee' => $adminFee,
                'total_amount' => $totalAmount,
                'formatted_sale_price' => 'Rp ' . number_format($salePrice, 0, ',', '.'),
                'formatted_shipping_fee' => 'Rp ' . number_format($shippingFee, 0, ',', '.'),
                'formatted_admin_fee' => 'Rp ' . number_format($adminFee, 0, ',', '.'),
                'formatted_total' => 'Rp ' . number_format($totalAmount, 0, ',', '.'),
            ],
            'prefill' => [
                'recipient_name' => $user['full_name'] ?? '',
                'phone' => $user['phone'] ?? '',
                'shipping_address' => $profile['address'] ?? '',
                'city' => $profile['city'] ?? '',
            ],
        ];
    }

    /**
     * Memproses formulir checkout dan menyimpan transaksi pembelian secara atomic
     */
    public function processCheckout(int $userId, string $productSlug, array $postData): array
    {
        // 1. Validasi formulir server-side
        $errors = $this->validator->validateCheckout($postData);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

        // 2. Cek produk tersedia untuk dibeli
        $product = $this->salesRepo->findProductForSale($productSlug);
        if (!$product) {
            return [
                'success' => false,
                'errors' => ['product' => 'Kamera ini tidak lagi tersedia untuk dibeli atau stok telah habis.'],
            ];
        }

        // 3. Kalkulasi biaya
        $salePrice = (float)($product['sale_price'] ?? 0);
        $shippingFee = self::SHIPPING_FEE;
        $adminFee = self::ESCROW_ADMIN_FEE;
        $totalAmount = $salePrice + $shippingFee + $adminFee;

        $fullAddress = trim($postData['shipping_address'] ?? '') . ', ' . trim($postData['city'] ?? '');
        if (!empty($postData['notes'])) {
            $fullAddress .= ' (Catatan: ' . trim($postData['notes']) . ')';
        }

        $orderData = [
            'user_id' => $userId,
            'subtotal' => $salePrice + $shippingFee,
            'admin_fee' => $adminFee,
            'total_amount' => $totalAmount,
            'order_status' => 'pending',
            'shipping_address' => $fullAddress,
        ];

        $itemData = [
            'product_id' => (int)$product['id'],
            'quantity' => 1,
            'unit_price' => $salePrice,
            'subtotal' => $salePrice,
        ];

        $paymentData = [
            'payment_method' => $postData['payment_method'] ?? 'escrow',
            'payment_status' => 'pending',
        ];

        try {
            $orderId = $this->salesRepo->createSalesOrderWithTransaction($orderData, $itemData, $paymentData);

            return [
                'success' => true,
                'order_id' => $orderId,
                'order_number' => '#BUY-' . str_pad((string)$orderId, 5, '0', STR_PAD_LEFT),
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'errors' => ['database' => 'Gagal memproses pesanan pembelian: ' . $e->getMessage()],
            ];
        }
    }
}
