<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamResult;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamResultAnalysisController extends Controller
{
    /**
     * Main results dashboard
     */
    public function index(Request $request)
    {
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $exams = Exam::with(['examType', 'classSection'])
                    ->where('is_published', true)
                    ->orderBy('start_date', 'desc')
                    ->get();
        $classSections = ClassSection::with(['class.grade'])->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('admin.exams.results.dashboard', compact(
            'academicSessions',
            'exams',
            'classSections',
            'subjects'
        ));
    }

    /**
     * Student-wise results with multiple tests
     */
    public function studentWise(Request $request)
    {
        // Get all active students for dropdown
        $students = Student::with(['classSection.class.grade'])
                          ->where('status', 'Active')
                          ->orderBy('first_name')
                          ->get();

        $selectedStudent = null;
        $exams = collect();
        $examMarks = collect();
        $subjectSummary = collect();
        $testWiseResults = collect();
        $performanceTrend = collect();
        $sessionId = null;

        if ($request->has('student_id') && $request->student_id) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
            ]);

            $selectedStudent = Student::with(['classSection.class.grade'])
                                     ->findOrFail($request->student_id);

            $sessionId = $request->session_id;
            
            // Get all exams for this student
            $exams = $this->getStudentExams($selectedStudent->id, $sessionId);
            
            // Get all exam marks with details
            $examMarks = $this->getStudentExamMarks($selectedStudent->id, $sessionId);
            
            // Get subject-wise summary across all tests
            $subjectSummary = $this->getStudentSubjectSummary($selectedStudent->id, $sessionId);
            
            // Get test-wise results
            $testWiseResults = $this->getStudentTestWiseResults($selectedStudent->id, $sessionId);
            
            // Calculate overall performance trends
            $performanceTrend = $this->calculatePerformanceTrend($examMarks);
        }

        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();

        return view('admin.exams.results.student-wise', compact(
            'students',
            'selectedStudent',
            'exams',
            'examMarks',
            'subjectSummary',
            'testWiseResults',
            'performanceTrend',
            'sessionId',
            'academicSessions'
        ));
    }

    /**
     * Subject-wise results across sections
     */
    public function subjectWise(Request $request)
    {
        // Get all subjects for dropdown
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        
        $selectedSubject = null;
        $results = collect();
        $sectionWise = collect();
        $studentPerformance = collect();
        $gradeDistribution = [];
        $examId = null;
        $sessionId = null;

        if ($request->has('subject_id') && $request->subject_id) {
            $request->validate([
                'subject_id' => 'required|exists:subjects,id',
            ]);

            $selectedSubject = Subject::findOrFail($request->subject_id);
            $examId = $request->exam_id;
            $sessionId = $request->session_id;

            // Get results for this subject
            $results = $this->getSubjectWiseResults($selectedSubject->id, $examId, $sessionId);
            
            // Get section-wise performance
            $sectionWise = $this->getSubjectSectionWise($selectedSubject->id, $examId, $sessionId);
            
            // Get student-wise performance for this subject
            $studentPerformance = $this->getSubjectStudentPerformance($selectedSubject->id, $examId, $sessionId);
            
            // Get grade distribution
            $gradeDistribution = $this->getSubjectGradeDistribution($selectedSubject->id, $examId, $sessionId);
        }

        $exams = Exam::where('is_published', true)->orderBy('start_date', 'desc')->get();
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();

        return view('admin.exams.results.subject-wise', compact(
            'subjects',
            'selectedSubject',
            'results',
            'sectionWise',
            'studentPerformance',
            'gradeDistribution',
            'examId',
            'sessionId',
            'exams',
            'academicSessions'
        ));
    }

    /**
     * Section-wise results with multiple tests
     */
    public function sectionWise(Request $request)
    {
        // Get all sections for dropdown
        $sections = ClassSection::with(['class.grade'])
                               ->get()
                               ->map(function($section) {
                                   $section->display_name = ($section->class->grade->name ?? '') . ' - ' . $section->section_name;
                                   return $section;
                               })
                               ->sortBy('display_name');

        $selectedSection = null;
        $sectionResults = collect();
        $subjectPerformance = collect();
        $studentResults = collect();
        $testComparison = collect();
        $examId = null;
        $sessionId = null;

        if ($request->has('section_id') && $request->section_id) {
            $request->validate([
                'section_id' => 'required|exists:class_sections,id',
            ]);

            $selectedSection = ClassSection::with(['class.grade'])
                                          ->findOrFail($request->section_id);
            
            $examId = $request->exam_id;
            $sessionId = $request->session_id;

            // Get section exam results
            $sectionResults = $this->getSectionWiseResults($selectedSection->id, $examId, $sessionId);
            
            // Get subject-wise performance for this section
            $subjectPerformance = $this->getSectionSubjectPerformance($selectedSection->id, $examId, $sessionId);
            
            // Get student-wise results for this section
            $studentResults = $this->getSectionStudentResults($selectedSection->id, $examId, $sessionId);
            
            // Get test-wise comparison
            $testComparison = $this->getSectionTestComparison($selectedSection->id, $sessionId);
        }

        $exams = Exam::where('is_published', true)->orderBy('start_date', 'desc')->get();
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();

        return view('admin.exams.results.section-wise', compact(
            'sections',
            'selectedSection',
            'sectionResults',
            'subjectPerformance',
            'studentResults',
            'testComparison',
            'examId',
            'sessionId',
            'exams',
            'academicSessions'
        ));
    }

    /**
     * Compare multiple tests for a student
     */
    public function compareTests(Request $request)
    {
        // Get all active students for dropdown
        $students = Student::with(['classSection.class.grade'])
                          ->where('status', 'Active')
                          ->orderBy('first_name')
                          ->get();

        $selectedStudent = null;
        $testResults = collect();
        $subjectComparison = collect();
        $sessionId = null;

        if ($request->has('student_id') && $request->student_id) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
            ]);

            $selectedStudent = Student::with(['classSection.class.grade'])
                                     ->findOrFail($request->student_id);
            
            $sessionId = $request->session_id;
            
            // Get all test results for this student
            $testResults = $this->getStudentTestComparison($selectedStudent->id, $sessionId);
            
            // Get subject-wise comparison across tests
            $subjectComparison = $this->getStudentSubjectComparison($selectedStudent->id, $sessionId);
        }

        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();

        return view('admin.exams.results.compare-tests', compact(
            'students',
            'selectedStudent',
            'testResults',
            'subjectComparison',
            'sessionId',
            'academicSessions'
        ));
    }

    /**
     * Export results to Excel/CSV
     */
    public function export(Request $request)
    {
        // Export logic
        // ... implementation
    }

    // ===================== PRIVATE METHODS =====================

    private function getStudentExams($studentId, $sessionId = null)
    {
        $query = Exam::select('exams.*')
                    ->join('exam_marks', 'exams.id', '=', 'exam_marks.exam_id')
                    ->where('exam_marks.student_id', $studentId)
                    ->where('exams.is_published', true)
                    ->distinct();

        if ($sessionId) {
            $query->whereHas('classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        return $query->orderBy('exams.start_date', 'desc')->get();
    }

    private function getStudentExamMarks($studentId, $sessionId = null)
    {
        $query = ExamMark::with(['exam', 'subject'])
                        ->where('student_id', $studentId);

        if ($sessionId) {
            $query->whereHas('exam.classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        return $query->orderBy('exam_id')->orderBy('subject_id')->get();
    }

    private function getStudentSubjectSummary($studentId, $sessionId = null)
    {
        $query = ExamMark::select(
                'subjects.id as subject_id',
                'subjects.name as subject_name',
                'subjects.code as subject_code',
                DB::raw('COUNT(DISTINCT exam_marks.exam_id) as test_count'),
                DB::raw('AVG(exam_marks.marks_obtained) as avg_marks'),
                DB::raw('AVG(exam_marks.max_marks) as avg_max_marks'),
                DB::raw('MAX(exam_marks.marks_obtained) as highest_marks'),
                DB::raw('MIN(exam_marks.marks_obtained) as lowest_marks'),
                DB::raw('SUM(CASE WHEN exam_marks.marks_obtained >= exam_marks.passing_marks THEN 1 ELSE 0 END) as passed_count'),
                DB::raw('COUNT(exam_marks.id) as total_attempts')
            )
            ->join('subjects', 'exam_marks.subject_id', '=', 'subjects.id')
            ->where('exam_marks.student_id', $studentId);

        if ($sessionId) {
            $query->whereHas('exam.classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        $results = $query->groupBy('subjects.id', 'subjects.name', 'subjects.code')
                        ->orderBy('subjects.name')
                        ->get();

        foreach ($results as $result) {
            $result->avg_percentage = $result->avg_max_marks > 0 
                ? ($result->avg_marks / $result->avg_max_marks) * 100 
                : 0;
            $result->pass_percentage = $result->total_attempts > 0 
                ? ($result->passed_count / $result->total_attempts) * 100 
                : 0;
        }

        return $results;
    }

    private function getStudentTestWiseResults($studentId, $sessionId = null)
    {
        $query = ExamMark::select(
                'exams.id as exam_id',
                'exams.name as exam_name',
                'exams.start_date',
                DB::raw('SUM(exam_marks.marks_obtained) as total_marks'),
                DB::raw('SUM(exam_marks.max_marks) as total_max_marks'),
                DB::raw('COUNT(exam_marks.id) as subject_count'),
                DB::raw('SUM(CASE WHEN exam_marks.marks_obtained >= exam_marks.passing_marks THEN 1 ELSE 0 END) as passed_subjects'),
                DB::raw('SUM(CASE WHEN exam_marks.marks_obtained < exam_marks.passing_marks THEN 1 ELSE 0 END) as failed_subjects')
            )
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->where('exam_marks.student_id', $studentId)
            ->where('exams.is_published', true);

        if ($sessionId) {
            $query->whereHas('exam.classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        $results = $query->groupBy('exams.id', 'exams.name', 'exams.start_date')
                        ->orderBy('exams.start_date', 'desc')
                        ->get();

        foreach ($results as $result) {
            $result->percentage = $result->total_max_marks > 0 
                ? ($result->total_marks / $result->total_max_marks) * 100 
                : 0;
            $result->pass_percentage = $result->subject_count > 0 
                ? ($result->passed_subjects / $result->subject_count) * 100 
                : 0;
            $result->grade = $this->calculateGrade($result->percentage);
        }

        return $results;
    }

    private function calculatePerformanceTrend($examMarks)
    {
        $trend = [];
        $exams = $examMarks->groupBy('exam_id');
        
        foreach ($exams as $examId => $marks) {
            $total = $marks->sum('marks_obtained');
            $max = $marks->sum('max_marks');
            $percentage = $max > 0 ? ($total / $max) * 100 : 0;
            $examName = $marks->first()->exam->name ?? 'Unknown';
            
            $trend[] = [
                'exam_id' => $examId,
                'exam_name' => $examName,
                'percentage' => $percentage,
                'total_marks' => $total,
                'max_marks' => $max
            ];
        }
        
        return collect($trend)->sortBy('exam_id')->values();
    }

    private function getSubjectWiseResults($subjectId, $examId = null, $sessionId = null)
    {
        $query = ExamMark::select(
                'exam_marks.*',
                'students.first_name',
                'students.last_name',
                'students.admission_number',
                'students.roll_number',
                'class_sections.section_name',
                'classes.grade_id',
                'grades.name as grade_name',
                'exams.name as exam_name'
            )
            ->join('students', 'exam_marks.student_id', '=', 'students.id')
            ->join('class_sections', 'students.class_section_id', '=', 'class_sections.id')
            ->join('classes', 'class_sections.class_id', '=', 'classes.id')
            ->join('grades', 'classes.grade_id', '=', 'grades.id')
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->where('exam_marks.subject_id', $subjectId)
            ->where('exams.is_published', true);

        if ($examId) {
            $query->where('exam_marks.exam_id', $examId);
        }

        if ($sessionId) {
            $query->where('classes.academic_session_id', $sessionId);
        }

        return $query->orderBy('students.roll_number')->get();
    }

    private function getSubjectSectionWise($subjectId, $examId = null, $sessionId = null)
    {
        $query = ExamMark::select(
                'class_sections.id as section_id',
                'class_sections.section_name',
                'grades.name as grade_name',
                DB::raw('COUNT(DISTINCT exam_marks.student_id) as student_count'),
                DB::raw('AVG(exam_marks.marks_obtained) as avg_marks'),
                DB::raw('MAX(exam_marks.marks_obtained) as highest_marks'),
                DB::raw('MIN(exam_marks.marks_obtained) as lowest_marks'),
                DB::raw('SUM(CASE WHEN exam_marks.marks_obtained >= exam_marks.passing_marks THEN 1 ELSE 0 END) as passed_count')
            )
            ->join('students', 'exam_marks.student_id', '=', 'students.id')
            ->join('class_sections', 'students.class_section_id', '=', 'class_sections.id')
            ->join('classes', 'class_sections.class_id', '=', 'classes.id')
            ->join('grades', 'classes.grade_id', '=', 'grades.id')
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->where('exam_marks.subject_id', $subjectId)
            ->where('exams.is_published', true);

        if ($examId) {
            $query->where('exam_marks.exam_id', $examId);
        }

        if ($sessionId) {
            $query->where('classes.academic_session_id', $sessionId);
        }

        $results = $query->groupBy('class_sections.id', 'class_sections.section_name', 'grades.name')
                        ->orderBy('grades.name')
                        ->orderBy('class_sections.section_name')
                        ->get();

        foreach ($results as $result) {
            $result->pass_percentage = $result->student_count > 0 
                ? ($result->passed_count / $result->student_count) * 100 
                : 0;
        }

        return $results;
    }

    private function getSubjectStudentPerformance($subjectId, $examId = null, $sessionId = null)
    {
        $query = ExamMark::select(
                'students.id as student_id',
                'students.first_name',
                'students.last_name',
                'students.admission_number',
                'students.roll_number',
                'class_sections.section_name',
                DB::raw('AVG(exam_marks.marks_obtained) as avg_marks'),
                DB::raw('MAX(exam_marks.marks_obtained) as highest_marks'),
                DB::raw('MIN(exam_marks.marks_obtained) as lowest_marks'),
                DB::raw('COUNT(exam_marks.id) as test_count'),
                DB::raw('SUM(CASE WHEN exam_marks.marks_obtained >= exam_marks.passing_marks THEN 1 ELSE 0 END) as passed_count')
            )
            ->join('students', 'exam_marks.student_id', '=', 'students.id')
            ->join('class_sections', 'students.class_section_id', '=', 'class_sections.id')
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->where('exam_marks.subject_id', $subjectId)
            ->where('exams.is_published', true);

        if ($examId) {
            $query->where('exam_marks.exam_id', $examId);
        }

        if ($sessionId) {
            $query->whereHas('exam.classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        $results = $query->groupBy(
                'students.id',
                'students.first_name',
                'students.last_name',
                'students.admission_number',
                'students.roll_number',
                'class_sections.section_name'
            )
            ->orderBy('students.roll_number')
            ->get();

        foreach ($results as $result) {
            $result->pass_percentage = $result->test_count > 0 
                ? ($result->passed_count / $result->test_count) * 100 
                : 0;
            $result->avg_percentage = $result->avg_marks > 0 
                ? ($result->avg_marks / 100) * 100 
                : 0;
            $result->grade = $this->calculateGrade($result->avg_percentage);
        }

        return $results;
    }

    private function getSubjectGradeDistribution($subjectId, $examId = null, $sessionId = null)
    {
        $grades = ['A+', 'A', 'B+', 'B', 'C', 'D', 'F'];
        $distribution = [];

        foreach ($grades as $grade) {
            $distribution[$grade] = 0;
        }

        $query = ExamMark::select(
                DB::raw('AVG(exam_marks.marks_obtained) as avg_marks'),
                DB::raw('AVG(exam_marks.max_marks) as avg_max_marks'),
                'students.id as student_id'
            )
            ->join('students', 'exam_marks.student_id', '=', 'students.id')
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->where('exam_marks.subject_id', $subjectId)
            ->where('exams.is_published', true);

        if ($examId) {
            $query->where('exam_marks.exam_id', $examId);
        }

        if ($sessionId) {
            $query->whereHas('exam.classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        $results = $query->groupBy('students.id')->get();

        foreach ($results as $result) {
            $percentage = $result->avg_max_marks > 0 
                ? ($result->avg_marks / $result->avg_max_marks) * 100 
                : 0;
            $grade = $this->calculateGrade($percentage);
            if (isset($distribution[$grade])) {
                $distribution[$grade]++;
            }
        }

        return $distribution;
    }

    private function getSectionWiseResults($sectionId, $examId = null, $sessionId = null)
    {
        $query = ExamResult::with(['student', 'exam'])
                          ->where('class_section_id', $sectionId);

        if ($examId) {
            $query->where('exam_id', $examId);
        }

        if ($sessionId) {
            $query->whereHas('exam.classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        return $query->orderBy('student_id')->get();
    }

    private function getSectionSubjectPerformance($sectionId, $examId = null, $sessionId = null)
    {
        $query = ExamMark::select(
                'subjects.id as subject_id',
                'subjects.name as subject_name',
                'subjects.code as subject_code',
                DB::raw('COUNT(DISTINCT exam_marks.student_id) as student_count'),
                DB::raw('AVG(exam_marks.marks_obtained) as avg_marks'),
                DB::raw('MAX(exam_marks.marks_obtained) as highest_marks'),
                DB::raw('MIN(exam_marks.marks_obtained) as lowest_marks'),
                DB::raw('SUM(CASE WHEN exam_marks.marks_obtained >= exam_marks.passing_marks THEN 1 ELSE 0 END) as passed_count')
            )
            ->join('subjects', 'exam_marks.subject_id', '=', 'subjects.id')
            ->join('students', 'exam_marks.student_id', '=', 'students.id')
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->where('students.class_section_id', $sectionId)
            ->where('exams.is_published', true);

        if ($examId) {
            $query->where('exam_marks.exam_id', $examId);
        }

        if ($sessionId) {
            $query->whereHas('exam.classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        $results = $query->groupBy('subjects.id', 'subjects.name', 'subjects.code')
                        ->orderBy('subjects.name')
                        ->get();

        foreach ($results as $result) {
            $result->pass_percentage = $result->student_count > 0 
                ? ($result->passed_count / $result->student_count) * 100 
                : 0;
            $result->avg_percentage = $result->avg_marks > 0 
                ? ($result->avg_marks / 100) * 100 
                : 0;
        }

        return $results;
    }

    private function getSectionStudentResults($sectionId, $examId = null, $sessionId = null)
    {
        $query = ExamResult::with(['student', 'exam'])
                          ->where('class_section_id', $sectionId);

        if ($examId) {
            $query->where('exam_id', $examId);
        }

        if ($sessionId) {
            $query->whereHas('exam.classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        return $query->orderBy('student_id')->get();
    }

    private function getSectionTestComparison($sectionId, $sessionId = null)
    {
        $query = ExamResult::select(
                'exams.id as exam_id',
                'exams.name as exam_name',
                'exams.start_date',
                DB::raw('COUNT(exam_results.id) as student_count'),
                DB::raw('AVG(exam_results.percentage) as avg_percentage'),
                DB::raw('MAX(exam_results.percentage) as highest_percentage'),
                DB::raw('MIN(exam_results.percentage) as lowest_percentage'),
                DB::raw('SUM(CASE WHEN exam_results.percentage >= 40 THEN 1 ELSE 0 END) as passed_count')
            )
            ->join('exams', 'exam_results.exam_id', '=', 'exams.id')
            ->where('exam_results.class_section_id', $sectionId)
            ->where('exams.is_published', true);

        if ($sessionId) {
            $query->whereHas('exam.classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        $results = $query->groupBy('exams.id', 'exams.name', 'exams.start_date')
                        ->orderBy('exams.start_date', 'desc')
                        ->get();

        foreach ($results as $result) {
            $result->pass_percentage = $result->student_count > 0 
                ? ($result->passed_count / $result->student_count) * 100 
                : 0;
        }

        return $results;
    }

    private function getStudentTestComparison($studentId, $sessionId = null)
    {
        $query = ExamMark::select(
                'exams.id as exam_id',
                'exams.name as exam_name',
                'exams.start_date',
                DB::raw('SUM(exam_marks.marks_obtained) as total_marks'),
                DB::raw('SUM(exam_marks.max_marks) as total_max_marks'),
                DB::raw('COUNT(exam_marks.id) as subject_count')
            )
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->where('exam_marks.student_id', $studentId)
            ->where('exams.is_published', true);

        if ($sessionId) {
            $query->whereHas('exam.classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        $results = $query->groupBy('exams.id', 'exams.name', 'exams.start_date')
                        ->orderBy('exams.start_date', 'desc')
                        ->get();

        foreach ($results as $result) {
            $result->percentage = $result->total_max_marks > 0 
                ? ($result->total_marks / $result->total_max_marks) * 100 
                : 0;
            $result->grade = $this->calculateGrade($result->percentage);
        }

        return $results;
    }

    private function getStudentSubjectComparison($studentId, $sessionId = null)
    {
        $query = ExamMark::select(
                'subjects.id as subject_id',
                'subjects.name as subject_name',
                'subjects.code as subject_code',
                DB::raw('AVG(exam_marks.marks_obtained) as avg_marks'),
                DB::raw('AVG(exam_marks.max_marks) as avg_max_marks'),
                DB::raw('COUNT(exam_marks.id) as test_count'),
                DB::raw('MAX(exam_marks.marks_obtained) as highest_marks'),
                DB::raw('MIN(exam_marks.marks_obtained) as lowest_marks')
            )
            ->join('subjects', 'exam_marks.subject_id', '=', 'subjects.id')
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->where('exam_marks.student_id', $studentId)
            ->where('exams.is_published', true);

        if ($sessionId) {
            $query->whereHas('exam.classSection.class', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        $results = $query->groupBy('subjects.id', 'subjects.name', 'subjects.code')
                        ->orderBy('subjects.name')
                        ->get();

        foreach ($results as $result) {
            $result->avg_percentage = $result->avg_max_marks > 0 
                ? ($result->avg_marks / $result->avg_max_marks) * 100 
                : 0;
            $result->grade = $this->calculateGrade($result->avg_percentage);
        }

        return $results;
    }

    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }
}