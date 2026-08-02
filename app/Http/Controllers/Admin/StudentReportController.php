<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\StudentAttendance;
use App\Models\ExamResult;
use App\Models\ExamMark;
use App\Models\StudentFeeInstallment;
use App\Models\Invoice;
use App\Models\AcademicSession;
use App\Models\GradeScale;
use App\Models\TimetableEntry;
use App\Models\TimeSlot;
use App\Models\CertificateDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentReportController extends Controller
{
    public function index()
    {
        $classSections = ClassSection::with('class.grade')->get();
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $students = Student::where('status', 'Active')->orderBy('first_name')->get();
        
        return view('admin.reports.student.dashboard', compact('classSections', 'academicSessions', 'students'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'session_id' => 'nullable|exists:academic_sessions,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $student = Student::with([
            'classSection.class.grade',
            'classSection.class.stream',
            'classSection.class.academicSession',
            'attendances' => function($q) use ($request) {
                $q->when($request->date_from, function($query) use ($request) {
                    return $query->whereDate('date', '>=', $request->date_from);
                })->when($request->date_to, function($query) use ($request) {
                    return $query->whereDate('date', '<=', $request->date_to);
                })->orderBy('date', 'desc');
            },
            'examResults' => function($q) use ($request) {
                $q->when($request->session_id, function($query) use ($request) {
                    return $query->whereHas('exam.classSection.class', function($cq) use ($request) {
                        $cq->where('academic_session_id', $request->session_id);
                    });
                })->orderBy('created_at', 'desc');
            },
            'examMarks.subject',
            'feeInstallments.feeSubmissionType',
            'invoices.bank',
            'certificates.certificateType'
        ])->findOrFail($request->student_id);

        $sessionId = $request->session_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $academicSession = $sessionId ? AcademicSession::find($sessionId) : AcademicSession::where('is_active', true)->first();

        // Calculate all statistics
        $stats = $this->calculateStats($student);
        $attendanceSummary = $this->getAttendanceSummary($student, $dateFrom, $dateTo);
        $attendanceMonthly = $this->getAttendanceMonthly($student, $dateFrom, $dateTo);
        $examPerformance = $this->getExamPerformance($student, $sessionId);
        $subjectPerformance = $this->getSubjectPerformance($student, $sessionId);
        $feeSummary = $this->getFeeSummary($student);
        $feeInstallments = $this->getFeeInstallments($student);
        
        // NEW: Get all fee installments for history
        $allFeeInstallments = $student->feeInstallments()->with('feeSubmissionType')
            ->orderBy('due_date', 'desc')
            ->get();

        // NEW: Get payment history
        $paymentHistory = $student->feeInstallments()
            ->where('status', 'paid')
            ->whereNotNull('payment_date')
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->payment_date,
                    'amount' => $item->paid_amount,
                    'type' => $item->feeSubmissionType->name ?? 'N/A',
                    'receipt' => $item->receipt_number,
                ];
            });

        // NEW: Get timetable for student's class
        $timetable = TimetableEntry::with(['subject', 'teacher', 'room', 'timeSlot', 'classSection'])
            ->where('class_section_id', $student->class_section_id)
            ->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->get()
            ->groupBy('day_of_week');

        $timeSlots = TimeSlot::where('is_active', true)
            ->where('type', 'period')
            ->orderBy('sort_order')
            ->get();

        $gradeScale = GradeScale::where('is_default', true)->first();
        $overallPercentage = $stats['avg_percentage'];
        $overallGrade = $this->calculateGrade($overallPercentage);
        $dateRangeText = $this->getDateRangeText($dateFrom, $dateTo);
        
        // Get certificates
        $certificates = $student->certificates()->with('certificateType')->get();

        return view('admin.reports.student.complete', compact(
            'student',
            'academicSession',
            'stats',
            'attendanceSummary',
            'attendanceMonthly',
            'examPerformance',
            'subjectPerformance',
            'feeSummary',
            'feeInstallments',
            'allFeeInstallments',
            'paymentHistory',
            'timetable',
            'timeSlots',
            'gradeScale',
            'overallPercentage',
            'overallGrade',
            'certificates',
            'dateFrom',
            'dateTo',
            'dateRangeText',
            'sessionId'
        ));
    }

    public function exportPDF(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::with([
            'classSection.class.grade',
            'attendances',
            'examResults',
            'examMarks.subject',
            'feeInstallments'
        ])->findOrFail($request->student_id);

        $sessionId = $request->session_id;
        $academicSession = $sessionId ? AcademicSession::find($sessionId) : AcademicSession::where('is_active', true)->first();

        $stats = $this->calculateStats($student);
        $attendanceSummary = $this->getAttendanceSummary($student);
        $subjectPerformance = $this->getSubjectPerformance($student, $sessionId);
        $feeSummary = $this->getFeeSummary($student);
        $overallPercentage = $stats['avg_percentage'];
        $overallGrade = $this->calculateGrade($overallPercentage);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.student.pdf', compact(
            'student', 'academicSession', 'stats', 'attendanceSummary', 
            'subjectPerformance', 'feeSummary', 'overallPercentage', 'overallGrade'
        ));

        return $pdf->download('student-report-' . $student->admission_number . '.pdf');
    }

    // ========== PRIVATE METHODS ==========

    private function calculateStats($student)
    {
        $totalDays = $student->attendances->count();
        $presentDays = $student->attendances->where('status', 'present')->count();
        $absentDays = $student->attendances->where('status', 'absent')->count();
        $leaveDays = $student->attendances->where('status', 'leave')->count();
        
        $attended = $presentDays + $leaveDays;
        $attendancePercentage = $totalDays > 0 ? round(($attended / $totalDays) * 100, 2) : 0;

        $totalExams = $student->examResults->count();
        $avgPercentage = $student->examResults->avg('percentage') ?? 0;

        $totalFee = $student->feeInstallments->sum('amount');
        $paidFee = $student->feeInstallments->where('status', 'paid')->sum('paid_amount');
        $pendingFee = $totalFee - $paidFee;

        return [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'attendance_percentage' => $attendancePercentage,
            'total_exams' => $totalExams,
            'avg_percentage' => $avgPercentage,
            'total_fee' => $totalFee,
            'paid_fee' => $paidFee,
            'pending_fee' => $pendingFee,
        ];
    }

    private function getAttendanceSummary($student, $dateFrom = null, $dateTo = null)
    {
        $attendances = $student->attendances;
        
        if ($dateFrom) {
            $attendances = $attendances->where('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $attendances = $attendances->where('date', '<=', $dateTo);
        }

        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $leave = $attendances->where('status', 'leave')->count();
        
        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'leave' => $leave,
            'percentage' => $total > 0 ? round((($present + $leave) / $total) * 100, 2) : 0,
        ];
    }

    private function getAttendanceMonthly($student, $dateFrom = null, $dateTo = null)
    {
        $attendances = $student->attendances;
        
        if ($dateFrom) {
            $attendances = $attendances->where('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $attendances = $attendances->where('date', '<=', $dateTo);
        }

        return $attendances->groupBy(function($item) {
            return Carbon::parse($item->date)->format('F Y');
        })->map(function($monthData) {
            $total = $monthData->count();
            $present = $monthData->where('status', 'present')->count();
            $absent = $monthData->where('status', 'absent')->count();
            $leave = $monthData->where('status', 'leave')->count();
            
            return [
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'leave' => $leave,
                'percentage' => $total > 0 ? round((($present + $leave) / $total) * 100, 2) : 0
            ];
        });
    }

    private function getExamPerformance($student, $sessionId = null)
    {
        $results = $student->examResults;
        
        if ($sessionId) {
            $results = $results->filter(function($item) use ($sessionId) {
                return $item->exam->classSection->class->academic_session_id == $sessionId;
            });
        }
        
        return $results->take(10);
    }

    private function getSubjectPerformance($student, $sessionId = null)
    {
        $marks = $student->examMarks;
        
        if ($sessionId) {
            $marks = $marks->filter(function($item) use ($sessionId) {
                return $item->exam->classSection->class->academic_session_id == $sessionId;
            });
        }

        return $marks->groupBy('subject_id')->map(function($items) {
            $subject = $items->first()->subject;
            $avgMarks = $items->avg('marks_obtained');
            $maxMarks = $items->avg('max_marks');
            $percentage = $maxMarks > 0 ? ($avgMarks / $maxMarks) * 100 : 0;
            $passed = $items->filter(function($m) {
                return $m->marks_obtained >= $m->passing_marks;
            })->count();
            $total = $items->count();
            
            return [
                'subject' => $subject,
                'avg_marks' => round($avgMarks, 2),
                'max_marks' => round($maxMarks, 2),
                'percentage' => round($percentage, 2),
                'passed' => $passed,
                'total' => $total,
                'pass_percentage' => $total > 0 ? round(($passed / $total) * 100, 2) : 0,
                'grade' => $this->calculateGrade($percentage),
            ];
        });
    }

    private function getFeeSummary($student)
    {
        $installments = $student->feeInstallments;
        return [
            'total' => $installments->sum('amount'),
            'paid' => $installments->where('status', 'paid')->sum('paid_amount'),
            'pending' => $installments->whereIn('status', ['pending', 'partial'])->sum('amount'),
            'overdue' => $installments->where('status', 'pending')->where('due_date', '<', now())->count(),
            'paid_count' => $installments->where('status', 'paid')->count(),
            'pending_count' => $installments->whereIn('status', ['pending', 'partial'])->count(),
        ];
    }

    private function getFeeInstallments($student)
    {
        return $student->feeInstallments->sortBy('due_date');
    }

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

    private function getDateRangeText($dateFrom, $dateTo)
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
}