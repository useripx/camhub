<?php

declare(strict_types=1);

namespace App\Models;

class Notification
{
    public int $id;
    public int $user_id;
    public string $title;
    public string $message;
    public bool $is_read;
    public ?string $action_url;
    public string $created_at;

    public function __construct(array $data = [])
    {
        $this->id = (int) ($data['id'] ?? 0);
        $this->user_id = (int) ($data['user_id'] ?? 0);
        $this->title = (string) ($data['title'] ?? '');
        $this->message = (string) ($data['message'] ?? '');
        $this->is_read = (bool) ($data['is_read'] ?? false);
        $this->action_url = $data['action_url'] ?? null;
        $this->created_at = (string) ($data['created_at'] ?? date('Y-m-d H:i:s'));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'message' => $this->message,
            'is_read' => $this->is_read ? 1 : 0,
            'action_url' => $this->action_url,
            'created_at' => $this->created_at,
        ];
    }
}
