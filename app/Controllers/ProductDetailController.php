<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ProductCatalogService;
use App\Services\RentalCalculatorService;

class ProductDetailController
{
    private ProductCatalogService $catalogService;
    private RentalCalculatorService $calculator;

    public function __construct(
        ?ProductCatalogService $catalogService = null,
        ?RentalCalculatorService $calculator = null
    ) {
        $this->catalogService = $catalogService ?? new ProductCatalogService();
        $this->calculator = $calculator ?? new RentalCalculatorService();
    }

    /**
     * Render Product Detail Page
     */
    public function show(string $slug): string
    {
        $data = $this->catalogService->getProductDetail($slug);

        if (!$data) {
            http_response_code(404);
            return "<div style='font-family:sans-serif; text-align:center; padding:80px;'>
                <h2>404 | Produk Kamera Tidak Ditemukan</h2>
                <p>Produk dengan tautan '{$slug}' tidak tersedia di katalog CamHub.</p>
                <a href='" . base_url('katalog') . "' style='color:#2563eb;'>Kembali ke Katalog Produk</a>
            </div>";
        }

        return view('public.product_detail', [
            'pageTitle'           => $data['product']->name . ' — Detail Sewa & Beli di CamHub',
            'product'             => $data['product'],
            'relatedProducts'     => $data['relatedProducts'],
            'availableUnitsCount' => $data['availableUnitsCount'],
            'rentalEstimates'     => $data['rentalEstimates'],
        ]);
    }

    /**
     * AJAX endpoint for live rental calculation
     */
    public function calculateEstimate(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $dailyPrice = isset($_POST['daily_price']) ? (float)$_POST['daily_price'] : 0.0;
        $durationDays = isset($_POST['duration_days']) ? (int)$_POST['duration_days'] : 1;

        if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
            $durationDays = $this->calculator->calculateDaysBetween((string)$_POST['start_date'], (string)$_POST['end_date']);
        }

        $estimate = $this->calculator->calculate($dailyPrice, $durationDays);

        echo json_encode([
            'status' => 'success',
            'data'   => $estimate,
        ]);
        exit;
    }
}
