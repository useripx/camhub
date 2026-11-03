<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;

class ProductCatalogService
{
    private ProductRepository $productRepo;
    private CategoryRepository $categoryRepo;
    private RentalCalculatorService $calculator;

    public function __construct(
        ?ProductRepository $productRepo = null,
        ?CategoryRepository $categoryRepo = null,
        ?RentalCalculatorService $calculator = null
    ) {
        $this->productRepo = $productRepo ?? new ProductRepository();
        $this->categoryRepo = $categoryRepo ?? new CategoryRepository();
        $this->calculator = $calculator ?? new RentalCalculatorService();
    }

    /**
     * Get data for Homepage
     */
    public function getHomeData(): array
    {
        $categories = $this->categoryRepo->getAllWithCount();
        $featuredProducts = $this->productRepo->getFeaturedForHome(6);

        return [
            'categories'       => $categories,
            'featuredProducts' => $featuredProducts,
        ];
    }

    /**
     * Get data for Catalog page with sanitized query parameters
     */
    public function getCatalogData(array $queryParams): array
    {
        $filters = [
            'q'              => isset($queryParams['q']) ? htmlspecialchars(trim((string)$queryParams['q']), ENT_QUOTES, 'UTF-8') : '',
            'category'       => isset($queryParams['category']) ? trim((string)$queryParams['category']) : '',
            'type'           => isset($queryParams['type']) ? trim((string)$queryParams['type']) : '',
            'min_price'      => isset($queryParams['min_price']) && is_numeric($queryParams['min_price']) ? (float)$queryParams['min_price'] : null,
            'max_price'      => isset($queryParams['max_price']) && is_numeric($queryParams['max_price']) ? (float)$queryParams['max_price'] : null,
            'available_only' => !empty($queryParams['available_only']),
            'sort'           => isset($queryParams['sort']) ? trim((string)$queryParams['sort']) : 'popular',
        ];

        $page = isset($queryParams['page']) && is_numeric($queryParams['page']) ? max(1, (int)$queryParams['page']) : 1;
        $perPage = 9;

        $catalogResult = $this->productRepo->getCatalogFiltered($filters, $page, $perPage);
        $categories = $this->categoryRepo->getAllWithCount();

        // Selected category model if filtered
        $selectedCategory = null;
        if (!empty($filters['category'])) {
            $selectedCategory = is_numeric($filters['category'])
                ? $this->categoryRepo->findById((int)$filters['category'])
                : $this->categoryRepo->findBySlug($filters['category']);
        }

        return [
            'products'         => $catalogResult['data'],
            'total'            => $catalogResult['total'],
            'page'             => $catalogResult['page'],
            'per_page'         => $catalogResult['per_page'],
            'last_page'        => $catalogResult['last_page'],
            'categories'       => $categories,
            'selectedCategory' => $selectedCategory,
            'filters'          => $filters,
        ];
    }

    /**
     * Get data for Product Detail page
     */
    public function getProductDetail(string $slug): ?array
    {
        $product = $this->productRepo->findBySlug($slug);
        if (!$product) {
            return null;
        }

        $relatedProducts = $this->productRepo->getRelatedProducts($product->category_id, $product->id, 4);
        $availableUnitsCount = $this->productRepo->countAvailableUnits($product->id);

        // Precompute quick rental estimates (1 day, 3 days, 7 days, 14 days)
        $dailyRate = $product->daily_price ?? 150000.0;
        $estimates = [
            1  => $this->calculator->calculate($dailyRate, 1),
            3  => $this->calculator->calculate($dailyRate, 3),
            7  => $this->calculator->calculate($dailyRate, 7),
            14 => $this->calculator->calculate($dailyRate, 14),
        ];

        return [
            'product'             => $product,
            'relatedProducts'     => $relatedProducts,
            'availableUnitsCount' => $availableUnitsCount,
            'rentalEstimates'     => $estimates,
        ];
    }
}
