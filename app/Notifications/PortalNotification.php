<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * One notification class for every portal event. Stored in Laravel's `notifications` table.
 *
 * category: announcement | attendance | exam | fee | leave | salary | admission | certificate | transport | hostel | account | general
 * level:    info | success | warning | danger
 */
class PortalNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
        public string $category = 'general',
        public string $level = 'info',
        public ?string $url = null,          // in-app path such as /student/fees
        public array $extra = [],
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (config('portal-notifications.mail') && ! empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'    => $this->title,
            'message'  => $this->message,
            'category' => $this->category,
            'level'    => $this->level,
            'url'      => $this->url,
        ] + $this->extra;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)->subject($this->title)->line($this->message);

        if ($this->url) {
            $mail->action('Open', url($this->url));
        }

        return $mail;
    }
}