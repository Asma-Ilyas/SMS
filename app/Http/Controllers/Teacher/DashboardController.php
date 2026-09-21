<?php

namespace App\Http\Controllers\Teacher;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseTeacherController
{
    public function index()
    {
        $t     = $this->teacher();
        $today = Carbon::today();
        $day   = strtolower($today->format('l'));
        $pairs = $this->pairs();

        $sectionIds = $pairs->pluck('section_id')->unique()->all();
        $studentCount = $sectionIds ? DB::table('students')->whereIn('class_section_id', $sectionIds)->where('status', 'Active')->count() : 0;

        // Today's classes
        $todayClasses = in_array($day, self::DAYS, true)
            ? $this->ttBase()->where('te.teacher_id', $t->id)->where('te.day_of_week', $day)->orderBy('ts.sort_order')->get()
            : collect();

        // Attendance still to be marked today (classes scheduled today with no rows yet)
        $markedKeys = DB::table('student_attendance')->whereDate('date', $today)
            ->where('teacher_id', $t->id)->get(['class_section_id', 'subject_id'])
            ->map(fn ($r) => $r->class_section_id . '|' . $r->subject_id)->unique()->all();

        $pendingAttendance = $todayClasses
            ->unique(fn ($c) => $c->class_section_id . '|' . $c->subject_id)
            ->filter(fn ($c) => !in_array($c->class_section_id . '|' . $c->subject_id, $markedKeys, true))
            ->values();

        // Upcoming exams in my sections
        $upcomingExams = collect();
        if ($sectionIds) {
            $upcomingExams = DB::table('exams as e')
                ->join('class_sections as cs', 'cs.id', '=', 'e.class_section_id')
                ->join('classes as c', 'c.id', '=', 'cs.class_id')
                ->join('grades as g', 'g.id', '=', 'c.grade_id')
                ->whereIn('e.class_section_id', $sectionIds)
                ->whereDate('e.end_date', '>=', $today)
                ->orderBy('e.start_date')->limit(5)
                ->select('e.*', 'g.name as grade_name', 'cs.section_name')->get();
        }

        // Marks entry still pending (exam started, not published, not everyone entered)
        $marksPending = $this->marksTasks()
            ->filter(fn ($x) => !$x->locked && Carbon::parse($x->exam->start_date)->lte($today) && $x->entered < $x->total)
            ->take(6)->values();

        // My attendance today
        $myAttendance = DB::table('staff_attendances')->where('staff_id', $t->id)->whereDate('date', $today)->first();

        $pendingLeaves = DB::table('leaves')->where('staff_id', $t->id)->where('status', 'pending')->count();
        $lastSalary = DB::table('salaries')->where('staff_id', $t->id)->orderByDesc('month')->first();

        // Attendance this month
        $monthRows = DB::table('staff_attendances')->where('staff_id', $t->id)
            ->whereBetween('date', [$today->copy()->startOfMonth()->toDateString(), $today->toDateString()])
            ->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        return view('teacher.dashboard', compact(
            't', 'pairs', 'studentCount', 'todayClasses', 'pendingAttendance', 'upcomingExams',
            'marksPending', 'myAttendance', 'pendingLeaves', 'lastSalary', 'monthRows'
        ));
    }
}
