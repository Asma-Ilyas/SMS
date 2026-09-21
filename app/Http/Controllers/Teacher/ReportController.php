<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends BaseTeacherController
{
    /** "My Reports": workload, attendance sessions, class performance */
    public function index()
    {
        $t = $this->teacher();
        $days  = self::DAYS;
        $pairs = $this->pairs();

        $tt = $this->ttBase()->where('te.teacher_id', $t->id)->get();
        $perDay = [];
        foreach ($days as $d) {
            $perDay[$d] = $tt->where('day_of_week', $d)->count();
        }

        $sessions30 = DB::table('student_attendance')->where('teacher_id', $t->id)
            ->whereDate('date', '>=', now()->subDays(30)->toDateString())
            ->selectRaw("COUNT(DISTINCT CONCAT(date,'-',class_section_id,'-',subject_id)) as c")->value('c');

        $strength = $pairs->isEmpty() ? collect() : DB::table('students')
            ->whereIn('class_section_id', $pairs->pluck('section_id')->unique()->all())->where('status', 'Active')
            ->selectRaw('class_section_id, COUNT(*) as c')->groupBy('class_section_id')->pluck('c', 'class_section_id');

        // Average performance in published exams (latest 12)
        $performance = $this->marksTasks()->where('locked', true)->take(12)->map(function ($task) {
            $map = $this->markMap((int) $task->exam->id, (int) $task->pair->subject_id);
            $pcts = $map->filter(fn ($m) => $m->marks_obtained !== null && ($m->max_marks ?: $task->max) > 0)
                ->map(fn ($m) => $m->marks_obtained / (($m->max_marks ?: $task->max)) * 100);
            $pass = $map->filter(fn ($m) => $m->marks_obtained !== null && $m->marks_obtained >= ($m->passing_marks ?? $task->pass))->count();
            return (object) [
                'task' => $task,
                'avg'  => $pcts->count() ? round($pcts->avg(), 1) : null,
                'n'    => $pcts->count(),
                'pass_rate' => $pcts->count() ? round($pass / $pcts->count() * 100, 1) : null,
            ];
        })->values();

        $stats = [
            'sections'  => $pairs->unique('section_id')->count(),
            'subjects'  => $pairs->unique('subject_id')->count(),
            'students'  => $pairs->unique('section_id')->sum(fn ($p) => (int) ($strength[$p->section_id] ?? 0)),
            'periods'   => $tt->count(),
            'sessions30' => (int) $sessions30,
        ];

        return view('teacher.reports.index', compact('t', 'days', 'perDay', 'pairs', 'strength', 'performance', 'stats'));
    }

    /** Student performance in my subjects (attendance + average marks) */
    public function students(Request $request)
    {
        $this->teacher();
        $pairs = $this->pairs(true);

        $sel = null; $rows = collect();
        $from = $request->input('from');
        $to   = $request->input('to');

        if ($request->filled('pair')) {
            [$sec, $sub] = array_map('intval', explode('|', $request->pair) + [0, 0]);
            $sel = $this->findPair($sec, $sub, true);

            $students = $this->studentsOfSection($sec);
            $att = $this->attendanceCounts($sec, $sub, $from, $to);

            $avg = collect();
            foreach (['exam_subject_marks', 'exam_marks'] as $tbl) {
                if (!Schema::hasTable($tbl)) continue;
                $r = DB::table("$tbl as m")->join('exams as e', 'e.id', '=', 'm.exam_id')
                    ->where('e.class_section_id', $sec)->where('m.subject_id', $sub)
                    ->whereNotNull('m.marks_obtained')->where('m.max_marks', '>', 0)
                    ->groupBy('m.student_id')
                    ->selectRaw('m.student_id, AVG(m.marks_obtained / m.max_marks * 100) as avg_pct, COUNT(*) as n')->get();
                foreach ($r as $x) { $avg[$x->student_id] = $x; }
            }

            $rows = $students->map(function ($s) use ($att, $avg) {
                $a = $this->attendancePercent($att[$s->id] ?? null);
                $m = $avg[$s->id] ?? null;
                return (object) [
                    'student' => $s, 'att' => $a,
                    'avg' => $m ? round($m->avg_pct, 1) : null, 'exams' => $m->n ?? 0,
                    'grade' => $m ? $this->gradeFor((float) $m->avg_pct) : '—',
                ];
            });
        }

        return view('teacher.reports.students', compact('pairs', 'sel', 'rows', 'from', 'to'));
    }
}
