<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classes;
use App\Models\StudentFeeInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // =========================================================================
    // SINGLE SOURCE OF TRUTH — student_fee_installments only.
    //
    // Definitions (identical across index, class breakdown, student list,
    // and student detail — so every number always cross-checks):
    //
    //   total_due      = SUM(amount)
    //   total_paid     = SUM(paid_amount)
    //   outstanding    = SUM(remaining)   where per-row remaining is:
    //                      status=paid    → 0
    //                      status=partial → amount - paid_amount
    //                      status=pending → amount
    //   overdue        = outstanding WHERE due_date < today
    //   collection_pct = total_paid / total_due × 100
    //
    // Note: total_paid + outstanding = total_due  ✓  (always balances)
    // =========================================================================

    public function index(Request $request)
    {
        $selectedClassId = $request->integer('class_id') ?: null;
        $selectedSection = $request->get('section')      ?: null;

        $classes  = Classes::orderBy('id')->get();
        $sections = collect();

        // ── 1. WHOLE-SCHOOL SUMMARY ──────────────────────────────────────────
        $totalDue  = (float) StudentFeeInstallment::sum('amount');
        $totalPaid = (float) StudentFeeInstallment::sum('paid_amount');

        $totalOutstanding = (float) StudentFeeInstallment::whereIn('status', ['pending', 'partial'])
            ->selectRaw("SUM(CASE
                WHEN status = 'pending' THEN amount
                WHEN status = 'partial' THEN amount - paid_amount
                ELSE 0 END) as v")
            ->value('v');

        $totalOverdue = (float) StudentFeeInstallment::whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<', Carbon::today())
            ->selectRaw("SUM(CASE
                WHEN status = 'pending' THEN amount
                WHEN status = 'partial' THEN amount - paid_amount
                ELSE 0 END) as v")
            ->value('v');

        $collectionRate = $totalDue > 0 ? round($totalPaid / $totalDue * 100, 1) : 0;

        // ── 2. CLASS / SECTION BREAKDOWN ─────────────────────────────────────
        $reportData = [];

        $classListQuery = Classes::orderBy('id');
        if ($selectedClassId) {
            $classListQuery->where('id', $selectedClassId);
        }

        foreach ($classListQuery->get() as $class) {
            $studentIds = Student::where('class_id', $class->id)
                ->when($selectedSection, fn($q) => $q->where('section', $selectedSection))
                ->pluck('id');

            if ($studentIds->isEmpty()) continue;

            $classDue = (float) StudentFeeInstallment::whereIn('student_id', $studentIds)
                ->sum('amount');

            $classPaid = (float) StudentFeeInstallment::whereIn('student_id', $studentIds)
                ->sum('paid_amount');

            $classOutstanding = (float) StudentFeeInstallment::whereIn('student_id', $studentIds)
                ->whereIn('status', ['pending', 'partial'])
                ->selectRaw("SUM(CASE
                    WHEN status = 'pending' THEN amount
                    WHEN status = 'partial' THEN amount - paid_amount
                    ELSE 0 END) as v")
                ->value('v');

            $reportData[] = [
                'class_id'       => $class->id,
                'class'          => $class->full_name,
                'section'        => $selectedSection ?: 'All Sections',
                'students'       => $studentIds->count(),
                'total_due'      => $classDue,
                'paid'           => $classPaid,
                'outstanding'    => $classOutstanding,
                'collection_pct' => $classDue > 0 ? round($classPaid / $classDue * 100, 1) : 0,
            ];
        }

        // ── 3. STUDENT LIST (only when a class is selected) ──────────────────
        $studentRows = collect();

        if ($selectedClassId) {
            $sections = Student::where('class_id', $selectedClassId)
                ->distinct()->orderBy('section')->pluck('section');

            $studentIds = Student::where('class_id', $selectedClassId)
                ->when($selectedSection, fn($q) => $q->where('section', $selectedSection))
                ->pluck('id');

            // 4 batch queries — no N+1
            $dueMap = StudentFeeInstallment::whereIn('student_id', $studentIds)
                ->select('student_id', DB::raw('SUM(amount) as v'))
                ->groupBy('student_id')->pluck('v', 'student_id');

            $paidMap = StudentFeeInstallment::whereIn('student_id', $studentIds)
                ->select('student_id', DB::raw('SUM(paid_amount) as v'))
                ->groupBy('student_id')->pluck('v', 'student_id');

            $outstandingMap = StudentFeeInstallment::whereIn('student_id', $studentIds)
                ->whereIn('status', ['pending', 'partial'])
                ->select('student_id', DB::raw("SUM(CASE
                    WHEN status = 'pending' THEN amount
                    WHEN status = 'partial' THEN amount - paid_amount
                    ELSE 0 END) as v"))
                ->groupBy('student_id')->pluck('v', 'student_id');

            $lastPayMap = StudentFeeInstallment::whereIn('student_id', $studentIds)
                ->whereNotNull('payment_date')
                ->select('student_id', DB::raw('MAX(payment_date) as v'))
                ->groupBy('student_id')->pluck('v', 'student_id');

            $studentRows = Student::where('class_id', $selectedClassId)
                ->when($selectedSection, fn($q) => $q->where('section', $selectedSection))
                ->orderBy('section')->orderBy('roll_number')
                ->get()
                ->map(function (Student $s) use ($dueMap, $paidMap, $outstandingMap, $lastPayMap) {
                    $due         = (float) ($dueMap[$s->id]         ?? 0);
                    $paid        = (float) ($paidMap[$s->id]        ?? 0);
                    $outstanding = (float) ($outstandingMap[$s->id] ?? 0);
                    return [
                        'id'             => $s->id,
                        'full_name'      => $s->full_name,
                        'roll_number'    => $s->roll_number       ?? '-',
                        'section'        => $s->section           ?? '-',
                        'admission_no'   => $s->admission_number  ?? '-',
                        'total_due'      => $due,
                        'total_paid'     => $paid,
                        'outstanding'    => $outstanding,
                        'collection_pct' => $due > 0 ? round($paid / $due * 100, 1) : 0,
                        'last_payment'   => isset($lastPayMap[$s->id])
                            ? Carbon::parse($lastPayMap[$s->id])->format('d M Y')
                            : null,
                        'pay_status'     => $outstanding <= 0 ? 'paid'
                            : ($paid > 0 ? 'partial' : 'pending'),
                    ];
                });
        }

        return view('admin.reports.fee-index', compact(
            'classes', 'sections',
            'selectedClassId', 'selectedSection',
            'totalDue', 'totalPaid', 'totalOutstanding', 'totalOverdue', 'collectionRate',
            'reportData', 'studentRows'
        ));
    }

    // =========================================================================
    // STUDENT DETAIL
    // =========================================================================
    public function studentDetails(int $studentId)
    {
        $student = Student::with(['class', 'feeInstallments.feeType'])
            ->findOrFail($studentId);

        $installments = $student->feeInstallments
            ->sortBy('due_date')
            ->map(function (StudentFeeInstallment $inst) {
                $remaining = match ($inst->status) {
                    'paid'    => 0.0,
                    'partial' => max(0.0, (float)$inst->amount - (float)$inst->paid_amount),
                    default   => (float) $inst->amount,
                };
                return [
                    'id'             => $inst->id,
                    'fee_type'       => $inst->feeType->name   ?? 'Fee',
                    'period'         => $inst->feeType->period ?? '',
                    'due_date'       => optional($inst->due_date)->format('d M Y'),
                    'amount'         => (float) $inst->amount,
                    'paid_amount'    => (float) $inst->paid_amount,
                    'remaining'      => $remaining,
                    'status'         => ucfirst($inst->status),
                    'payment_date'   => optional($inst->payment_date)->format('d M Y'),
                    'receipt_number' => $inst->receipt_number ?? '-',
                    'is_overdue'     => $remaining > 0
                                        && $inst->due_date
                                        && $inst->due_date->isPast(),
                ];
            });

        // All totals from installments — match index page exactly
        $totalDue       = (float) $student->feeInstallments->sum('amount');
        $totalPaid      = (float) $student->feeInstallments->sum('paid_amount');
        $totalRemaining = (float) $installments->sum('remaining');
        $collectionPct  = $totalDue > 0 ? round($totalPaid / $totalDue * 100, 1) : 0;

        // Invoice / challan (extra charges — shown for reference only)
        $invoices = collect();
        if (method_exists($student, 'invoices')) {
            $invoices = $student->invoices()
                ->with('paymentMethod')
                ->orderByDesc('due_date')
                ->get()
                ->map(function ($inv) {
                    $meta = is_array($inv->metadata)
                        ? $inv->metadata
                        : (json_decode($inv->metadata, true) ?? []);
                    return [
                        'invoice_number'      => $inv->invoice_number,
                        'amount'              => (float) $inv->amount,
                        'due_date'            => optional($inv->due_date)->format('d M Y'),
                        'status'              => ucfirst($inv->status),
                        'paid_at'             => optional($inv->paid_at)->format('d M Y'),
                        'late_fee'            => (float) ($meta['late_fee']            ?? 0),
                        'discount'            => (float) ($meta['discount']            ?? 0),
                        'other_charge_desc'   => $meta['other_charge_desc']            ?? null,
                        'other_charge_amount' => (float) ($meta['other_charge_amount'] ?? 0),
                        'bank'                => $inv->paymentMethod->name             ?? 'N/A',
                    ];
                });
        }

        return view('admin.reports.student-details', compact(
            'student', 'installments', 'invoices',
            'totalDue', 'totalPaid', 'totalRemaining', 'collectionPct'
        ));
    }
}