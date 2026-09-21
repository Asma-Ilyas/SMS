<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Report cards of students in the teacher's classes (published exams only). */
class ReportCardController extends BaseTeacherController
{
    public function index(Request $request)
    {
        $this->teacher();
        $sections = $this->mySections();

        $exams = collect(); $students = collect(); $results = collect();
        $sectionId = $request->filled('section_id') ? (int) $request->section_id : null;
        $examId = $request->filled('exam_id') ? (int) $request->exam_id : null;

        if ($sectionId) {
            abort_if(!$sections->contains('id', $sectionId), 403);

            $exams = DB::table('exams')->where('class_section_id', $sectionId)->where('is_published', 1)
                ->orderByDesc('start_date')->get(['id', 'name', 'start_date']);
            $students = $this->studentsOfSection($sectionId);

            if ($examId) {
                abort_if(!$exams->contains('id', $examId), 404);
                $results = DB::table('exam_results')->where('exam_id', $examId)->get()->keyBy('student_id');
            }
        }

        return view('teacher.report-card.index', compact('sections', 'sectionId', 'examId', 'exams', 'students', 'results'));
    }

    public function show($exam, $student)
    {
        $this->teacher();

        $exam = DB::table('exams as e')->leftJoin('exam_types as et', 'et.id', '=', 'e.exam_type_id')
            ->where('e.id', $exam)->where('e.is_published', 1)->select('e.*', 'et.name as type_name')->first();
        abort_if(!$exam, 404);

        $student = DB::table('students as s')
            ->leftJoin('class_sections as cs', 'cs.id', '=', 's.class_section_id')
            ->leftJoin('classes as c', 'c.id', '=', 'cs.class_id')
            ->leftJoin('grades as g', 'g.id', '=', 'c.grade_id')
            ->leftJoin('streams as st', 'st.id', '=', 'c.stream_id')
            ->leftJoin('academic_sessions as ays', 'ays.id', '=', 'c.academic_session_id')
            ->where('s.id', $student)
            ->select('s.*', 'cs.section_name', 'g.name as grade_name', 'st.name as stream_name', 'ays.name as session_name')->first();
        abort_if(!$student || (int) $student->class_section_id !== (int) $exam->class_section_id, 404);
        abort_if(!$this->mySections()->contains('id', (int) $student->class_section_id), 403);

        $marks  = $this->studentSubjectMarks($exam->id, $student->id);
        $result = DB::table('exam_results')->where('exam_id', $exam->id)->where('student_id', $student->id)->first();
        abort_if($marks->isEmpty() && !$result, 404, 'No result available for this student.');

        $totalObtained = $marks->sum('obtained');
        $totalMax = $marks->sum('max');
        $percentage = $result->percentage ?? ($totalMax > 0 ? round($totalObtained / $totalMax * 100, 2) : 0);
        $grade = $result->grade ?? $this->gradeFor((float) $percentage);
        $strength = DB::table('exam_results')->where('exam_id', $exam->id)->count();

        return view('teacher.report-card.show', compact('exam', 'student', 'marks', 'result', 'totalObtained', 'totalMax', 'percentage', 'grade', 'strength'));
    }
}
