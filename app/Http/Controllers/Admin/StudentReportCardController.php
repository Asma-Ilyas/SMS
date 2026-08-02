<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\StudentAttendance;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\AcademicSession;
use App\Models\Classes;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentReportCardController extends Controller
{
    /**
     * Show the report card form
     */
    public function index(Request $request)
    {
        $students = Student::with(['classSection.class.grade', 'classSection.class'])
                           ->where('status', 'Active')
                           ->orderBy('first_name')
                           ->get();

        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();
        
        $exams = Exam::with(['examType', 'classSection'])
                    ->where('is_published', true)
                    ->orderBy('start_date', 'desc')
                    ->get();

        return view('admin.exams.reports.student-report-card', compact(
            'students',
            'academicSessions',
            'exams'
        ));
    }

    /**
     * Generate and display the report card
     */
    public function generate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'session_id' => 'nullable|exists:academic_sessions,id',
            'exam_id' => 'nullable|exists:exams,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $student = Student::with([
            'classSection.class.grade',
            'classSection.class.academicSession'
        ])->findOrFail($request->student_id);

        $sessionId = $request->session_id;
        $examId = $request->exam_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        // Get academic session
        $academicSession = null;
        if ($sessionId) {
            $academicSession = AcademicSession::find($sessionId);
        } else {
            $academicSession = AcademicSession::where('is_active', true)->first();
        }

        // Get exams for the student
        $exams = $this->getStudentExams($student->id, $sessionId, $examId);
        
        // Get exam marks
        $examMarks = $this->getStudentExamMarks($student->id, $sessionId, $examId);
        
        // Get subject-wise results
        $subjectResults = $this->getSubjectWiseResults($student->id, $sessionId, $examId);
        
        // Get attendance data with date range
        $attendanceData = $this->getStudentAttendance($student->id, $dateFrom, $dateTo);
        
        // Calculate overall performance
        $overallPerformance = $this->calculateOverallPerformance($examMarks);
        
        // Get grade scale
        $gradeScale = $this->getGradeScale();

        // Get date range display text
        $dateRangeText = $this->getDateRangeText($dateFrom, $dateTo, $academicSession);

        return view('admin.exams.reports.student-report-card-view', compact(
            'student',
            'academicSession',
            'exams',
            'examMarks',
            'subjectResults',
            'attendanceData',
            'overallPerformance',
            'gradeScale',
            'dateFrom',
            'dateTo',
            'dateRangeText'
        ));
    }

    /**
     * Get student exams
     */
    private function getStudentExams($studentId, $sessionId = null, $examId = null)
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

        if ($examId) {
            $query->where('exams.id', $examId);
        }

        return $query->orderBy('exams.start_date', 'desc')->get();
    }

    /**
     * Get student exam marks
     */
    private function getStudentExamMarks($studentId, $sessionId = null, $examId = null)
    {
        $query = ExamMark::with(['exam', 'subject'])
                        ->where('student_id', $studentId);

        if ($examId) {
            $query->where('exam_id', $examId);
        } else {
            $query->whereHas('exam', function($q) use ($sessionId) {
                $q->where('is_published', true);
                if ($sessionId) {
                    $q->whereHas('classSection.class', function($cq) use ($sessionId) {
                        $cq->where('academic_session_id', $sessionId);
                    });
                }
            });
        }

        return $query->orderBy('exam_id')->orderBy('subject_id')->get();
    }

    /**
     * Get subject-wise results
     */
    private function getSubjectWiseResults($studentId, $sessionId = null, $examId = null)
    {
        $query = ExamMark::select(
                'subjects.id as subject_id',
                'subjects.name as subject_name',
                'subjects.code as subject_code',
                DB::raw('AVG(exam_marks.marks_obtained) as average_marks'),
                DB::raw('AVG(exam_marks.max_marks) as average_max_marks'),
                DB::raw('COUNT(exam_marks.id) as exam_count'),
                DB::raw('SUM(CASE WHEN exam_marks.marks_obtained >= exam_marks.passing_marks THEN 1 ELSE 0 END) as passed_count'),
                DB::raw('SUM(CASE WHEN exam_marks.marks_obtained < exam_marks.passing_marks THEN 1 ELSE 0 END) as failed_count'),
                DB::raw('MAX(exam_marks.marks_obtained) as highest_marks'),
                DB::raw('MIN(exam_marks.marks_obtained) as lowest_marks')
            )
            ->join('subjects', 'exam_marks.subject_id', '=', 'subjects.id')
            ->where('exam_marks.student_id', $studentId)
            ->whereHas('exam', function($q) use ($sessionId, $examId) {
                $q->where('is_published', true);
                if ($examId) {
                    $q->where('id', $examId);
                }
                if ($sessionId) {
                    $q->whereHas('classSection.class', function($cq) use ($sessionId) {
                        $cq->where('academic_session_id', $sessionId);
                    });
                }
            })
            ->groupBy('subjects.id', 'subjects.name', 'subjects.code')
            ->orderBy('subjects.name')
            ->get();

        foreach ($query as $subject) {
            $subject->pass_percentage = $subject->exam_count > 0 
                ? ($subject->passed_count / $subject->exam_count) * 100 
                : 0;
            $subject->average_percentage = $subject->average_max_marks > 0 
                ? ($subject->average_marks / $subject->average_max_marks) * 100 
                : 0;
        }

        return $query;
    }

    /**
     * Get student attendance with date range filtering
     */
    /**
 * Get student attendance with date range filtering - UPDATED
 */
private function getStudentAttendance($studentId, $dateFrom = null, $dateTo = null)
{
    $query = StudentAttendance::where('student_id', $studentId);

    // Apply date range filters
    if ($dateFrom) {
        $query->where('date', '>=', $dateFrom);
    }
    if ($dateTo) {
        $query->where('date', '<=', $dateTo);
    }

    $attendance = $query->orderBy('date', 'asc')->get();

    // If no records, return empty data
    if ($attendance->isEmpty()) {
        return [
            'total_days' => 0,
            'present_days' => 0,
            'absent_days' => 0,
            'leave_days' => 0,
            'percentage' => 0,
            'monthly' => [],
            'daily' => [],
            'records' => collect([]),
            'has_data' => false,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];
    }

    // Initialize counters
    $totalDays = $attendance->count();
    $presentDays = 0;
    $absentDays = 0;
    $leaveDays = 0;
    $monthlyData = [];
    $dailyData = [];

    // Process each record
    foreach ($attendance as $record) {
        $status = strtolower(trim($record->status));
        $date = Carbon::parse($record->date);
        
        // Count by status - Only present, absent, leave
        if ($status === 'present') {
            $presentDays++;
        } elseif ($status === 'absent') {
            $absentDays++;
        } elseif ($status === 'leave') {
            $leaveDays++;
        }

        // Daily data
        $dailyData[$date->format('Y-m-d')] = [
            'date' => $date->format('d M Y'),
            'status' => $status,
            'status_display' => ucfirst($status),
            'day' => $date->format('l'),
            'leave_reason' => $record->leave_reason ?? null
        ];

        // Monthly breakdown
        $month = $date->format('F Y');
        if (!isset($monthlyData[$month])) {
            $monthlyData[$month] = [
                'total' => 0,
                'present' => 0,
                'absent' => 0,
                'leave' => 0,
                'percentage' => 0
            ];
        }
        
        $monthlyData[$month]['total']++;
        if ($status === 'present') {
            $monthlyData[$month]['present']++;
        } elseif ($status === 'absent') {
            $monthlyData[$month]['absent']++;
        } elseif ($status === 'leave') {
            $monthlyData[$month]['leave']++;
        }
    }

    // Calculate monthly percentages
    foreach ($monthlyData as $month => &$data) {
        $attended = $data['present'] + $data['leave']; // Leave is considered as attended
        $data['percentage'] = $data['total'] > 0 
            ? round(($attended / $data['total']) * 100, 2)
            : 0;
    }

    // Calculate overall percentage - Present + Leave are considered as attended
    $attendedDays = $presentDays + $leaveDays;
    $percentage = $totalDays > 0 
        ? round(($attendedDays / $totalDays) * 100, 2)
        : 0;

    // Get date range display
    $dateRangeDisplay = $this->getDateRangeDisplay($dateFrom, $dateTo);

    return [
        'total_days' => $totalDays,
        'present_days' => $presentDays,
        'absent_days' => $absentDays,
        'leave_days' => $leaveDays,
        'percentage' => $percentage,
        'monthly' => $monthlyData,
        'daily' => $dailyData,
        'records' => $attendance,
        'has_data' => true,
        'date_from' => $dateFrom,
        'date_to' => $dateTo,
        'date_range_display' => $dateRangeDisplay
    ];
}
    
    private function getDateRangeDisplay($dateFrom, $dateTo)
    {
        if ($dateFrom && $dateTo) {
            return Carbon::parse($dateFrom)->format('d M Y') . ' - ' . Carbon::parse($dateTo)->format('d M Y');
        } elseif ($dateFrom) {
            return 'From ' . Carbon::parse($dateFrom)->format('d M Y');
        } elseif ($dateTo) {
            return 'Until ' . Carbon::parse($dateTo)->format('d M Y');
        }
        return 'All Time';
    }

    /**
     * Get date range text for display
     */
    private function getDateRangeText($dateFrom, $dateTo, $academicSession)
    {
        if ($dateFrom && $dateTo) {
            return Carbon::parse($dateFrom)->format('d M Y') . ' to ' . Carbon::parse($dateTo)->format('d M Y');
        } elseif ($dateFrom) {
            return 'From ' . Carbon::parse($dateFrom)->format('d M Y');
        } elseif ($dateTo) {
            return 'Until ' . Carbon::parse($dateTo)->format('d M Y');
        } elseif ($academicSession) {
            return $academicSession->name;
        }
        return 'All Time';
    }

    /**
     * Calculate overall performance
     */
    private function calculateOverallPerformance($examMarks)
    {
        if ($examMarks->isEmpty()) {
            return [
                'total_marks' => 0,
                'total_max_marks' => 0,
                'average_percentage' => 0,
                'total_exams' => 0,
                'total_subjects' => 0,
                'passed_exams' => 0,
                'failed_exams' => 0,
                'overall_grade' => 'N/A'
            ];
        }

        $totalMarks = $examMarks->sum('marks_obtained');
        $totalMaxMarks = $examMarks->sum('max_marks');
        $totalExams = $examMarks->groupBy('exam_id')->count();
        $totalSubjects = $examMarks->groupBy('subject_id')->count();
        
        $passedExams = $examMarks->filter(function($mark) {
            return $mark->marks_obtained >= $mark->passing_marks;
        })->count();
        
        $failedExams = $examMarks->filter(function($mark) {
            return $mark->marks_obtained < $mark->passing_marks;
        })->count();

        $averagePercentage = $totalMaxMarks > 0 
            ? ($totalMarks / $totalMaxMarks) * 100 
            : 0;

        $grade = $this->calculateGrade($averagePercentage);

        return [
            'total_marks' => $totalMarks,
            'total_max_marks' => $totalMaxMarks,
            'average_percentage' => $averagePercentage,
            'total_exams' => $totalExams,
            'total_subjects' => $totalSubjects,
            'passed_exams' => $passedExams,
            'failed_exams' => $failedExams,
            'overall_grade' => $grade
        ];
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

    /**
     * Get grade scale
     */
    private function getGradeScale()
    {
        return [
            ['grade' => 'A+', 'min' => 90, 'max' => 100, 'description' => 'Excellent'],
            ['grade' => 'A', 'min' => 80, 'max' => 89, 'description' => 'Very Good'],
            ['grade' => 'B+', 'min' => 70, 'max' => 79, 'description' => 'Good'],
            ['grade' => 'B', 'min' => 60, 'max' => 69, 'description' => 'Satisfactory'],
            ['grade' => 'C', 'min' => 50, 'max' => 59, 'description' => 'Average'],
            ['grade' => 'D', 'min' => 40, 'max' => 49, 'description' => 'Below Average'],
            ['grade' => 'F', 'min' => 0, 'max' => 39, 'description' => 'Fail']
        ];
    }

    /**
     * Export report card as PDF
     */
    public function exportPDF(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::with([
            'classSection.class.grade',
            'classSection.class.academicSession'
        ])->findOrFail($request->student_id);

        $sessionId = $request->session_id;
        $examId = $request->exam_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $academicSession = null;
        if ($sessionId) {
            $academicSession = AcademicSession::find($sessionId);
        } else {
            $academicSession = AcademicSession::where('is_active', true)->first();
        }

        $exams = $this->getStudentExams($student->id, $sessionId, $examId);
        $examMarks = $this->getStudentExamMarks($student->id, $sessionId, $examId);
        $subjectResults = $this->getSubjectWiseResults($student->id, $sessionId, $examId);
        $attendanceData = $this->getStudentAttendance($student->id, $dateFrom, $dateTo);
        $overallPerformance = $this->calculateOverallPerformance($examMarks);
        $gradeScale = $this->getGradeScale();
        $dateRangeText = $this->getDateRangeText($dateFrom, $dateTo, $academicSession);

        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.exams.reports.student-report-card-pdf', compact(
                'student',
                'academicSession',
                'exams',
                'examMarks',
                'subjectResults',
                'attendanceData',
                'overallPerformance',
                'gradeScale',
                'dateRangeText'
            ));
            return $pdf->download('report-card-' . $student->first_name . '-' . $student->last_name . '.pdf');
        }

        return view('admin.exams.reports.student-report-card-pdf', compact(
            'student',
            'academicSession',
            'exams',
            'examMarks',
            'subjectResults',
            'attendanceData',
            'overallPerformance',
            'gradeScale',
            'dateRangeText'
        ));
    }
}