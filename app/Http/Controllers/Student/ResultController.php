<?php

namespace App\Http\Controllers\Student;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResultController extends BaseStudentController
{
    public function index()
    {
        $s = $this->student();

        $results = DB::table('exam_results as er')
            ->join('exams as e', 'e.id', '=', 'er.exam_id')
            ->leftJoin('exam_types as et', 'et.id', '=', 'e.exam_type_id')
            ->where('er.student_id', $s->id)->where('e.is_published', 1)
            ->orderByDesc('e.end_date')
            ->select('er.*', 'e.name as exam_name', 'e.end_date', 'e.passing_percentage', 'et.name as type_name')
            ->get();

        $avg  = $results->count() ? round($results->avg('percentage'), 1) : null;
        $best = $results->sortByDesc('percentage')->first();

        return view('student.results.index', compact('s', 'results', 'avg', 'best'));
    }

    public function show($exam)
    {
        $s = $this->student();

        $exam = DB::table('exams as e')
            ->leftJoin('exam_types as et', 'et.id', '=', 'e.exam_type_id')
            ->where('e.id', $exam)->where('e.is_published', 1)
            ->select('e.*', 'et.name as type_name')->first();

        abort_if(!$exam, 404);

        $marks = $this->subjectMarks($exam->id, $s->id);
        $result = DB::table('exam_results')->where('exam_id', $exam->id)->where('student_id', $s->id)->first();

        abort_if($marks->isEmpty() && !$result, 404);

        // Fall back to computed totals when exam_results row is missing
        $totalObtained = $marks->sum('obtained');
        $totalMax      = $marks->sum('max');
        $percentage    = $result->percentage ?? ($totalMax > 0 ? round($totalObtained / $totalMax * 100, 2) : 0);

        $classStrength = $exam->class_section_id
            ? DB::table('exam_results')->where('exam_id', $exam->id)->count()
            : null;

        return view('student.results.show', compact('s', 'exam', 'marks', 'result', 'totalObtained', 'totalMax', 'percentage', 'classStrength'));
    }

    /** Reads exam_marks first, falls back to exam_subject_marks (both tables exist in schema). */
    private function subjectMarks(int $examId, int $studentId)
    {
        foreach (['exam_marks', 'exam_subject_marks'] as $table) {
            if (!Schema::hasTable($table)) continue;

            $rows = DB::table("$table as m")
                ->join('subjects as sub', 'sub.id', '=', 'm.subject_id')
                ->leftJoin('exam_subject_schedules as ess', function ($j) {
                    $j->on('ess.exam_id', '=', 'm.exam_id')->on('ess.subject_id', '=', 'm.subject_id');
                })
                ->where('m.exam_id', $examId)->where('m.student_id', $studentId)
                ->orderBy('sub.name')
                ->select('sub.name as subject', 'sub.code', 'm.marks_obtained', 'm.max_marks', 'm.passing_marks',
                         'm.remarks', 'ess.max_marks as sched_max', 'ess.passing_marks as sched_pass')
                ->get();

            if ($rows->isNotEmpty()) {
                return $rows->map(function ($r) {
                    $max  = $r->max_marks ?? $r->sched_max ?? 100;
                    $pass = $r->passing_marks ?? $r->sched_pass ?? 40;
                    $obt  = $r->marks_obtained;
                    return (object) [
                        'subject'  => $r->subject,
                        'code'     => $r->code,
                        'obtained' => $obt,
                        'max'      => $max,
                        'passing'  => $pass,
                        'pct'      => ($obt !== null && $max > 0) ? round($obt / $max * 100, 1) : null,
                        'passed'   => $obt !== null ? $obt >= $pass : null,
                        'remarks'  => $r->remarks,
                    ];
                });
            }
        }

        return collect();
    }
}
