<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentFeeSubmission; // ← Changed from StudentFeeInstallment
use App\Models\Student;
use App\Models\ClassSection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display fee reports dashboard
     */
    public function index(Request $request)
    {
        $classSections = ClassSection::with('class.grade')->orderBy('class_id')->orderBy('section_name')->get();
        
        // ── 1. WHOLE-SCHOOL SUMMARY ──────────────────────────────────────────
        // FIXED: Changed from StudentFeeInstallment to StudentFeeSubmission
        $totalDue  = (float) StudentFeeSubmission::sum('amount');
        $totalPaid = (float) StudentFeeSubmission::sum('paid_amount');
        
        // Calculate outstanding amount
        $totalOutstanding = (float) StudentFeeSubmission::whereIn('status', ['pending', 'partial'])
            ->selectRaw("SUM(CASE
                WHEN status = 'pending' THEN amount
                WHEN status = 'partial' THEN amount - paid_amount
                ELSE 0 END) as v")
            ->value('v') ?? 0;

        // Calculate overdue amount
        $totalOverdue = (float) StudentFeeSubmission::whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<', Carbon::today())
            ->selectRaw("SUM(CASE
                WHEN status = 'pending' THEN amount
                WHEN status = 'partial' THEN amount - paid_amount
                ELSE 0 END) as v")
            ->value('v') ?? 0;

        // ── 2. CLASS-WISE BREAKDOWN ───────────────────────────────────────────
        $classWiseData = [];
        foreach ($classSections as $section) {
            $studentIds = Student::where('class_section_id', $section->id)->pluck('id');
            
            if ($studentIds->isEmpty()) {
                continue;
            }
            
            $sectionDue = (float) StudentFeeSubmission::whereIn('student_id', $studentIds)->sum('amount');
            $sectionPaid = (float) StudentFeeSubmission::whereIn('student_id', $studentIds)->sum('paid_amount');
            $sectionOutstanding = $sectionDue - $sectionPaid;
            
            $classWiseData[] = [
                'section' => $section,
                'due' => $sectionDue,
                'paid' => $sectionPaid,
                'outstanding' => $sectionOutstanding,
                'collection_percentage' => $sectionDue > 0 ? round(($sectionPaid / $sectionDue) * 100, 2) : 0,
                'student_count' => $studentIds->count(),
            ];
        }

        // ── 3. STUDENT-WISE DETAILS ───────────────────────────────────────────
        $query = StudentFeeSubmission::with(['student', 'feeSubmissionType']);
        
        if ($request->filled('class_section_id')) {
            $studentIds = Student::where('class_section_id', $request->class_section_id)->pluck('id');
            $query->whereIn('student_id', $studentIds);
        }
        
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $studentWise = $query->orderBy('due_date', 'desc')->paginate(20);

        // ── 4. FILTER OPTIONS ──────────────────────────────────────────────────
        $students = collect();
        if ($request->filled('class_section_id')) {
            $students = Student::where('class_section_id', $request->class_section_id)
                ->orderBy('first_name')
                ->get();
        }

        // ── 5. MONTHLY COLLECTION TREND ──────────────────────────────────────
        $monthlyData = StudentFeeSubmission::select(
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                DB::raw('SUM(paid_amount) as collected')
            )
            ->whereNotNull('payment_date')
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // ── 6. STATUS BREAKDOWN ───────────────────────────────────────────────
        $statusBreakdown = [
            'paid' => StudentFeeSubmission::where('status', 'paid')->count(),
            'partial' => StudentFeeSubmission::where('status', 'partial')->count(),
            'pending' => StudentFeeSubmission::where('status', 'pending')->count(),
        ];

        return view('admin.fee-reports.index', compact(
            'classSections',
            'totalDue',
            'totalPaid',
            'totalOutstanding',
            'totalOverdue',
            'classWiseData',
            'studentWise',
            'students',
            'monthlyData',
            'statusBreakdown'
        ));
    }

    /**
     * Show detailed report for a specific student
     */
    public function studentDetails($studentId)
    {
        $student = Student::with(['classSection.class.grade'])->findOrFail($studentId);
        
        $installments = StudentFeeSubmission::where('student_id', $studentId)
            ->with(['feeSubmissionType'])
            ->orderBy('due_date')
            ->get();
        
        $summary = [
            'total_due' => $installments->sum('amount'),
            'total_paid' => $installments->sum('paid_amount'),
            'outstanding' => $installments->sum('amount') - $installments->sum('paid_amount'),
            'total_installments' => $installments->count(),
            'paid_installments' => $installments->where('status', 'paid')->count(),
            'pending_installments' => $installments->where('status', 'pending')->count(),
            'partial_installments' => $installments->where('status', 'partial')->count(),
        ];
        
        return view('admin.fee-reports.student', compact('student', 'installments', 'summary'));
    }

    /**
     * Export fee report to CSV
     */
    public function export(Request $request)
    {
        $query = StudentFeeSubmission::with(['student', 'feeSubmissionType']);
        
        if ($request->filled('class_section_id')) {
            $studentIds = Student::where('class_section_id', $request->class_section_id)->pluck('id');
            $query->whereIn('student_id', $studentIds);
        }
        
        $data = $query->orderBy('due_date')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=fee_report_' . date('Y-m-d') . '.csv',
        ];

        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            
            // Headers
            fputcsv($handle, [
                'Student Name',
                'Admission Number',
                'Fee Type',
                'Installment',
                'Amount',
                'Paid Amount',
                'Status',
                'Due Date',
                'Payment Date'
            ]);

            foreach ($data as $item) {
                fputcsv($handle, [
                    $item->student->full_name ?? 'N/A',
                    $item->student->admission_number ?? 'N/A',
                    $item->feeSubmissionType->name ?? 'N/A',
                    $item->installment_number ?? 1,
                    number_format($item->amount, 2),
                    number_format($item->paid_amount, 2),
                    ucfirst($item->status),
                    $item->due_date ? $item->due_date->format('d-m-Y') : 'N/A',
                    $item->payment_date ? $item->payment_date->format('d-m-Y') : 'N/A',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}