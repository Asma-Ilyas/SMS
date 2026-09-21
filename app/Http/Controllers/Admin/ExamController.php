<?php
// app/Http/Controllers/Admin/ExamController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\ExamGroup;
use App\Models\ClassSection;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ExamMark;
use App\Models\ExamResult;
use App\Models\GradeScale;
use App\Models\ExamSubjectSchedule;
use App\Models\ExamSubjectMark; // only used to clean up old rows in destroy()
use App\Models\SubjectAssignment;
use App\Support\PortalAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Display exam dashboard
     */
    public function index(Request $request)
    {
        $query = Exam::with(['examType', 'examGroup', 'classSection']);

        if ($request->filled('exam_type_id')) {
            $query->where('exam_type_id', $request->exam_type_id);
        }
        if ($request->filled('exam_group_id')) {
            $query->where('exam_group_id', $request->exam_group_id);
        }
        if ($request->filled('class_section_id')) {
            $query->where('class_section_id', $request->class_section_id);
        }

        $exams = $query->orderBy('start_date', 'desc')->paginate(15);
        
        $examTypes = ExamType::where('is_active', true)->get();
        $examGroups = ExamGroup::where('is_active', true)->get();
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();

        // Statistics
        $totalExams = Exam::count();
        $publishedExams = Exam::where('is_published', true)->count();
        $upcomingExams = Exam::where('start_date', '>=', now())->count();

        return view('admin.exams.index', compact(
            'exams', 'examTypes', 'examGroups', 'classSections',
            'totalExams', 'publishedExams', 'upcomingExams'
        ));
    }

    /**
     * Show exam creation form
     */
    public function create()
    {
        $examTypes = ExamType::where('is_active', true)->get();
        $examGroups = ExamGroup::where('is_active', true)->get();
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();

        return view('admin.exams.create', compact('examTypes', 'examGroups', 'classSections'));
    }

    /**
     * Store exam with subject-wise marks
     */
    public function store(Request $request)
    {
        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id',
            'exam_group_id' => 'required|exists:exam_groups,id',
            'class_section_id' => 'required|exists:class_sections,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'passing_percentage' => 'nullable|numeric|min:0|max:100',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
            'subject_marks' => 'required|array',
            'subject_marks.*.max_marks' => 'required|numeric|min:1',
            'subject_marks.*.passing_marks' => 'required|numeric|min:0',
        ]);

        // Everything in one transaction: if any step fails, no half-created exam is left behind.
        $exam = DB::transaction(function () use ($request) {
            $exam = Exam::create([
                'exam_type_id' => $request->exam_type_id,
                'exam_group_id' => $request->exam_group_id,
                'class_section_id' => $request->class_section_id,
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
                'is_published' => $request->boolean('is_published'),
                'passing_percentage' => $request->passing_percentage ?? 40,
            ]);

            // One marks row per student and subject. Each row stores that subject's
            // max / passing marks, so no separate per-subject table is needed.
            // (The old code also wrote to exam_subject_marks without a student_id,
            // which is a required column, and that caused the 500 error.)
            $students = Student::where('class_section_id', $request->class_section_id)->get();

            foreach ($students as $student) {
                foreach ($request->subjects as $subjectId) {
                    if (!isset($request->subject_marks[$subjectId])) {
                        continue;
                    }

                    $marks = $request->subject_marks[$subjectId];

                    ExamMark::create([
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                        'subject_id' => $subjectId,
                        'marks_obtained' => 0,
                        'max_marks' => $marks['max_marks'],
                        'passing_marks' => $marks['passing_marks'],
                        'remarks' => 'Not attempted',
                    ]);
                }
            }

            return $exam;
        });

        // If the exam was created already published, tell the students right away
        if ($exam->is_published) {
            $this->notifyExamPublished($exam);
        }

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam created successfully.');
    }

    /**
     * Show exam details
     */
    public function show($id)
    {
        $exam = Exam::with(['examType', 'examGroup', 'classSection', 'marks.student', 'marks.subject'])
            ->findOrFail($id);

        $students = Student::where('class_section_id', $exam->class_section_id)
            ->orderBy('first_name')
            ->get();

        $subjects = SubjectAssignment::where('class_section_id', $exam->class_section_id)
            ->with('subject')
            ->get();

        // Get marks grouped by student_id
        $marks = $exam->marks->groupBy('student_id');

        // Calculate statistics
        $totalStudents = $students->count();
        $totalMarks = $exam->marks->sum('marks_obtained');
        $totalMaxMarks = $exam->marks->sum('max_marks');
        $averagePercentage = $totalMaxMarks > 0 
            ? round(($totalMarks / $totalMaxMarks) * 100, 2) 
            : 0;

        // Subject-wise statistics
        $subjectStats = [];
        foreach ($subjects as $assignment) {
            $subjectMarks = $exam->marks->where('subject_id', $assignment->subject_id);
            $count = $subjectMarks->count();
            $total = $subjectMarks->sum('marks_obtained');
            $avg = $count > 0 ? round($total / $count, 2) : 0;
            $max = $subjectMarks->max('marks_obtained') ?? 0;
            $min = $subjectMarks->min('marks_obtained') ?? 0;

            $subjectStats[] = [
                'subject' => $assignment->subject,
                'count' => $count,
                'total' => $total,
                'average' => $avg,
                'max' => $max,
                'min' => $min,
            ];
        }

        return view('admin.exams.show', compact(
            'exam', 'students', 'subjects', 'subjectStats',
            'totalStudents', 'averagePercentage', 'marks'
        ));
    }

    /**
     * Edit exam
     */
    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        $examTypes = ExamType::where('is_active', true)->get();
        $examGroups = ExamGroup::where('is_active', true)->get();
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();

        return view('admin.exams.edit', compact('exam', 'examTypes', 'examGroups', 'classSections'));
    }

    /**
     * Update exam
     */
    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id',
            'exam_group_id' => 'required|exists:exam_groups,id',
            'class_section_id' => 'required|exists:class_sections,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'passing_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $exam->update($request->all());

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    /**
     * Delete exam
     */
    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        
        // Delete related records
        ExamMark::where('exam_id', $id)->delete();
        ExamResult::where('exam_id', $id)->delete();
        ExamSubjectMark::where('exam_id', $id)->delete();
        $exam->delete();

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }

    /**
     * Marks entry form
     */
   /**
 * Marks entry form
 */
public function marksEntryForm($examId)
{
    $exam = Exam::with(['classSection'])->findOrFail($examId);
    $students = Student::where('class_section_id', $exam->class_section_id)
        ->orderBy('first_name')
        ->get();

    // Get subjects with their relationships
    $subjects = SubjectAssignment::where('class_section_id', $exam->class_section_id)
        ->with('subject')
        ->get()
        ->filter(function($assignment) {
            return $assignment->subject !== null;
        });

    // Get marks - FIX: Use DB query or proper relationship
    $marks = ExamMark::where('exam_id', $examId)
        ->get()
        ->groupBy('student_id');

    // If marks is empty, create an empty collection
    if (!$marks) {
        $marks = collect();
    }

    return view('admin.exams.marks-entry', compact(
        'exam', 'students', 'subjects', 'marks'
    ));
}

    /**
     * Store marks.
     * Uses the max / passing marks that were set for each student's subject when the
     * exam was created (falls back to 100 / 40), instead of overwriting them.
     */
    public function storeMarks(Request $request, $examId)
    {
        $request->validate([
            'marks' => 'required|array',
            'marks.*.*' => 'nullable|numeric|min:0',
        ]);

        $existing = ExamMark::where('exam_id', $examId)->get()
            ->keyBy(fn ($m) => $m->student_id . '-' . $m->subject_id);

        // First pass: make sure no mark is above its subject's maximum
        $problems = [];
        foreach ($request->marks as $studentId => $subjectMarks) {
            foreach ($subjectMarks as $subjectId => $marksObtained) {
                if ($marksObtained === null || $marksObtained === '') continue;
                $max = $existing[$studentId . '-' . $subjectId]->max_marks ?? 100;
                if ($marksObtained > $max) {
                    $problems[] = "Marks for student #{$studentId}, subject #{$subjectId} cannot be above {$max}.";
                }
            }
        }
        if ($problems) {
            return back()->withInput()->withErrors($problems);
        }

        // Second pass: save
        foreach ($request->marks as $studentId => $subjectMarks) {
            foreach ($subjectMarks as $subjectId => $marksObtained) {
                $row  = $existing[$studentId . '-' . $subjectId] ?? null;
                $max  = $row->max_marks ?? 100;
                $pass = $row->passing_marks ?? 40;
                $obtained = ($marksObtained === null || $marksObtained === '') ? 0 : $marksObtained;

                ExamMark::updateOrCreate(
                    [
                        'exam_id' => $examId,
                        'student_id' => $studentId,
                        'subject_id' => $subjectId,
                    ],
                    [
                        'marks_obtained' => $obtained,
                        'max_marks' => $max,
                        'passing_marks' => $pass,
                        'remarks' => $obtained >= $pass ? 'Pass' : 'Fail',
                    ]
                );
            }
        }

        // Recalculate results
        $this->calculateResults($examId);

        return redirect()->route('admin.exams.show', $examId)
            ->with('success', 'Marks saved and results calculated.');
    }

    /**
     * Calculate exam results
     */
    public function calculateResults($examId)
    {
        $exam = Exam::findOrFail($examId);
        $students = Student::where('class_section_id', $exam->class_section_id)->get();
        $gradeScale = GradeScale::getDefault();

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

        // Delete old results and insert new ones
        ExamResult::where('exam_id', $examId)->delete();
        ExamResult::insert($results);

        // Calculate ranks
        $this->calculateRanks($examId);

        return redirect()->back()->with('success', 'Results calculated successfully.');
    }

    /**
     * Calculate ranks
     */
    public function calculateRanks($examId)
    {
        $results = ExamResult::where('exam_id', $examId)
            ->orderBy('percentage', 'desc')
            ->get();

        $rank = 1;
        $prevPercentage = null;
        foreach ($results as $index => $result) {
            if ($prevPercentage !== null && $result->percentage < $prevPercentage) {
                $rank = $index + 1;
            }
            ExamResult::where('id', $result->id)->update(['rank_in_class' => $rank]);
            $prevPercentage = $result->percentage;
        }
    }

    /**
     * Publish exam
     */
    public function publish($examId)
    {
        $exam = Exam::findOrFail($examId);
        $wasPublished = (bool) $exam->is_published;

        $exam->update(['is_published' => true]);

        // Only notify when the exam actually changes from unpublished to published
        if (!$wasPublished) {
            $this->notifyExamPublished($exam);
        }

        return redirect()->back()->with('success', 'Exam published successfully.');
    }

    /**
     * Tell every student in the exam's class section that it is published.
     * If marks/results already exist, say the results are out; otherwise announce the exam.
     */
    private function notifyExamPublished(Exam $exam): void
    {
        $hasResults = ExamResult::where('exam_id', $exam->id)->exists();

        if ($hasResults) {
            $title   = 'Exam results published';
            $message = 'Results for ' . $exam->name . ' are now available.';
            $level   = 'success';
        } else {
            $start = $exam->start_date ? \Carbon\Carbon::parse($exam->start_date)->format('d M Y') : null;
            $end   = $exam->end_date ? \Carbon\Carbon::parse($exam->end_date)->format('d M Y') : null;

            $title   = 'Exam published';
            $message = $exam->name . ' has been published' . ($start && $end ? " ({$start} to {$end})." : '.');
            $level   = 'info';
        }

        PortalAlert::toSection(
            $exam->class_section_id,
            $title,
            $message,
            PortalAlert::link('student.exams.index', '/student/exams'),
            'exam',
            $level
        );
    }

    /**
     * Unpublish exam
     */
    public function unpublish($examId)
    {
        $exam = Exam::findOrFail($examId);
        $exam->update(['is_published' => false]);

        return redirect()->back()->with('success', 'Exam unpublished.');
    }

    /**
     * Show student result
     */
    public function showResult($examId, $studentId)
    {
        $exam = Exam::findOrFail($examId);
        $student = Student::findOrFail($studentId);
        $result = ExamResult::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->first();

        $marks = ExamMark::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->with('subject')
            ->get();

        $gradeScale = GradeScale::getDefault();

        return view('admin.exams.student-result', compact(
            'exam', 'student', 'result', 'marks', 'gradeScale'
        ));
    }

    /**
     * Print student result
     */
    public function printResult($examId, $studentId)
    {
        $exam = Exam::with(['classSection', 'marks.subject'])->findOrFail($examId);
        $student = Student::findOrFail($studentId);
        
        return view('admin.exams.print-result', compact('exam', 'student'));
    }
}