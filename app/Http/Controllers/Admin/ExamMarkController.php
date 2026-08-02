<?php
// app/Http/Controllers/Admin/ExamMarkController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamMarkController extends Controller
{
    /**
     * Display marks entry page
     */
    public function index()
    {
        $exams = Exam::with(['examType', 'classSection'])
            ->where('is_published', true)
            ->orderBy('start_date', 'desc')
            ->paginate(20);
            
        return view('admin.exam-marks.index', compact('exams'));
    }

    /**
     * Show selection form for marks entry
     */
    public function create(Request $request)
    {
        // Get all data needed for the form
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        $subjects = Subject::all();
        $exams = Exam::where('is_published', true)
            ->with(['examType', 'classSection'])
            ->orderBy('start_date', 'desc')
            ->get();
        
        // For backward compatibility
        $classes = $classSections;
        
        $selectedClass = null;
        $selectedSubject = null;
        $examId = $request->exam_id;
        
        if ($examId) {
            $exam = Exam::find($examId);
            if ($exam) {
                $selectedClass = $exam->classSection;
                // Get subjects for this class
                $subjects = Subject::whereHas('subjectAssignments', function($q) use ($exam) {
                    $q->where('class_section_id', $exam->class_section_id);
                })->get();
            }
        }
        
        return view('admin.exam-marks.select', compact(
            'classSections', 'classes', 'subjects', 'selectedClass', 
            'selectedSubject', 'examId', 'exams'
        ));
    }

    /**
     * Show marks entry form (POST handler)
     */
    public function store(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $examId = $request->exam_id;
        $classSectionId = $request->class_section_id;
        $subjectId = $request->subject_id;

        // Get students
        $students = Student::where('class_section_id', $classSectionId)
            ->orderBy('first_name')
            ->get();

        // Get existing marks
        $existingMarks = ExamMark::where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->get()
            ->keyBy('student_id');

        $exam = Exam::find($examId);
        $subject = Subject::find($subjectId);
        $classSection = ClassSection::find($classSectionId);

        return view('admin.exam-marks.entry', compact(
            'exam', 'subject', 'classSection', 'students', 'existingMarks'
        ));
    }

    /**
     * Save marks (POST handler)
     */
    public function saveMarks(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks' => 'required|array',
            'marks.*' => 'nullable|numeric|min:0|max:100',
            'remarks' => 'nullable|array',
        ]);

        $examId = $request->exam_id;
        $subjectId = $request->subject_id;

        foreach ($request->marks as $studentId => $marksObtained) {
            ExamMark::updateOrCreate(
                [
                    'exam_id' => $examId,
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                ],
                [
                    'marks_obtained' => $marksObtained ?? 0,
                    'max_marks' => 100,
                    'passing_marks' => 40,
                    'remarks' => $request->remarks[$studentId] ?? ($marksObtained >= 40 ? 'Pass' : 'Fail'),
                ]
            );
        }

        // Recalculate results
        $this->calculateResults($examId);

        return redirect()->route('admin.exam-marks.index')
            ->with('success', 'Marks saved successfully.');
    }

    /**
     * Calculate results for an exam
     */
    private function calculateResults($examId)
    {
        $exam = Exam::findOrFail($examId);
        $students = Student::where('class_section_id', $exam->class_section_id)->get();
        $gradeScale = \App\Models\GradeScale::getDefault();

        $results = [];

        foreach ($students as $student) {
            $marks = ExamMark::where('exam_id', $examId)
                ->where('student_id', $student->id)
                ->get();

            $totalMarks = $marks->sum('marks_obtained');
            $totalMaxMarks = $marks->sum('max_marks');
            $percentage = $totalMaxMarks > 0 
                ? round(($totalMarks / $totalMaxMarks) * 100, 2) 
                : 0;

            $grade = $gradeScale ? $gradeScale->getGrade($percentage) : 'N/A';
            $remarks = $percentage >= 40 ? 'Pass' : 'Fail';

            $results[] = [
                'exam_id' => $examId,
                'student_id' => $student->id,
                'class_section_id' => $student->class_section_id,
                'total_marks' => $totalMarks,
                'total_max_marks' => $totalMaxMarks,
                'percentage' => $percentage,
                'grade' => $grade,
                'remarks' => $remarks,
            ];
        }

        \App\Models\ExamResult::where('exam_id', $examId)->delete();
        \App\Models\ExamResult::insert($results);

        // Calculate ranks
        $this->calculateRanks($examId);
    }

    /**
     * Calculate ranks
     */
    private function calculateRanks($examId)
    {
        $results = \App\Models\ExamResult::where('exam_id', $examId)
            ->orderBy('percentage', 'desc')
            ->get();

        $rank = 1;
        $prevPercentage = null;
        foreach ($results as $index => $result) {
            if ($prevPercentage !== null && $result->percentage < $prevPercentage) {
                $rank = $index + 1;
            }
            \App\Models\ExamResult::where('id', $result->id)->update(['rank_in_class' => $rank]);
            $prevPercentage = $result->percentage;
        }
    }

    /**
     * Bulk upload form
     */
    public function bulkUploadForm()
    {
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        $subjects = Subject::all();
        $exams = Exam::where('is_published', true)->get();

        return view('admin.exam-marks.bulk-upload', compact('classSections', 'subjects', 'exams'));
    }

    /**
     * Bulk upload store
     */
    public function bulkUploadStore(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        // Process file and save marks
        // Implementation depends on your Excel package

        return redirect()->route('admin.exam-marks.index')
            ->with('success', 'Bulk upload completed successfully.');
    }
}