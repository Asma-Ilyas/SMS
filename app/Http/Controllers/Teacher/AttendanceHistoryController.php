<?php

namespace App\Http\Controllers\Teacher;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Teacher's OWN attendance (check-in / check-out and history). */
class AttendanceHistoryController extends BaseTeacherController
{
    public function index(Request $request)
    {
        $t = $this->teacher();

        try {
            $month = Carbon::createFromFormat('Y-m', $request->get('month', now()->format('Y-m')))->startOfMonth();
        } catch (\Throwable $e) {
            $month = now()->startOfMonth();
        }

        $records = DB::table('staff_attendances')->where('staff_id', $t->id)
            ->whereBetween('date', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->orderByDesc('date')->get();

        $counts = $records->groupBy('status')->map->count();
        $summary = [
            'present'   => (int) ($counts['present'] ?? 0),
            'late'      => (int) ($counts['late'] ?? 0),
            'absent'    => (int) ($counts['absent'] ?? 0),
            'half_day'  => (int) ($counts['half_day'] ?? 0),
            'leave'     => (int) ($counts['leave'] ?? 0),
            'on_duty'   => (int) ($counts['on_duty'] ?? 0),
            'hours'     => round((float) $records->sum('work_hours'), 1),
            'overtime'  => (int) $records->sum('overtime_minutes'),
            'late_min'  => (int) $records->sum('late_minutes'),
        ];
        $working = $summary['present'] + $summary['late'] + $summary['absent'] + $summary['half_day'] + $summary['leave'] + $summary['on_duty'];
        $summary['pct'] = $working > 0
            ? round(($summary['present'] + $summary['late'] + $summary['on_duty'] + 0.5 * $summary['half_day']) / $working * 100, 1) : 0;

        return view('teacher.my-attendance.history', compact('t', 'records', 'summary', 'month'));
    }

    public function check()
    {
        $t = $this->teacher();
        $todayRecord = DB::table('staff_attendances')->where('staff_id', $t->id)->whereDate('date', today())->first();
        return view('teacher.my-attendance.check', compact('t', 'todayRecord'));
    }

    public function checkin(Request $request)
    {
        $t = $this->teacher();
        $today = Carbon::today();
        $now = Carbon::now();

        $existing = DB::table('staff_attendances')->where('staff_id', $t->id)->whereDate('date', $today)->first();

        if ($existing && $existing->check_in) {
            return back()->with('error', 'You have already checked in today.');
        }
        if ($existing && in_array($existing->status, ['leave', 'holiday', 'weekend', 'on_duty', 'training'], true)) {
            return back()->with('error', 'Today is already recorded as "' . str_replace('_', ' ', $existing->status) . '".');
        }

        $status = 'present';
        $late = 0;
        if ($t->cat_arrival) {
            $expected = Carbon::parse($today->toDateString() . ' ' . $t->cat_arrival);
            if ($now->gt($expected)) {
                $late = (int) abs($expected->diffInMinutes($now));
                $status = $late > 0 ? 'late' : 'present';
            }
        }

        $time = $now->format('H:i:s');
        $this->upsert('staff_attendances', ['staff_id' => $t->id, 'date' => $today->toDateString()], [
            'status'       => $status,
            'check_in'     => $time,
            'arrival_time' => $time,
            'late_minutes' => $late > 0 ? $late : null,
            'late_arrival_time' => $late > 0 ? $time : null,
            'late_reason'  => $late > 0 ? $request->input('reason') : null,
            'marked_by'    => $t->id,
            'is_approved'  => true,
        ]);

        return back()->with('success', $status === 'late'
            ? "Checked in at {$now->format('h:i A')} ({$late} min late)."
            : "Checked in at {$now->format('h:i A')}.");
    }

    public function checkout(Request $request)
    {
        $t = $this->teacher();
        $now = Carbon::now();

        $rec = DB::table('staff_attendances')->where('staff_id', $t->id)->whereDate('date', today())->first();

        if (!$rec || !$rec->check_in) {
            return back()->with('error', 'Please check in first.');
        }
        if ($rec->check_out) {
            return back()->with('error', 'You have already checked out today.');
        }

        $in = Carbon::parse(today()->toDateString() . ' ' . $rec->check_in);
        $total = (int) abs($in->diffInMinutes($now));

        $overtime = null;
        $early = null;
        if ($t->cat_departure) {
            $expectedOut = Carbon::parse(today()->toDateString() . ' ' . $t->cat_departure);
            if ($now->gt($expectedOut)) {
                $overtime = (int) abs($expectedOut->diffInMinutes($now));
            } elseif ($now->lt($expectedOut)) {
                $early = $request->input('reason') ?: 'Left before scheduled departure time';
            }
        }

        $time = $now->format('H:i:s');
        DB::table('staff_attendances')->where('id', $rec->id)->update([
            'check_out'              => $time,
            'departure_time'         => $time,
            'total_minutes'          => $total,
            'work_hours'             => round($total / 60, 2),
            'overtime_minutes'       => $overtime,
            'early_departure_reason' => $early,
            'updated_at'             => now(),
        ]);

        return back()->with('success', 'Checked out at ' . $now->format('h:i A') . ' (' . floor($total / 60) . 'h ' . ($total % 60) . 'm worked).');
    }
}
