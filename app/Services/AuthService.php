<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Repositories\UserRepository;
use PDO;
use Throwable;

class AuthService
{
    private UserRepository $userRepo;
    private PDO $pdo;

    public function __construct(?UserRepository $userRepo = null, ?PDO $pdo = null)
    {
        $this->userRepo = $userRepo ?? new UserRepository();
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Attempt login with identifier (username, email, or phone) and password
     */
    public function attempt(string $identifier, string $password): array
    {
        $identifier = trim($identifier);

        if ($identifier === '' || $password === '') {
            return [
                'success' => false,
                'message' => 'Silakan isi username/email/no. HP dan password.',
            ];
        }

        $user = $this->userRepo->findByIdentifier($identifier);

        if (!$user) {
            $this->logAudit(null, 'LOGIN_FAILED', 'users', null, ['identifier' => $identifier, 'reason' => 'User not found']);
            return [
                'success' => false,
                'message' => 'Kredensial tidak ditemukan. Periksa kembali username/email/no. HP Anda.',
            ];
        }

        if ($user['status'] !== 'active') {
            $this->logAudit((int)$user['id'], 'LOGIN_BLOCKED', 'users', (int)$user['id'], ['status' => $user['status']]);
            return [
                'success' => false,
                'message' => 'Akun Anda sedang dinonaktifkan atau ditangguhkan. Silakan hubungi admin.',
            ];
        }

        if (!password_verify($password, $user['password_hash'])) {
            $this->logAudit((int)$user['id'], 'LOGIN_FAILED', 'users', (int)$user['id'], ['reason' => 'Invalid password']);
            return [
                'success' => false,
                'message' => 'Password yang Anda masukkan salah.',
            ];
        }

        // Authentication Success: Regenerate session to prevent fixation
        session_start_safe();
        if (!headers_sent()) {
            session_regenerate_id(true);
        }

        $sessionPayload = [
            'id' => (int)$user['id'],
            'role_id' => (int)$user['role_id'],
            'role_name' => $user['role_name'],
            'role_display_name' => $user['role_display_name'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
        ];

        $_SESSION['user'] = $sessionPayload;

        $this->logAudit((int)$user['id'], 'LOGIN_SUCCESS', 'users', (int)$user['id'], [
            'role' => $user['role_name'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        ]);

        return [
            'success' => true,
            'message' => 'Login berhasil! Mengalihkan ke dashboard...',
            'user' => $sessionPayload,
            'redirect' => $this->getRedirectPathByRole($user['role_name']),
        ];
    }

    /**
     * Register a new user account
     */
    public function register(array $data): array
    {
        $roleName = strtolower($data['role'] ?? 'customer');
        $roleId = $this->userRepo->getRoleIdByName($roleName);

        if (!$roleId) {
            $roleId = $this->userRepo->getRoleIdByName('customer');
            $roleName = 'customer';
        }

        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        $this->pdo->beginTransaction();
        try {
            $userId = $this->userRepo->create([
                'role_id' => $roleId,
                'username' => !empty($data['username']) ? trim($data['username']) : null,
                'full_name' => trim($data['full_name']),
                'email' => strtolower(trim($data['email'])),
                'phone' => trim($data['phone']),
                'password_hash' => $passwordHash,
                'status' => 'active',
            ]);

            if ($roleName === 'customer') {
                $this->userRepo->createCustomerProfile($userId, $data['address'] ?? null, $data['city'] ?? null);
            } elseif ($roleName === 'partner') {
                $businessName = !empty($data['business_name']) ? trim($data['business_name']) : $data['full_name'] . ' Camera';
                $this->userRepo->createPartner($userId, $businessName, $data['address'] ?? null, $data['city'] ?? null, $data['phone'] ?? null);
            }

            $this->logAudit($userId, 'REGISTER_SUCCESS', 'users', $userId, [
                'role' => $roleName,
                'email' => $data['email'],
            ]);

            $this->pdo->commit();

            return [
                'success' => true,
                'user_id' => $userId,
                'message' => 'Pendaftaran akun berhasil! Silakan login.',
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat mendaftar: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Log user out
     */
    public function logout(): void
    {
        session_start_safe();

        if (isset($_SESSION['user']['id'])) {
            $this->logAudit((int)$_SESSION['user']['id'], 'LOGOUT', 'users', (int)$_SESSION['user']['id']);
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies") && !headers_sent()) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Map role to target dashboard route
     */
    public function getRedirectPathByRole(string $role): string
    {
        return match (strtolower($role)) {
            'admin' => '/dashboard/admin',
            'partner' => '/dashboard/mitra',
            default => '/dashboard/pelanggan',
        };
    }

    /**
     * Record security audit log
     */
    public function logAudit(?int $userId, string $action, string $targetTable, ?int $targetId = null, ?array $changes = null): void
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO audit_logs (user_id, action, target_table, target_id, changes_json, ip_address, created_at)
                VALUES (:user_id, :action, :target_table, :target_id, :changes_json, :ip_address, NOW())
            ");

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $changesJson = $changes !== null ? json_encode($changes, JSON_UNESCAPED_UNICODE) : null;

            $stmt->execute([
                'user_id' => $userId,
                'action' => $action,
                'target_table' => $targetTable,
                'target_id' => $targetId,
                'changes_json' => $changesJson,
                'ip_address' => $ip,
            ]);
        } catch (Throwable $e) {
            // Silently fail logging if DB issue occurs so core auth is not blocked
        }
    }
}
