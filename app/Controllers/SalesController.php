<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\SalesRepository;
use App\Services\SalesService;

class SalesController
{
    private SalesService $salesService;
    private SalesRepository $salesRepo;

    public function __construct(?SalesService $salesService = null, ?SalesRepository $salesRepo = null)
    {
        $this->salesService = $salesService ?? new SalesService();
        $this->salesRepo = $salesRepo ?? new SalesRepository();
    }

    /**
     * Halaman Formulir Checkout Pembelian Kamera (2 Kolom Presisi Prototype)
     */
    public function checkout(string $slug): string
    {
        $user = auth();
        if (!$user) {
            flash('error', 'Silakan masuk ke akun Anda terlebih dahulu untuk membeli kamera.');
            redirect('/login');
            return '';
        }

        $userId = (int)$user['id'];
        $checkoutData = $this->salesService->prepareCheckout($userId, $slug);

        if (!$checkoutData) {
            flash('error', 'Kamera yang Anda pilih tidak tersedia untuk dijual atau stok telah habis.');
            redirect('/katalog');
            return '';
        }

        return view('sales.checkout', [
            'product' => $checkoutData['product'],
            'user' => $checkoutData['user'],
            'profile' => $checkoutData['profile'],
            'pricing' => $checkoutData['pricing'],
            'prefill' => $checkoutData['prefill'],
            'errors' => flash('errors') ?? [],
            'old' => flash('old_input') ?? [],
        ]);
    }

    /**
     * Memproses Pengiriman Formulir Checkout Pembelian Kamera
     */
    public function processCheckout(string $slug): void
    {
        $user = auth();
        if (!$user) {
            flash('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            redirect('/login');
            return;
        }

        $userId = (int)$user['id'];
        $result = $this->salesService->processCheckout($userId, $slug, $_POST);

        if (!$result['success']) {
            flash('errors', $result['errors'] ?? []);
            flash('old_input', $_POST);
            redirect("/beli/{$slug}");
            return;
        }

        flash('success', 'Pesanan pembelian kamera berhasil dibuat! Dana Anda diamankan di CamHub Escrow.');
        redirect('/pesanan/beli/' . $result['order_id'] . '/sukses');
    }

    /**
     * Halaman Nota Konfirmasi Pembelian Kamera (#BUY-XXXXX)
     */
    public function success(int $id): string
    {
        $user = auth();
        $userId = (int)($user['id'] ?? 0);

        $order = $this->salesRepo->findOrderById($id, $userId);

        if (!$order) {
            flash('error', 'Pesanan pembelian tidak ditemukan.');
            redirect('/pesanan-saya?type=sale');
            return '';
        }

        return view('sales.success', [
            'order' => $order,
        ]);
    }

    /**
     * Halaman Cetak Invoice Pembelian Kamera Resmi (Print-to-PDF)
     */
    public function printInvoice(int $id): string
    {
        $user = auth();
        if (!$user) {
            redirect('/login');
            return '';
        }

        $userId = (int) $user['id'];
        $userRole = (string) ($user['role'] ?? 'customer');

        // Admin dapat melihat seluruh invoice, customer hanya miliknya
        $order = $this->salesRepo->findOrderById($id, $userRole === 'admin' ? null : $userId);

        if (!$order) {
            flash('error', 'Dokumen invoice pembelian tidak ditemukan.');
            redirect('/pesanan-saya?type=sale');
            return '';
        }

        return view('sales.invoice_print', [
            'order' => $order,
        ]);
    }

    /**
     * Halaman Daftar Jual Beli Saya (Pembelian Kamera Pelanggan)
     */
    public function customerPurchases(): string
    {
        $user = auth();
        if (!$user) {
            redirect('/login');
            return '';
        }

        $userId = (int)$user['id'];
        $orders = $this->salesRepo->getUserSalesOrders($userId);

        return view('customer.orders', [
            'purchases' => $orders,
            'user' => $user,
            'activeType' => 'sale',
        ]);
    }
}
