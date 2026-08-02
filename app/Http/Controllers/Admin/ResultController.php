<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ClassSection;
use App\Models\Stream;
use App\Models\Student;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\ExamHelper;

class ResultController extends Controller
{
    public function classWise(Request $request)
    {
        $exams = Exam::where('is_published', true)->orderBy('name')->get();
        $classSections = ClassSection::with('class.grade')->orderBy('class_id')->orderBy('section_name')->get();

        $examId = $request->exam_id;
        $classSectionId = $request->class_section_id;

        $results = collect();
        if ($examId && $classSectionId) {
            $results = ExamResult::with(['student', 'exam'])
                ->where('exam_id', $examId)
                ->where('class_section_id', $classSectionId)
                ->orderBy('rank_in_class')
                ->get();
        }

        return view('admin.results.class-wise', compact('exams', 'classSections', 'examId', 'classSectionId', 'results'));
    }

    public function studentWise(Request $request)
    {
        $students = Student::with('classSection')->orderBy('first_name')->get();
        $studentId = $request->student_id;
        $results = collect();
        if ($studentId) {
            $results = ExamResult::with('exam')
                        ->where('student_id', $studentId)
                        ->orderBy('exam_id')
                        ->get();
        }
        return view('admin.results.student-wise', compact('students', 'studentId', 'results'));
    }

    public function departmentWise(Request $request)
    {
        $exams = Exam::where('is_published', true)->get();
        $streams = Stream::orderBy('name')->get();

        $examId = $request->exam_id;
        $streamId = $request->stream_id;
        $fromGrade = $request->from_grade;
        $toGrade = $request->to_grade;

        $results = collect();
        if ($examId && $streamId) {
            // Get all class sections belonging to the selected stream (via class)
            $classSectionIds = ClassSection::whereHas('class', function($q) use ($streamId, $fromGrade, $toGrade) {
                $q->where('stream_id', $streamId);
                if ($fromGrade) $q->where('grade_id', '>=', $fromGrade);
                if ($toGrade) $q->where('grade_id', '<=', $toGrade);
            })->pluck('id');

            $results = ExamResult::where('exam_id', $examId)
                ->whereIn('class_section_id', $classSectionIds)
                ->orderBy('percentage', 'desc')
                ->get();
        }
        return view('admin.results.department-wise', compact('exams', 'streams', 'examId', 'streamId', 'fromGrade', 'toGrade', 'results'));
    }

    public function pdfReportCard($studentId, $examId = null)
    {
        $student = Student::with('classSection.class.grade')->findOrFail($studentId);
        $exams = Exam::where('is_published', true)->orderBy('start_date')->get();
        if ($examId) {
            $exams = Exam::where('id', $examId)->get();
        }

        $data = [];
        foreach ($exams as $exam) {
            $result = ExamResult::where('exam_id', $exam->id)
                        ->where('student_id', $studentId)
                        ->first();
            $marks = \App\Models\ExamMark::with('subject')
                        ->where('exam_id', $exam->id)
                        ->where('student_id', $studentId)
                        ->get();
            $data[] = [
                'exam' => $exam,
                'result' => $result,
                'marks' => $marks,
            ];
        }

        $pdf = Pdf::loadView('pdf.report-card', compact('student', 'data'));
        return $pdf->download("report_card_{$student->id}.pdf");
    }

    public function recalcExam($examId)
    {
        // Get all students who have any mark in this exam (or all students in the exam's class sections)
        $students = Student::whereHas('marks', function($q) use ($examId) {
            $q->where('exam_id', $examId);
        })->get();

        foreach ($students as $student) {
            ExamHelper::calculateAndStoreResult($examId, $student->id);
        }
        ExamHelper::recalculateRanks($examId);
        return back()->with('success', 'Results recalculated and ranks updated.');
    }
}