<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Find a user by username, email, or phone number with attached role details
     */
    public function findByIdentifier(string $identifier): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT u.*, r.name AS role_name, r.display_name AS role_display_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.username = :id1 OR u.email = :id2 OR u.phone = :id3
            LIMIT 1
        ");
        $stmt->execute([
            'id1' => $identifier,
            'id2' => $identifier,
            'id3' => $identifier,
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Find a user by ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT u.*, r.name AS role_name, r.display_name AS role_display_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Find a user by email
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Find a user by username
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Find a user by phone
     */
    public function findByPhone(string $phone): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE phone = :phone LIMIT 1");
        $stmt->execute(['phone' => $phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Get role ID by its system name (admin, partner, customer)
     */
    public function getRoleIdByName(string $name): ?int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM roles WHERE name = :name LIMIT 1");
        $stmt->execute(['name' => $name]);
        $id = $stmt->fetchColumn();

        return $id !== false ? (int)$id : null;
    }

    /**
     * Create a new user record
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (role_id, username, full_name, email, phone, password_hash, status)
            VALUES (:role_id, :username, :full_name, :email, :phone, :password_hash, :status)
        ");

        $stmt->execute([
            'role_id' => $data['role_id'],
            'username' => $data['username'] ?? null,
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => $data['password_hash'],
            'status' => $data['status'] ?? 'active',
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Create customer user profile
     */
    public function createCustomerProfile(int $userId, ?string $address = null, ?string $city = null): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO user_profiles (user_id, address, city)
            VALUES (:user_id, :address, :city)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'address' => $address,
            'city' => $city,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Create partner and partner store entry
     */
    public function createPartner(int $userId, string $businessName, ?string $address = null, ?string $city = null, ?string $phone = null): int
    {
        $stmtPartner = $this->pdo->prepare("
            INSERT INTO partners (user_id, business_name, status, commission_rate, verified_at)
            VALUES (:user_id, :business_name, 'pending', 10.00, NULL)
        ");
        $stmtPartner->execute([
            'user_id' => $userId,
            'business_name' => $businessName,
        ]);
        $partnerId = (int)$this->pdo->lastInsertId();

        $stmtStore = $this->pdo->prepare("
            INSERT INTO partner_stores (partner_id, store_name, address, city, phone)
            VALUES (:partner_id, :store_name, :address, :city, :phone)
        ");
        $stmtStore->execute([
            'partner_id' => $partnerId,
            'store_name' => $businessName,
            'address' => $address ?? 'Belum diisi',
            'city' => $city ?? 'Indonesia',
            'phone' => $phone ?? '08123456789',
        ]);

        return $partnerId;
    }
}
