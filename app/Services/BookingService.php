<?php

namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Validators\BookingValidator;
use DateTime;
use Throwable;

/**
 * Service BookingService
 * 
 * Mengelola logika bisnis alur checkout pemesanan sewa kamera,
 * alokasi unit fisik, penerapan diskon durasi, dan proteksi transaksi rekening bersama escrow.
 */
class BookingService
{
    private BookingRepository $bookingRepo;
    private ProductRepository $productRepo;
    private UserRepository $userRepo;
    private RentalCalculatorService $calculatorService;
    private BookingValidator $validator;

    public function __construct(
        ?BookingRepository $bookingRepo = null,
        ?ProductRepository $productRepo = null,
        ?UserRepository $userRepo = null,
        ?RentalCalculatorService $calculatorService = null,
        ?BookingValidator $validator = null
    ) {
        $this->bookingRepo = $bookingRepo ?? new BookingRepository();
        $this->productRepo = $productRepo ?? new ProductRepository();
        $this->userRepo = $userRepo ?? new UserRepository();
        $this->calculatorService = $calculatorService ?? new RentalCalculatorService();
        $this->validator = $validator ?? new BookingValidator();
    }

    /**
     * Mempersiapkan data untuk halaman formulir checkout
     */
    public function prepareCheckout(string $slug, int $userId, int $durationDays = 3, ?string $startDate = null): ?array
    {
        $product = $this->productRepo->findBySlug($slug);
        if (!$product || empty($product['is_rental'])) {
            return null;
        }

        $user = $this->userRepo->findById($userId);
        $profile = $this->userRepo->getProfile($userId);

        if (!$startDate) {
            $startDate = date('Y-m-d', strtotime('+1 day'));
        }

        $durationDays = max(1, min(30, $durationDays));
        $endDate = date('Y-m-d', strtotime("{$startDate} + {$durationDays} days"));

        $dailyPrice = (float)($product['daily_price'] ?? 0);
        $calc = $this->calculatorService->calculate($dailyPrice, $durationDays);

        $availableUnit = $this->bookingRepo->findAvailableUnit((int)$product['id'], $startDate, $endDate);

        return [
            'product' => $product,
            'user' => $user,
            'profile' => $profile,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_days' => $durationDays,
            'daily_price' => $dailyPrice,
            'calculation' => $calc,
            'has_available_unit' => ($availableUnit !== null),
            'available_unit' => $availableUnit,
        ];
    }

    /**
     * Memproses formulir checkout dan menyimpan transaksi pemesanan
     */
    public function processCheckout(int $userId, string $slug, array $postData): array
    {
        if (!$this->validator->validate($postData)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ];
        }

        $product = $this->productRepo->findBySlug($slug);
        if (!$product || empty($product['is_rental'])) {
            return [
                'success' => false,
                'errors' => ['product' => 'Produk kamera tidak ditemukan atau tidak tersedia untuk disewa.'],
            ];
        }

        $startDate = trim($postData['start_date']);
        $durationDays = (int)($postData['duration_days'] ?? 1);
        $durationDays = max(1, min(30, $durationDays));
        $endDate = date('Y-m-d', strtotime("{$startDate} + {$durationDays} days"));

        // Alokasikan unit fisik bebas benturan
        $unit = $this->bookingRepo->findAvailableUnit((int)$product['id'], $startDate, $endDate);
        if (!$unit) {
            return [
                'success' => false,
                'errors' => ['unit' => 'Seluruh unit fisik kamera ini telah terbooking pada tanggal yang Anda pilih. Silakan pilih tanggal atau durasi lain.'],
            ];
        }

        // Kalkulasi biaya
        $dailyPrice = (float)($product['daily_price'] ?? 0);
        $calc = $this->calculatorService->calculate($dailyPrice, $durationDays);
        $subtotal = $calc['subtotal'];
        $adminFee = 5000.00;
        $totalAmount = $calc['total_price'] ?? ($subtotal + $adminFee);

        $bookingData = [
            'user_id' => $userId,
            'product_unit_id' => (int)$unit['id'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $durationDays,
            'daily_price' => $dailyPrice,
            'subtotal' => $subtotal,
            'admin_fee' => $adminFee,
            'total_amount' => $totalAmount,
            'payment_status' => 'unpaid',
            'rental_status' => 'pending',
        ];

        $paymentData = [
            'payment_method' => $postData['payment_method'] ?? 'escrow',
            'payment_status' => 'pending',
        ];

        try {
            $bookingId = $this->bookingRepo->createBookingWithTransaction($bookingData, $paymentData);

            return [
                'success' => true,
                'booking_id' => $bookingId,
                'booking_code' => '#RENT-' . str_pad((string)$bookingId, 5, '0', STR_PAD_LEFT),
                'allocated_serial' => $unit['serial_number'],
                'total_amount' => $totalAmount,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'errors' => ['database' => 'Gagal memproses pemesanan: ' . $e->getMessage()],
            ];
        }
    }
}

