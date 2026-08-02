<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Student;
use App\Models\ExamGroup;
use App\Models\Subject;
use App\Models\ClassSection;          // ✅ added
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamResultController extends Controller
{
    // Show results for a single student in a specific exam
    public function studentResult(Exam $exam, Student $student)
    {
        $marks = ExamMark::with('subject')
            ->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get();

        $summary = $this->calculateSummary($marks);
        return view('admin.exams.result-single', compact('exam', 'student', 'marks', 'summary'));
    }

    // Bulk print: select students from a class section
    public function bulkPrintForm(Exam $exam)
    {
        $students = $exam->classSection ? $exam->classSection->students : collect();
        return view('admin.exams.bulk-print', compact('exam', 'students'));
    }

    public function bulkPrint(Request $request, Exam $exam)
    {
        $request->validate(['student_ids' => 'required|array']);
        $students = Student::whereIn('id', $request->student_ids)->get();
        $data = [];
        foreach ($students as $student) {
            $marks = ExamMark::with('subject')
                ->where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->get();
            $summary = $this->calculateSummary($marks);
            $data[] = ['student' => $student, 'marks' => $marks, 'summary' => $summary];
        }
        $pdf = Pdf::loadView('pdf.exam-results-bulk', compact('exam', 'data'));
        return $pdf->download("exam_results_{$exam->id}.pdf");
    }

    // Academic Report (all exams for a student)
    public function academicReport(Student $student)
    {
        // ✅ Get all published exams for the student's class section
        $exams = Exam::where('class_section_id', $student->class_section_id)
            ->where('is_published', true)
            ->orderBy('start_date')
            ->get();

        $report = [];
        foreach ($exams as $exam) {
            $marks = ExamMark::with('subject')
                ->where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->get();
            $report[$exam->id] = ['exam' => $exam, 'marks' => $marks, 'summary' => $this->calculateSummary($marks)];
        }
        $overall = $this->calculateOverall($report);
        return view('admin.exams.academic-report', compact('student', 'report', 'overall'));
    }

    // Multi-Group Report (now uses class_section_id)
    public function multiGroupReportForm()
    {
        $groups = ExamGroup::where('is_active', true)->orderBy('name')->get();
        $classSections = ClassSection::with('class.grade')->get();
        return view('admin.exams.multi-group-report', compact('groups', 'classSections'));
    }

    public function multiGroupReport(Request $request)
    {
        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'group_ids'        => 'required|array|min:1',
            'group_ids.*'      => 'exists:exam_groups,id',
        ]);

        $classSection = ClassSection::find($request->class_section_id);
        $groups = ExamGroup::whereIn('id', $request->group_ids)->orderBy('sort_order')->get();

        // Get all students in this class section (for report generation)
        $students = $classSection->students;

        // For multi‑group report, we need to generate a PDF covering all students? 
        // The original method only handled one student. We'll adjust to handle all students of that class section.
        $reportData = [];
        foreach ($students as $student) {
            $studentData = [];
            foreach ($groups as $group) {
                $exams = Exam::where('exam_group_id', $group->id)
                    ->where('class_section_id', $classSection->id)
                    ->where('is_published', true)
                    ->orderBy('start_date')
                    ->get();

                $examData = [];
                $groupTotal = ['total_marks' => 0, 'total_max' => 0];

                foreach ($exams as $exam) {
                    $marks = ExamMark::with('subject')
                        ->where('exam_id', $exam->id)
                        ->where('student_id', $student->id)
                        ->get();
                    $summary = $this->calculateSummary($marks);
                    $groupTotal['total_marks'] += $summary['total_marks'];
                    $groupTotal['total_max'] += $summary['total_max'];
                    $examData[] = ['exam' => $exam, 'marks' => $marks, 'summary' => $summary];
                }

                $groupPercentage = $groupTotal['total_max'] > 0 ? round(($groupTotal['total_marks'] / $groupTotal['total_max']) * 100, 2) : 0;
                $studentData[] = [
                    'group' => $group,
                    'exams' => $examData,
                    'total_marks' => $groupTotal['total_marks'],
                    'total_max'   => $groupTotal['total_max'],
                    'percentage'  => $groupPercentage,
                    'grade'       => $this->getGrade($groupPercentage)
                ];
            }
            $reportData[] = ['student' => $student, 'groups_data' => $studentData];
        }

        $pdf = Pdf::loadView('pdf.multi-group-report', compact('classSection', 'reportData', 'groups'));
        return $pdf->download("academic_report_class_section_{$classSection->id}.pdf");
    }

    // ========== MARKS ENTRY ==========
    public function marksEntryForm(Exam $exam)
    {
        // ✅ Get students via class_section
        $students = $exam->classSection ? $exam->classSection->students : collect();
        $students = $students->sortBy('first_name')->values();

        $subjects = Subject::orderBy('name')->get();

        return view('admin.exams.marks-entry', compact('exam', 'students', 'subjects'));
    }

    public function storeMarks(Request $request, Exam $exam)
    {
        $request->validate([
            'marks' => 'required|array',
            'max_marks' => 'required|array',
            'max_marks.*' => 'numeric|min:0',
        ]);

        foreach ($request->marks as $studentId => $subjects) {
            foreach ($subjects as $subjectId => $marksObtained) {
                if ($marksObtained === '' || $marksObtained === null) continue;
                ExamMark::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $studentId,
                        'subject_id' => $subjectId,
                    ],
                    [
                        'marks_obtained' => $marksObtained,
                        'max_marks' => $request->max_marks[$subjectId] ?? 100,
                    ]
                );
            }
        }
        return redirect()->back()->with('success', 'Marks saved successfully.');
    }

    // ========== CLASS SECTION PDF MARKSHEET ==========
    public function classSectionMarksheet(Exam $exam, $classSectionId)
    {
        $classSection = ClassSection::with('class.grade')->findOrFail($classSectionId);
        $students = $classSection->students()->orderBy('first_name')->orderBy('last_name')->get();

        // Get subjects that have marks for this exam in this class section
        $subjects = Subject::whereHas('examMarks', function ($q) use ($exam, $classSectionId) {
            $q->where('exam_id', $exam->id)
              ->whereIn('student_id', Student::where('class_section_id', $classSectionId)->pluck('id'));
        })->orderBy('name')->get();

        if ($subjects->isEmpty()) {
            // fallback: all subjects assigned to this class section
            $subjects = $classSection->subjectAssignments->map->subject->sortBy('name');
        }

        $pdf = Pdf::loadView('pdf.class_section_marksheet', [
            'exam'          => $exam,
            'classSection'  => $classSection,
            'students'      => $students,
            'subjects'      => $subjects,
        ]);

        return $pdf->download("class_section_marksheet_{$exam->id}_{$classSectionId}.pdf");
    }

    // ========== HELPER METHODS ==========
    private function calculateSummary($marks)
    {
        $totalMarks = $marks->sum('marks_obtained');
        $totalMax = $marks->sum('max_marks');
        $percentage = $totalMax > 0 ? round(($totalMarks / $totalMax) * 100, 2) : 0;
        return [
            'total_marks' => $totalMarks,
            'total_max'   => $totalMax,
            'percentage'  => $percentage,
            'grade'       => $this->getGrade($percentage)
        ];
    }

    private function getGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    private function calculateOverall($report)
    {
        $totalMarks = 0;
        $totalMax = 0;
        foreach ($report as $r) {
            $totalMarks += $r['summary']['total_marks'];
            $totalMax += $r['summary']['total_max'];
        }
        $percentage = $totalMax > 0 ? round(($totalMarks / $totalMax) * 100, 2) : 0;
        return [
            'total_marks' => $totalMarks,
            'total_max'   => $totalMax,
            'percentage'  => $percentage,
            'grade'       => $this->getGrade($percentage)
        ];
    }
}