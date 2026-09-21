<?php

namespace App\Http\Controllers\Student;

use Illuminate\Support\Facades\DB;

class SubjectController extends BaseStudentController
{
    public function index()
    {
        $s = $this->student();
        $subjects = collect();

        if ($s->class_section_id) {
            $subjects = DB::table('subject_assignments as sa')
                ->join('subjects as sub', 'sub.id', '=', 'sa.subject_id')
                ->leftJoin('staff as t', 't.id', '=', 'sa.teacher_id')
                ->where('sa.class_section_id', $s->class_section_id)
                ->where('sa.is_active', 1)
                ->orderBy('sub.name')
                ->select(
                    'sub.name', 'sub.code', 'sub.type', 'sub.description',
                    'sa.weekly_frequency', 'sa.is_elective',
                    DB::raw("CONCAT(t.first_name,' ',t.last_name) as teacher_name"),
                    't.email as teacher_email'
                )->get();
        }

        return view('student.subjects.index', compact('s', 'subjects'));
    }
}
