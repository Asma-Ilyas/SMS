<?php

namespace App\Services\Notifications;

use App\Models\Announcement;
use App\Notifications\PortalNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

/**
 * The single entry point for sending portal notifications:
 *
 *   app(PortalNotifier::class)->send($user, 'Title', 'Message', ['category' => 'fee', 'level' => 'warning', 'url' => '/student/fees']);
 *   app(PortalNotifier::class)->send($users, ..., ['key' => 'fee-due-12', 'dedupe_days' => 30]);   // only once per user
 */
class PortalNotifier
{
    public function __construct(protected RecipientResolver $recipients)
    {
    }

    /** In-app path for a named route (relative, so it survives an APP_URL change). Null if the route does not exist. */
    public function path(string $route, array $params = []): ?string
    {
        return Route::has($route) ? route($route, $params, false) : null;
    }

    /**
     * @param  Model|iterable  $users
     * @param  array{category?:string, level?:string, url?:?string, extra?:array, key?:string, dedupe_days?:int}  $opts
     * @return int  number of users notified
     */
    public function send($users, string $title, string $message, array $opts = []): int
    {
        $users = collect($users instanceof Model ? [$users] : $users)
            ->filter()
            ->unique(fn ($u) => $u->getKey())
            ->values();

        if ($users->isEmpty()) {
            return 0;
        }

        $extra = $opts['extra'] ?? [];

        if (! empty($opts['key'])) {
            $extra['key'] = $opts['key'];
            $users        = $this->withoutRecentDuplicates($users, $opts['key'], (int) ($opts['dedupe_days'] ?? 1));
            if ($users->isEmpty()) {
                return 0;
            }
        }

        $notification = new PortalNotification(
            $title,
            $message,
            $opts['category'] ?? 'general',
            $opts['level'] ?? 'info',
            $opts['url'] ?? null,
            $extra
        );

        foreach ($users->chunk(200) as $chunk) {
            Notification::send($chunk->all(), $notification);
        }

        return $users->count();
    }

    protected function withoutRecentDuplicates(Collection $users, string $key, int $days): Collection
    {
        $already = DatabaseNotification::query()
            ->where('notifiable_type', $users->first()->getMorphClass())
            ->whereIn('notifiable_id', $users->map(fn ($u) => $u->getKey())->all())
            ->where('data->key', $key)
            ->where('created_at', '>=', now()->subDays(max($days, 1)))
            ->pluck('notifiable_id')
            ->all();

        return $users->reject(fn ($u) => in_array($u->getKey(), $already))->values();
    }

    /* ---------- announcements ---------- */

    public function audienceUsers(Announcement $a): Collection
    {
        return match ($a->audience) {
            'admins'   => $this->recipients->admins(),
            'teachers' => $this->recipients->teachers(),
            'students' => $this->recipients->students(),
            'section'  => $this->recipients->usersForStudents($this->recipients->studentsForScope($a->class_section_id)),
            default    => $this->recipients->everyone(),
        };
    }

    public function deliverAnnouncement(Announcement $a): int
    {
        $users = $this->audienceUsers($a);

        if ($a->created_by) {
            $users = $users->reject(fn ($u) => $u->getKey() == $a->created_by);
        }

        $count = $this->send($users, $a->title, $a->message, [
            'category' => 'announcement',
            'level'    => ['urgent' => 'danger', 'important' => 'warning'][$a->priority] ?? 'info',
            'url'      => $a->link,
            'extra'    => ['announcement_id' => $a->id, 'priority' => $a->priority],
        ]);

        $a->forceFill(['recipients_count' => $count])->save();

        return $count;
    }
}