<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentController extends BaseTeacherController
{
    public function index(Request $request)
    {
        $this->teacher();
        $sections = $this->mySections();
        $secIds = $sections->pluck('id')->all();

        $students = collect();
        if ($secIds) {
            $students = DB::table('students as s')
                ->join('class_sections as cs', 'cs.id', '=', 's.class_section_id')
                ->join('classes as c', 'c.id', '=', 'cs.class_id')
                ->join('grades as g', 'g.id', '=', 'c.grade_id')
                ->whereIn('s.class_section_id', $secIds)
                ->when($request->filled('section_id'), fn ($q) => $q->where('s.class_section_id', (int) $request->section_id))
                ->when($request->filled('q'), function ($q) use ($request) {
                    $term = '%' . $request->q . '%';
                    $q->where(function ($w) use ($term) {
                        $w->where('s.first_name', 'like', $term)->orWhere('s.last_name', 'like', $term)
                          ->orWhere('s.admission_number', 'like', $term)->orWhere('s.roll_number', 'like', $term);
                    });
                })
                ->when($request->filled('status'), fn ($q) => $q->where('s.status', $request->status))
                ->orderBy('g.numeric_value')->orderBy('cs.section_name')
                ->orderByRaw('CAST(s.roll_number AS UNSIGNED)')->orderBy('s.first_name')
                ->select('s.*', 'g.name as grade_name', 'cs.section_name')
                ->paginate(25)->withQueryString();
        }

        return view('teacher.students.index', compact('students', 'sections'));
    }

    public function show($student)
    {
        $t = $this->teacher();

        $student = DB::table('students as s')
            ->leftJoin('class_sections as cs', 'cs.id', '=', 's.class_section_id')
            ->leftJoin('classes as c', 'c.id', '=', 'cs.class_id')
            ->leftJoin('grades as g', 'g.id', '=', 'c.grade_id')
            ->where('s.id', $student)
            ->select('s.*', 'g.name as grade_name', 'cs.section_name')->first();

        abort_if(!$student, 404);

        $myPairs = $this->pairs()->where('section_id', (int) $student->class_section_id)->values();
        abort_if($myPairs->isEmpty(), 403, 'This student is not in one of your classes.');

        // Attendance per subject I teach
        $attendance = [];
        foreach ($myPairs as $p) {
            $rows = DB::table('student_attendance')->where('student_id', $student->id)
                ->where('class_section_id', $p->section_id)->where('subject_id', $p->subject_id)
                ->selectRaw('student_id, status, COUNT(*) as c')->groupBy('student_id', 'status')->get();
            $attendance[$p->subject_id] = $this->attendancePercent($rows);
        }

        // Marks in my subjects
        $marks = collect();
        $subjectIds = $myPairs->pluck('subject_id')->all();
        foreach (['exam_subject_marks', 'exam_marks'] as $tbl) {
            if (!Schema::hasTable($tbl)) continue;
            $rows = DB::table("$tbl as m")
                ->join('exams as e', 'e.id', '=', 'm.exam_id')
                ->join('subjects as sub', 'sub.id', '=', 'm.subject_id')
                ->where('m.student_id', $student->id)->whereIn('m.subject_id', $subjectIds)
                ->where('e.class_section_id', $student->class_section_id)
                ->select('m.exam_id', 'm.subject_id', 'm.marks_obtained', 'm.max_marks', 'm.passing_marks',
                         'e.name as exam_name', 'e.start_date', 'e.is_published', 'sub.name as subject_name')->get();
            foreach ($rows as $r) {
                $marks[$r->exam_id . '|' . $r->subject_id] = $r;
            }
        }
        $marks = $marks->sortByDesc('start_date')->values();

        return view('teacher.students.show', compact('t', 'student', 'myPairs', 'attendance', 'marks'));
    }
}
