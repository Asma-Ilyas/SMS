<?php

namespace App\Http\Controllers\Student;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamController extends BaseStudentController
{
    public function index()
    {
        $s = $this->student();
        $today = Carbon::today();
        $exams = collect();

        if ($s->class_section_id) {
            $exams = DB::table('exams as e')
                ->leftJoin('exam_types as et', 'et.id', '=', 'e.exam_type_id')
                ->leftJoin('exam_groups as eg', 'eg.id', '=', 'e.exam_group_id')
                ->where('e.class_section_id', $s->class_section_id)
                ->orderByDesc('e.start_date')
                ->select('e.*', 'et.name as type_name', 'eg.name as group_name')->get()
                ->map(function ($e) use ($today) {
                    $start = Carbon::parse($e->start_date);
                    $end   = Carbon::parse($e->end_date);
                    $e->state = $today->lt($start) ? 'upcoming' : ($today->gt($end) ? 'completed' : 'ongoing');
                    return $e;
                });
        }

        return view('student.exams.index', compact('s', 'exams'));
    }

    public function show($exam)
    {
        $s = $this->student();

        $exam = DB::table('exams as e')
            ->leftJoin('exam_types as et', 'et.id', '=', 'e.exam_type_id')
            ->leftJoin('exam_groups as eg', 'eg.id', '=', 'e.exam_group_id')
            ->where('e.id', $exam)
            ->select('e.*', 'et.name as type_name', 'eg.name as group_name')->first();

        abort_if(!$exam || (int) $exam->class_section_id !== (int) $s->class_section_id, 404);

        $schedule = DB::table('exam_subject_schedules as ess')
            ->join('subjects as sub', 'sub.id', '=', 'ess.subject_id')
            ->leftJoin('rooms as r', 'r.id', '=', 'ess.room_id')
            ->where('ess.exam_id', $exam->id)
            ->orderBy('ess.exam_date')->orderBy('ess.start_time')
            ->select('ess.*', 'sub.name as subject_name', 'sub.code as subject_code', 'r.name as room_name', 'r.room_number')
            ->get();

        return view('student.exams.show', compact('s', 'exam', 'schedule'));
    }
}
