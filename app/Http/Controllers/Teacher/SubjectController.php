<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Support\Facades\DB;

class SubjectController extends BaseTeacherController
{
    public function index()
    {
        $t = $this->teacher();
        $pairs = $this->pairs();

        $strength = $pairs->isEmpty() ? collect() : DB::table('students')
            ->whereIn('class_section_id', $pairs->pluck('section_id')->unique()->all())->where('status', 'Active')
            ->selectRaw('class_section_id, COUNT(*) as c')->groupBy('class_section_id')->pluck('c', 'class_section_id');

        $freq = DB::table('subject_assignments')->where('teacher_id', $t->id)->where('is_active', 1)
            ->get(['class_section_id', 'subject_id', 'weekly_frequency', 'is_elective'])
            ->keyBy(fn ($r) => $r->class_section_id . '|' . $r->subject_id);

        $periods = DB::table('timetable_entries')->where('teacher_id', $t->id)
            ->selectRaw('class_section_id, subject_id, COUNT(*) as c')->groupBy('class_section_id', 'subject_id')->get()
            ->keyBy(fn ($r) => $r->class_section_id . '|' . $r->subject_id);

        $rows = $pairs->map(function ($p) use ($strength, $freq, $periods) {
            $p->students   = (int) ($strength[$p->section_id] ?? 0);
            $p->weekly     = $freq[$p->key]->weekly_frequency ?? null;
            $p->elective   = (bool) ($freq[$p->key]->is_elective ?? false);
            $p->scheduled  = (int) ($periods[$p->key]->c ?? 0);
            return $p;
        });

        $bySubject = $rows->groupBy('subject_name');

        return view('teacher.subjects.index', compact('t', 'rows', 'bySubject'));
    }
}
