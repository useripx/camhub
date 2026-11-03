<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Database Connection Manager (Singleton Pattern)
 * 
 * Provides centralized, secure PDO connection for CamHub.
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
    }

    /**
     * Get the singleton PDO connection instance
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $default = $config['default'] ?? 'mysql';
            $conn = $config['connections'][$default] ?? [];

            $host = $conn['host'] ?? '127.0.0.1';
            $port = $conn['port'] ?? 3306;
            $dbname = $conn['database'] ?? 'camhub_db';
            $charset = $conn['charset'] ?? 'utf8mb4';
            $user = $conn['username'] ?? 'root';
            $pass = $conn['password'] ?? '';
            $options = $conn['options'] ?? [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Log error safely without exposing credentials
                error_log("Database connection error: " . $e->getMessage());
                throw new RuntimeException("Koneksi basis data gagal: " . $e->getMessage(), (int)$e->getCode(), $e);
            }
        }

        return self::$instance;
    }

    /**
     * Execute a closure inside a database transaction
     */
    public static function transaction(callable $callback): mixed
    {
        $pdo = self::getConnection();
        $pdo->beginTransaction();

        try {
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Reset instance (useful for test suites)
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}

