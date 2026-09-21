<?php

namespace App\Http\Controllers\Student;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends BaseStudentController
{
    public function index(Request $request)
    {
        $s = $this->student();

        try {
            $month = Carbon::createFromFormat('Y-m', $request->get('month', now()->format('Y-m')))->startOfMonth();
        } catch (\Throwable $e) {
            $month = now()->startOfMonth();
        }

        $from = $month->toDateString();
        $to   = $month->copy()->endOfMonth()->toDateString();

        $records = DB::table('student_attendance as a')
            ->join('subjects as sub', 'sub.id', '=', 'a.subject_id')
            ->where('a.student_id', $s->id)
            ->whereBetween('a.date', [$from, $to])
            ->orderByDesc('a.date')->orderBy('sub.name')
            ->select('a.*', 'sub.name as subject_name')->get();

        $byDate  = $records->groupBy(fn ($r) => substr((string) $r->date, 0, 10));
        $summary = $this->attendanceSummary($s->id, $from, $to);
        $overall = $this->attendanceSummary($s->id);

        // Last 6 months trend
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->startOfMonth()->subMonths($i);
            $sm = $this->attendanceSummary($s->id, $m->toDateString(), $m->copy()->endOfMonth()->toDateString());
            $trend[] = ['label' => $m->format('M Y'), 'pct' => $sm['percentage'], 'days' => $sm['total']];
        }

        return view('student.attendance.index', compact('s', 'month', 'byDate', 'summary', 'overall', 'trend'));
    }
}
