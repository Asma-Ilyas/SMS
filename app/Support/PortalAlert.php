<?php

namespace App\Support;

use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use App\Notifications\AdminAlert;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

/**
 * One place to send in-app notifications.
 * Every method is safe: a failure is logged and never breaks the page.
 *
 * Usage:
 *   PortalAlert::toAdmins('Title', 'Message', route('admin.leaves.index'), 'leave');
 *   PortalAlert::toStudent($studentId, 'Title', 'Message', PortalAlert::link('student.fees.index'));
 */
class PortalAlert
{
    /** Every user with the admin role. */
    public static function toAdmins(string $title, string $message, string $url, string $category = 'general', string $level = 'info'): void
    {
        self::send(User::role('admin')->get(), $title, $message, $url, $category, $level);
    }

    /** One student (or several) by students.id. */
    public static function toStudent(int|array $studentIds, string $title, string $message, string $url, string $category = 'general', string $level = 'info'): void
    {
        self::send(self::studentUsers((array) $studentIds), $title, $message, $url, $category, $level);
    }

    /** Every student in one class section. */
    public static function toSection(int $classSectionId, string $title, string $message, string $url, string $category = 'general', string $level = 'info'): void
    {
        $ids = Student::where('class_section_id', $classSectionId)->pluck('id')->all();
        self::send(self::studentUsers($ids), $title, $message, $url, $category, $level);
    }

    /** One staff member (or several) by staff.id. */
    public static function toStaff(int|array $staffIds, string $title, string $message, string $url, string $category = 'general', string $level = 'info'): void
    {
        self::send(self::staffUsers((array) $staffIds), $title, $message, $url, $category, $level);
    }

    /**
     * Build a link from a route name, or fall back to a plain path
     * (or the notifications page) if that route does not exist.
     */
    public static function link(string $routeName, ?string $fallbackPath = null, array $params = []): string
    {
        if (Route::has($routeName)) {
            return route($routeName, $params);
        }

        return url($fallbackPath ?? '/notifications');
    }

    // ------------------------------------------------------------------

    private static function studentUsers(array $studentIds): Collection
    {
        if (empty($studentIds)) {
            return collect();
        }

        $students = Student::whereIn('id', $studentIds)->get(['id', 'user_id', 'email']);
        $userIds  = $students->pluck('user_id')->filter()->all();
        $emails   = $students->whereNull('user_id')->pluck('email')->filter()->all();

        return User::where(function ($q) use ($userIds, $emails) {
            $q->whereIn('id', $userIds)->orWhereIn('email', $emails);
        })->get();
    }

    private static function staffUsers(array $staffIds): Collection
    {
        if (empty($staffIds)) {
            return collect();
        }

        $emails = Staff::whereIn('id', $staffIds)->pluck('email')->filter()->all();

        return User::whereIn('email', $emails)->get();
    }

    private static function send(Collection $users, string $title, string $message, string $url, string $category, string $level): void
    {
        try {
            if ($users->isEmpty()) {
                logger()->warning("Notification skipped (no recipients): {$title}");
                return;
            }

            Notification::send($users, new AdminAlert($title, $message, $url, $category, $level));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}