<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * In-app notification centre shared by admin, teacher and student (each user only ever sees their own).
 * Routes live under /notifications for every role; the layout/sidebar around the page is the user's own.
 */
class NotificationController extends Controller
{
    public const CATEGORIES = [
        'announcement' => 'Announcements',
        'attendance'   => 'Attendance',
        'exam'         => 'Exams',
        'fee'          => 'Fees',
        'leave'        => 'Leave',
        'salary'       => 'Salary',
        'admission'    => 'Admissions',
        'certificate'  => 'Certificates',
        'hostel'       => 'Hostel',
        'transport'    => 'Transport',
        'account'      => 'Account',
        'general'      => 'Other',
    ];

    public function index(Request $request)
    {
        $user   = $request->user();
        $filter = $request->query('filter') === 'unread' ? 'unread' : 'all';

        $category = (string) $request->query('category', '');
        if (! isset(self::CATEGORIES[$category])) {
            $category = null;
        }

        $query = $filter === 'unread' ? $user->unreadNotifications() : $user->notifications();
        if ($category) {
            $query->where('data->category', $category);
        }

        return view('notifications.index', [
            'notifications' => $query->paginate(15)->withQueryString(),
            'unreadCount'   => $user->unreadNotifications()->count(),
            'categories'    => self::CATEGORIES,
            'filter'        => $filter,
            'category'      => $category,
        ]);
    }

    /** JSON for the header bell (latest 8 + unread count). */
    public function feed(Request $request)
    {
        $user = $request->user();

        $items = $user->notifications()->limit(8)->get()->map(fn ($n) => [
            'id'       => $n->id,
            'title'    => $n->data['title'] ?? 'Notification',
            'message'  => $n->data['message'] ?? '',
            'level'    => $n->data['level'] ?? 'info',
            'read'     => $n->read_at !== null,
            'time'     => $n->created_at?->diffForHumans(),
            'open_url' => route('notifications.open', $n->id),
        ]);

        return response()->json([
            'unread' => $user->unreadNotifications()->count(),
            'items'  => $items,
        ]);
    }

    /** Mark as read, then go to the page the notification points at. */
    public function open(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;
        $safe = is_string($url) && str_starts_with($url, '/') && ! str_starts_with($url, '//') && ! str_contains($url, '\\');

        return redirect($safe ? $url : route('notifications.index'));
    }

    public function read(Request $request, string $id)
    {
        $request->user()->notifications()->findOrFail($id)->markAsRead();

        return back();
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Request $request, string $id)
    {
        $request->user()->notifications()->findOrFail($id)->delete();

        return back();
    }

    public function clearRead(Request $request)
    {
        $request->user()->readNotifications()->delete();

        return back()->with('success', 'Read notifications cleared.');
    }
}