<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Services\Notifications\PortalNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverAnnouncement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $announcementId)
    {
    }

    public function handle(PortalNotifier $notifier): void
    {
        if ($announcement = Announcement::find($this->announcementId)) {
            $notifier->deliverAnnouncement($announcement);
        }
    }
}