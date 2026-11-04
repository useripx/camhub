<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\NotificationService;

class NotificationController
{
    private NotificationService $service;

    public function __construct(?NotificationService $service = null)
    {
        $this->service = $service ?? new NotificationService();
    }

    public function index(): string
    {
        $user = auth();
        if (!$user) {
            redirect('/login');
            return '';
        }

        $userId = (int) $user['id'];
        $notifications = $this->service->getUserNotifications($userId, 50);
        $unreadCount = $this->service->getUnreadCount($userId);

        return view('customer.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'user' => $user
        ]);
    }

    public function markAsRead(int $id): void
    {
        $user = auth();
        if (!$user) {
            redirect('/login');
            return;
        }

        $this->service->markAsRead($id, (int) $user['id']);

        // Check if there is an action_url to redirect to
        $target = $_POST['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? '/notifikasi';
        redirect($target);
    }

    public function markAllAsRead(): void
    {
        $user = auth();
        if (!$user) {
            redirect('/login');
            return;
        }

        $this->service->markAllAsRead((int) $user['id']);
        set_flash('success', 'Seluruh notifikasi telah ditandai sudah dibaca.');

        $target = $_POST['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? '/notifikasi';
        redirect($target);
    }

    public function apiUnreadCount(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $user = auth();
        if (!$user) {
            echo json_encode(['unread' => 0]);
            return;
        }

        $count = $this->service->getUnreadCount((int) $user['id']);
        echo json_encode(['unread' => $count]);
    }
}
