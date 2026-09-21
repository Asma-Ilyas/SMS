<?php

namespace App\Http\Controllers\Teacher;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamController extends BaseTeacherController
{
    public function index()
    {
        $this->teacher();
        $today = Carbon::today();

        $groups = $this->marksTasks()->groupBy(fn ($x) => $x->exam->id)->map(function ($tasks) use ($today) {
            $e = $tasks->first()->exam;
            $start = Carbon::parse($e->start_date);
            $end = Carbon::parse($e->end_date);
            return (object) [
                'exam'  => $e,
                'tasks' => $tasks,
                'state' => $today->lt($start) ? 'upcoming' : ($today->gt($end) ? 'completed' : 'ongoing'),
            ];
        })->values();

        return view('teacher.exams.index', compact('groups'));
    }

    public function show($exam)
    {
        $this->teacher();

        $tasks = $this->marksTasks()->filter(fn ($x) => (int) $x->exam->id === (int) $exam)->values();
        abort_if($tasks->isEmpty(), 403, 'This exam is not linked to your classes.');

        $e = $tasks->first()->exam;
        $today = Carbon::today();
        $state = $today->lt(Carbon::parse($e->start_date)) ? 'upcoming' : ($today->gt(Carbon::parse($e->end_date)) ? 'completed' : 'ongoing');

        $section = $tasks->first()->pair->section_label;

        $schedule = DB::table('exam_subject_schedules as ess')
            ->join('subjects as sub', 'sub.id', '=', 'ess.subject_id')
            ->leftJoin('rooms as r', 'r.id', '=', 'ess.room_id')
            ->where('ess.exam_id', $e->id)
            ->orderBy('ess.exam_date')->orderBy('ess.start_time')
            ->select('ess.*', 'sub.name as subject_name', 'r.room_number', 'r.name as room_name')->get();

        $mySubjectIds = $tasks->pluck('pair.subject_id')->all();

        return view('teacher.exams.show', compact('e', 'tasks', 'state', 'section', 'schedule', 'mySubjectIds'));
    }
}
