<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Category;
use PDO;

class CategoryRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Get all categories with product count
     *
     * @return Category[]
     */
    public function getAllWithCount(): array
    {
        $stmt = $this->pdo->query("
            SELECT c.*, COUNT(p.id) AS product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id
            GROUP BY c.id, c.name, c.slug, c.icon, c.created_at
            ORDER BY c.id ASC
        ");

        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category($row);
        }

        return $categories;
    }

    /**
     * Alias for getAllWithCount
     *
     * @return Category[]
     */
    public function getAllWithCounts(): array
    {
        return $this->getAllWithCount();
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category
    {
        $stmt = $this->pdo->prepare("
            SELECT c.*, COUNT(p.id) AS product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id
            WHERE c.slug = :slug
            GROUP BY c.id, c.name, c.slug, c.icon, c.created_at
            LIMIT 1
        ");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Category($row) : null;
    }

    /**
     * Find category by ID
     */
    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM categories WHERE id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Category($row) : null;
    }
}
