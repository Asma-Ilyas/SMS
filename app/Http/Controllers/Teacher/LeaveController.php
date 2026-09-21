<?php

namespace App\Http\Controllers\Teacher;

use App\Models\User;
use App\Notifications\AdminAlert;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class LeaveController extends BaseTeacherController
{
    public function index(Request $request)
    {
        $t = $this->teacher();
        $year = (int) $request->get('year', now()->year);

        $leaves = DB::table('leaves')->where('staff_id', $t->id)->orderByDesc('start_date')->get()
            ->map(function ($l) {
                $l->days = Carbon::parse($l->start_date)->diffInDays(Carbon::parse($l->end_date)) + 1;
                return $l;
            });

        $thisYear = $leaves->filter(fn ($l) => Carbon::parse($l->start_date)->year === $year);
        $usage = [];
        foreach (['sick', 'casual', 'annual'] as $type) {
            $usage[$type] = $thisYear->where('type', $type)->where('status', 'approved')->sum('days');
        }
        $pending = $leaves->where('status', 'pending')->count();

        return view('teacher.leaves.index', compact('leaves', 'usage', 'pending', 'year'));
    }

    public function create()
    {
        $this->teacher();
        return view('teacher.leaves.create');
    }

    public function store(Request $request)
    {
        $t = $this->teacher();

        $data = $request->validate([
            'type'       => 'required|in:sick,casual,annual',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|min:5|max:1000',
        ]);

        $overlap = DB::table('leaves')->where('staff_id', $t->id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereDate('start_date', '<=', $data['end_date'])
            ->whereDate('end_date', '>=', $data['start_date'])->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'You already have a pending or approved leave overlapping these dates.');
        }

        DB::table('leaves')->insert($data + [
            'staff_id' => $t->id, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now(),
        ]);

        // Tell every admin. A notification problem must never block the request.
        $this->notifyAdmins(
            'New leave request',
            $this->teacherName($t) . ' requested ' . $data['type'] . ' leave ('
                . Carbon::parse($data['start_date'])->format('d M') . ' to '
                . Carbon::parse($data['end_date'])->format('d M Y') . ').',
            'info'
        );

        return redirect()->route('teacher.leaves.index')->with('success', 'Leave request submitted. Awaiting approval.');
    }

    public function destroy($id)
    {
        $t = $this->teacher();
        $leave = DB::table('leaves')->where('id', $id)->first();
        $this->ensureOwner($leave);

        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        DB::table('leaves')->where('id', $id)->delete();

        $this->notifyAdmins(
            'Leave request cancelled',
            $this->teacherName($t) . ' cancelled a pending ' . $leave->type . ' leave request.',
            'warning'
        );

        return back()->with('success', 'Leave request cancelled.');
    }

    // ------------------------------------------------------------------

    private function teacherName($staff): string
    {
        return trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? '')) ?: 'A teacher';
    }

    private function notifyAdmins(string $title, string $message, string $level): void
    {
        try {
            $admins = User::role('admin')->get(); // Spatie role from RolePermissionSeeder

            if ($admins->isEmpty()) {
                logger()->warning('Leave notification skipped: no users with the admin role.');
                return;
            }

            Notification::send($admins, new AdminAlert(
                $title,
                $message,
                route('admin.leaves.index'),
                'leave',
                $level
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}