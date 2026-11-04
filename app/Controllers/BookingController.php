<?php

namespace App\Controllers;

use App\Services\BookingService;
use App\Repositories\BookingRepository;

/**
 * Controller BookingController
 * 
 * Mengelola alur pemesanan sewa kamera (Booking & Checkout),
 * nota keberhasilan transaksi escrow, dan riwayat pesanan sewa pelanggan.
 */
class BookingController
{
    private BookingService $bookingService;
    private BookingRepository $bookingRepo;

    public function __construct(
        ?BookingService $bookingService = null,
        ?BookingRepository $bookingRepo = null
    ) {
        $this->bookingService = $bookingService ?? new BookingService();
        $this->bookingRepo = $bookingRepo ?? new BookingRepository();
    }

    /**
     * Halaman Formulir Checkout (100% Mockup CamHub — Booking & Checkout.png)
     */
    public function checkout(string $slug): string
    {
        $userId = auth()['id'] ?? 0;
        $durationDays = isset($_GET['duration']) ? (int)$_GET['duration'] : 3; // Default 3 hari persis prototype
        $startDate = isset($_GET['start_date']) ? (string)$_GET['start_date'] : null;

        $data = $this->bookingService->prepareCheckout($slug, $userId, $durationDays, $startDate);

        if (!$data) {
            flash('error', 'Produk kamera tidak ditemukan atau tidak tersedia untuk disewa.');
            redirect('/katalog');
            return '';
        }

        return view('booking.checkout', [
            'product' => $data['product'],
            'user' => $data['user'],
            'profile' => $data['profile'],
            'startDate' => $data['start_date'],
            'endDate' => $data['end_date'],
            'durationDays' => $data['duration_days'],
            'dailyPrice' => $data['daily_price'],
            'calculation' => $data['calculation'],
            'hasAvailableUnit' => $data['has_available_unit'],
            'availableUnit' => $data['available_unit'],
        ]);
    }

    /**
     * Memproses formulir checkout pemesanan sewa
     */
    public function process(string $slug): void
    {
        verify_csrf();

        $userId = auth()['id'] ?? 0;
        $result = $this->bookingService->processCheckout($userId, $slug, $_POST);

        if (!$result['success']) {
            flash('error', implode('<br>', $result['errors']));
            $duration = (int)($_POST['duration_days'] ?? 3);
            $startDate = urlencode($_POST['start_date'] ?? '');
            redirect("/checkout/{$slug}?duration={$duration}&start_date={$startDate}");
            return;
        }

        flash('success', 'Pemesanan sewa kamera berhasil dibuat! Silakan lakukan pembayaran rekening bersama.');
        redirect('/booking/' . $result['booking_id'] . '/sukses');
    }

    /**
     * Halaman Nota Konfirmasi Pemesanan (Booking Receipt)
     */
    public function success(int $id): string
    {
        $userId = auth()['id'] ?? 0;
        $booking = $this->bookingRepo->findBookingById($id, $userId);

        if (!$booking) {
            flash('error', 'Pesanan tidak ditemukan.');
            redirect('/pesanan-saya');
            return '';
        }

        return view('booking.success', [
            'booking' => $booking,
        ]);
    }

    /**
     * Halaman Riwayat Pesanan Sewa Pelanggan
     */
    public function customerOrders(): string
    {
        $userId = auth()['id'] ?? 0;
        $bookings = $this->bookingRepo->getUserBookings($userId);

        return view('customer.orders', [
            'bookings' => $bookings,
            'user' => auth(),
        ]);
    }
}
