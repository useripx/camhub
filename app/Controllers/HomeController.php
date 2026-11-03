<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ProductCatalogService;

class HomeController
{
    private ProductCatalogService $catalogService;

    public function __construct(?ProductCatalogService $catalogService = null)
    {
        $this->catalogService = $catalogService ?? new ProductCatalogService();
    }

    /**
     * Render CamHub Homepage (Beranda)
     */
    public function index(): string
    {
        $data = $this->catalogService->getHomeData();

        return view('public.home', [
            'pageTitle'        => 'CamHub — Platform Sewa, Jual, Beli & Titip Kamera Terpercaya',
            'categories'       => $data['categories'],
            'featuredProducts' => $data['featuredProducts'],
        ]);
    }
}
