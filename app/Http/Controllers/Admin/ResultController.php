<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Classes;
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
        $classes = Classes::orderBy('id')->get(); // no 'name' column

        $examId = $request->exam_id;
        $classId = $request->class_id;

        $results = collect();
        if ($examId && $classId) {
            $results = ExamResult::with(['student', 'exam'])
                ->where('exam_id', $examId)
                ->where('class_id', $classId)
                ->orderBy('rank_in_class')
                ->get();
        }

        return view('admin.results.class-wise', compact('exams', 'classes', 'examId', 'classId', 'results'));
    }

    public function studentWise(Request $request)
    {
        $students = Student::orderBy('first_name')->get();
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
            $results = ExamResult::where('exam_id', $examId)
                ->whereHas('class', function($q) use ($streamId, $fromGrade, $toGrade) {
                    $q->where('stream_id', $streamId);
                    if ($fromGrade) $q->where('grade_id', '>=', $fromGrade);
                    if ($toGrade) $q->where('grade_id', '<=', $toGrade);
                })
                ->orderBy('percentage', 'desc')
                ->get();
        }
        return view('admin.results.department-wise', compact('exams', 'streams', 'examId', 'streamId', 'fromGrade', 'toGrade', 'results'));
    }

    public function pdfReportCard($studentId, $examId = null)
    {
        $student = Student::with('class')->findOrFail($studentId);
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
        $students = Student::all();
        foreach ($students as $student) {
            ExamHelper::calculateAndStoreResult($examId, $student->id);
        }
        ExamHelper::recalculateRanks($examId);
        return back()->with('success', 'Results recalculated and ranks updated.');
    }
}