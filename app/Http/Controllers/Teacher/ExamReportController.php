<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExamReportController extends BaseTeacherController
{
    public function index(Request $request)
    {
        $this->teacher();
        $tasks = $this->marksTasks();

        $task = null; $stats = null; $rows = collect(); $dist = []; $trend = collect();

        if ($request->filled('task')) {
            $task = $tasks->firstWhere('key', $request->task);
            abort_if(!$task, 403);

            $students = $this->studentsOfSection($task->pair->section_id);
            $map = $this->markMap((int) $task->exam->id, (int) $task->pair->subject_id);

            $rows = $students->map(function ($s) use ($map, $task) {
                $m = $map[$s->id] ?? null;
                $max = ($m->max_marks ?? null) ?: $task->max;
                $pass = ($m->passing_marks ?? null) ?? $task->pass;
                $obt = $m->marks_obtained ?? null;
                $pct = ($obt !== null && $max > 0) ? round($obt / $max * 100, 1) : null;
                return (object) [
                    'student' => $s, 'obtained' => $obt, 'max' => $max, 'pct' => $pct,
                    'grade' => $this->gradeFor($pct), 'passed' => $obt === null ? null : $obt >= $pass,
                ];
            });

            $graded = $rows->whereNotNull('pct');
            $stats = [
                'students' => $rows->count(),
                'entered'  => $graded->count(),
                'absent'   => $rows->count() - $graded->count(),
                'avg'      => $graded->count() ? round($graded->avg('pct'), 1) : null,
                'high'     => $graded->max('pct'),
                'low'      => $graded->min('pct'),
                'passed'   => $graded->where('passed', true)->count(),
                'failed'   => $graded->where('passed', false)->count(),
            ];
            $stats['pass_rate'] = $stats['entered'] ? round($stats['passed'] / $stats['entered'] * 100, 1) : null;

            foreach ($graded as $r) {
                $dist[$r->grade] = ($dist[$r->grade] ?? 0) + 1;
            }
            ksort($dist);

            // Trend of this section+subject across all exams
            foreach (['exam_marks', 'exam_subject_marks'] as $tbl) {
                if (!Schema::hasTable($tbl)) continue;
                $t = DB::table("$tbl as m")->join('exams as e', 'e.id', '=', 'm.exam_id')
                    ->where('e.class_section_id', $task->pair->section_id)->where('m.subject_id', $task->pair->subject_id)
                    ->whereNotNull('m.marks_obtained')->whereNotNull('m.max_marks')->where('m.max_marks', '>', 0)
                    ->groupBy('e.id', 'e.name', 'e.start_date')->orderBy('e.start_date')
                    ->selectRaw('e.id, e.name, e.start_date, AVG(m.marks_obtained / m.max_marks * 100) as avg_pct, COUNT(*) as n')->get();
                if ($t->isNotEmpty()) { $trend = $t; break; }
            }
        }

        return view('teacher.exam-reports.index', compact('tasks', 'task', 'stats', 'rows', 'dist', 'trend'));
    }
}
