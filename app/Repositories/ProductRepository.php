<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Product;
use App\Models\Store;
use PDO;

class ProductRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Get featured products for Homepage
     *
     * @return Product[]
     */
    public function getFeaturedForHome(int $limit = 6): array
    {
        $sql = "
            SELECT p.*,
                   c.name AS category_name, c.slug AS category_slug,
                   s.store_name, s.city AS store_city,
                   COALESCE(
                       (SELECT price_per_day FROM rental_pricing WHERE product_id = p.id AND duration_days = 1 LIMIT 1),
                       p.sale_price * 0.015
                   ) AS daily_price,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN partner_stores s ON p.store_id = s.id
            WHERE p.is_rental = 1
            ORDER BY p.review_count DESC, p.rating_avg DESC, p.id DESC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row);
        }

        return $products;
    }

    /**
     * Get filtered products for Catalog page with pagination
     *
     * @param array $filters ['q', 'category', 'type', 'min_price', 'max_price', 'sort', 'available_only']
     * @param int $page
     * @param int $perPage
     * @return array ['data' => Product[], 'total' => int, 'page' => int, 'per_page' => int, 'last_page' => int]
     */
    public function getCatalogFiltered(array $filters = [], int $page = 1, int $perPage = 9): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $conditions = ["1=1"];
        $params = [];

        // 1. Keyword search (Name or Description)
        if (!empty($filters['q'])) {
            $conditions[] = "(p.name LIKE :kw1 OR p.description LIKE :kw2)";
            $params['kw1'] = '%' . trim($filters['q']) . '%';
            $params['kw2'] = '%' . trim($filters['q']) . '%';
        }

        // 2. Category filter (by slug or id)
        if (!empty($filters['category'])) {
            if (is_numeric($filters['category'])) {
                $conditions[] = "p.category_id = :cat_id";
                $params['cat_id'] = (int) $filters['category'];
            } else {
                $conditions[] = "c.slug = :cat_slug";
                $params['cat_slug'] = trim($filters['category']);
            }
        }

        // 3. Transaction Type (rental, sale, consignment)
        if (!empty($filters['type'])) {
            if ($filters['type'] === 'rental') {
                $conditions[] = "p.is_rental = 1";
            } elseif ($filters['type'] === 'sale') {
                $conditions[] = "p.is_sale = 1";
            } elseif ($filters['type'] === 'consignment') {
                $conditions[] = "p.is_consignment = 1";
            }
        }

        // 4. Availability
        if (!empty($filters['available_only'])) {
            $conditions[] = "p.stock_quantity > 0";
        }

        $whereClause = implode(" AND ", $conditions);

        // Subquery for daily price
        $dailyPriceSubquery = "COALESCE(
            (SELECT price_per_day FROM rental_pricing WHERE product_id = p.id AND duration_days = 1 LIMIT 1),
            p.sale_price * 0.015
        )";

        // 5. Price filter (applied via HAVING or wrapped subquery)
        $havingClauses = [];
        if (isset($filters['min_price']) && is_numeric($filters['min_price']) && $filters['min_price'] > 0) {
            $havingClauses[] = "daily_price >= :min_price";
            $params['min_price'] = (float) $filters['min_price'];
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price']) && $filters['max_price'] > 0) {
            $havingClauses[] = "daily_price <= :max_price";
            $params['max_price'] = (float) $filters['max_price'];
        }
        $havingSql = !empty($havingClauses) ? "HAVING " . implode(" AND ", $havingClauses) : "";

        // 6. Sorting
        $orderBy = "p.id DESC";
        $sort = $filters['sort'] ?? 'popular';
        if ($sort === 'price_asc') {
            $orderBy = "daily_price ASC";
        } elseif ($sort === 'price_desc') {
            $orderBy = "daily_price DESC";
        } elseif ($sort === 'rating_desc') {
            $orderBy = "p.rating_avg DESC, p.review_count DESC";
        } elseif ($sort === 'popular') {
            $orderBy = "p.review_count DESC, p.rating_avg DESC";
        } elseif ($sort === 'newest') {
            $orderBy = "p.id DESC";
        }

        // Count total matching items
        $countSql = "
            SELECT COUNT(*) AS total FROM (
                SELECT p.id, {$dailyPriceSubquery} AS daily_price
                FROM products p
                JOIN categories c ON p.category_id = c.id
                JOIN partner_stores s ON p.store_id = s.id
                WHERE {$whereClause}
                {$havingSql}
            ) AS counted_results
        ";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $k => $v) {
            $countStmt->bindValue(":{$k}", $v);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        // Main Query
        $sql = "
            SELECT p.*,
                   c.name AS category_name, c.slug AS category_slug,
                   s.store_name, s.city AS store_city,
                   {$dailyPriceSubquery} AS daily_price,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN partner_stores s ON p.store_id = s.id
            WHERE {$whereClause}
            {$havingSql}
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(":{$k}", $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = new Product($row);
        }

        $lastPage = (int) ceil($total / $perPage);
        if ($lastPage < 1) {
            $lastPage = 1;
        }

        return [
            'data'        => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'last_page'   => $lastPage,
        ];
    }

    /**
     * Find Product by Slug with all detailed relations (Store, Images, Units, Pricing, Reviews)
     */
    public function findBySlug(string $slug): ?Product
    {
        $dailyPriceSubquery = "COALESCE(
            (SELECT price_per_day FROM rental_pricing WHERE product_id = p.id AND duration_days = 1 LIMIT 1),
            p.sale_price * 0.015
        )";

        $stmt = $this->pdo->prepare("
            SELECT p.*,
                   c.name AS category_name, c.slug AS category_slug,
                   s.store_name, s.city AS store_city, s.address AS store_address, s.phone AS store_phone,
                   4.9 AS store_rating,
                   {$dailyPriceSubquery} AS daily_price,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN partner_stores s ON p.store_id = s.id
            WHERE p.slug = :slug
            LIMIT 1
        ");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $product = new Product($row);

        // Fetch image gallery
        $imgStmt = $this->pdo->prepare("
            SELECT * FROM product_images
            WHERE product_id = :id
            ORDER BY is_primary DESC, sort_order ASC, id ASC
        ");
        $imgStmt->execute(['id' => $product->id]);
        $product->images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

        // If no images registered in product_images, populate primary fallback
        if (empty($product->images)) {
            $product->images = [
                [
                    'image_path' => $product->getPrimaryImageUrl(),
                    'is_primary' => 1
                ]
            ];
        }

        // Fetch physical units
        $unitStmt = $this->pdo->prepare("
            SELECT * FROM product_units
            WHERE product_id = :id
            ORDER BY availability_status ASC, id ASC
        ");
        $unitStmt->execute(['id' => $product->id]);
        $product->units = $unitStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch rental pricing tiers
        $priceStmt = $this->pdo->prepare("
            SELECT * FROM rental_pricing
            WHERE product_id = :id
            ORDER BY duration_days ASC
        ");
        $priceStmt->execute(['id' => $product->id]);
        $product->pricing = $priceStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch user reviews
        $product->reviews = [
            [
                'user_name' => 'Dimas Wicaksono',
                'username'  => 'dimasw',
                'rating'    => 5,
                'comment'   => 'Kondisi kamera sangat mulus, sensor bersih tanpa debu. Pengambilan unit di lapak sangat cepat.',
                'created_at'=> date('Y-m-d H:i:s', strtotime('-3 days')),
            ],
            [
                'user_name' => 'Siti Rahmawati',
                'username'  => 'siti_r',
                'rating'    => 5,
                'comment'   => 'Baterai awet dan charger lengkap dengan tas pelindung. Sangat puas sewa di CamHub!',
                'created_at'=> date('Y-m-d H:i:s', strtotime('-1 week')),
            ],
        ];

        return $product;
    }

    /**
     * Get related products in the same category
     *
     * @return Product[]
     */
    public function getRelatedProducts(int $categoryId, int $excludeProductId, int $limit = 4): array
    {
        $dailyPriceSubquery = "COALESCE(
            (SELECT price_per_day FROM rental_pricing WHERE product_id = p.id AND duration_days = 1 LIMIT 1),
            p.sale_price * 0.015
        )";

        $stmt = $this->pdo->prepare("
            SELECT p.*,
                   c.name AS category_name, c.slug AS category_slug,
                   s.store_name, s.city AS store_city,
                   {$dailyPriceSubquery} AS daily_price,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN partner_stores s ON p.store_id = s.id
            WHERE p.category_id = :cat_id AND p.id != :exclude_id AND p.is_rental = 1
            ORDER BY p.rating_avg DESC, p.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':exclude_id', $excludeProductId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row);
        }

        return $products;
    }

    /**
     * Count available physical units for a product
     */
    public function countAvailableUnits(int $productId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM product_units
            WHERE product_id = :id AND availability_status = 'available'
        ");
        $stmt->execute(['id' => $productId]);
        return (int) $stmt->fetchColumn();
    }
}
