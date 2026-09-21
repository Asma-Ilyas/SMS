<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Generic in-app notification. Stores the same keys your other
 * notifications use (title, message, category, level, url) so it shows
 * up on the /notifications page like the rest.
 */
class AdminAlert extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
        public string $url,
        public string $category = 'general',
        public string $level = 'info',
    ) {}

    public function via($notifiable): array
    {
        // 'database' only. Do not add ShouldQueue unless a queue worker is running.
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'    => $this->title,
            'message'  => $this->message,
            'category' => $this->category,
            'level'    => $this->level,
            'url'      => $this->url,
        ];
    }
}