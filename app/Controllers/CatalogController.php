<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ProductCatalogService;

class CatalogController
{
    private ProductCatalogService $catalogService;

    public function __construct(?ProductCatalogService $catalogService = null)
    {
        $this->catalogService = $catalogService ?? new ProductCatalogService();
    }

    /**
     * Render Product Catalog Page with Dynamic Filters
     */
    public function index(): string
    {
        $data = $this->catalogService->getCatalogData($_GET);

        return view('public.catalog', [
            'pageTitle'        => 'Katalog Peralatan Kamera & Fotografi — CamHub',
            'products'         => $data['products'],
            'total'            => $data['total'],
            'page'             => $data['page'],
            'perPage'          => $data['per_page'],
            'lastPage'         => $data['last_page'],
            'categories'       => $data['categories'],
            'selectedCategory' => $data['selectedCategory'],
            'filters'          => $data['filters'],
        ]);
    }
}
