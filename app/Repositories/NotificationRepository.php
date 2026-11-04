<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class NotificationRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function getUserNotifications(int $userId, int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT id, user_id, title, message, is_read, action_url, created_at
            FROM notifications
            WHERE user_id = :user_id
            ORDER BY created_at DESC, id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM notifications 
            WHERE user_id = :user_id AND is_read = 0
        ");
        $stmt->execute(['user_id' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    public function create(int $userId, string $title, string $message, ?string $actionUrl = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, title, message, is_read, action_url, created_at)
            VALUES (:user_id, :title, :message, 0, :action_url, NOW())
        ");
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function markAsRead(int $notificationId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE notifications 
            SET is_read = 1 
            WHERE id = :id AND user_id = :user_id
        ");

        return $stmt->execute([
            'id' => $notificationId,
            'user_id' => $userId,
        ]);
    }

    public function markAllAsRead(int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE notifications 
            SET is_read = 1 
            WHERE user_id = :user_id AND is_read = 0
        ");

        return $stmt->execute(['user_id' => $userId]);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, user_id, title, message, is_read, action_url, created_at
            FROM notifications
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}
