<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class ExamMarkController extends Controller
{
    // Show list of exams (for mark entry)
    public function index()
    {
        $exams = Exam::with('class')->orderBy('id', 'desc')->get();
        return view('admin.exam-marks.index', compact('exams'));
    }

    // Show form to select exam, class, subject
    public function create(Request $request)
    {
        $exams = Exam::orderBy('name')->get();
        $classes = \App\Models\Classes::orderBy('id')->get();

        // If parameters are passed via GET, pre-select
        $selectedExam = $request->exam_id ? Exam::find($request->exam_id) : null;
        $selectedClass = $request->class_id ? \App\Models\Classes::find($request->class_id) : null;
        $selectedSubject = $request->subject_id ? Subject::find($request->subject_id) : null;

        // If all three are selected, show the actual mark entry grid
        if ($selectedExam && $selectedClass && $selectedSubject) {
            return $this->showGrid($selectedExam, $selectedClass, $selectedSubject);
        }

        // Otherwise show selection form
        $subjects = Subject::orderBy('name')->get();
        return view('admin.exam-marks.select', compact('exams', 'classes', 'subjects', 'selectedExam', 'selectedClass', 'selectedSubject'));
    }

    // Display the grid (students × marks)
    private function showGrid($exam, $class, $subject)
    {
        if ($exam->is_published) {
            return redirect()->route('admin.exam-marks.index')
                ->with('error', 'Cannot edit marks for a published exam.');
        }

        $students = Student::where('class_id', $class->id)
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->get();

        $existingMarks = ExamMark::where('exam_id', $exam->id)
                        ->where('subject_id', $subject->id)
                        ->get()
                        ->keyBy('student_id');

        return view('admin.exam-marks.grid', [
            'exam'          => $exam,
            'class'         => $class,
            'subject'       => $subject,
            'students'      => $students,
            'existingMarks' => $existingMarks,
        ]);
    }

    // Save marks from grid
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id'    => 'required|exists:exams,id',
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks'      => 'required|array',
            'marks.*'    => 'nullable|numeric|min:0|max:100',
        ]);

        $exam = Exam::find($validated['exam_id']);
        if ($exam->is_published) {
            return back()->with('error', 'Cannot modify marks for a published exam.');
        }

        foreach ($validated['marks'] as $studentId => $obtained) {
            if ($obtained === '' || $obtained === null) continue;
            ExamMark::updateOrCreate(
                [
                    'exam_id'    => $validated['exam_id'],
                    'student_id' => $studentId,
                    'subject_id' => $validated['subject_id'],
                ],
                [
                    'marks_obtained' => $obtained,
                    'max_marks'      => 100,
                    'passing_marks'  => 33,
                ]
            );
        }

        return redirect()->route('admin.exam-marks.index')->with('success', 'Marks saved.');
    }

    public function bulkUploadForm()
    {
        return view('admin.exam-marks.bulk-upload');
    }

    public function bulkUploadStore(Request $request)
    {
        // Placeholder
        return back()->with('info', 'Bulk upload feature coming soon.');
    }
}