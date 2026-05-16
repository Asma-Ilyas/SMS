<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryController extends Controller
{
    // =========================================================================
    // SALARY CALCULATION RULES (used everywhere for consistency)
    //
    //   gross_salary  = basic_salary + allowances
    //   net_salary    = gross_salary - deductions
    //   pending       = salaries where payment_status = 'pending'
    //   paid          = salaries where payment_status = 'paid'
    //
    // Salary model expected columns:
    //   id, staff_id, month (Y-m), basic_salary, allowances, deductions,
    //   net_salary, payment_date, payment_method, payment_status (paid|pending),
    //   notes, created_at, updated_at
    //
    // Staff model expected columns:
    //   id, first_name, last_name, designation, department, basic_salary
    // =========================================================================

    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Salary::with('staff')
            ->when($request->employee_id, fn($q) => $q->where('staff_id', $request->employee_id))
            ->when($request->month,       fn($q) => $q->where('month',    $request->month))
            ->when($request->status,      fn($q) => $q->where('payment_status', $request->status))
            ->latest('month');

        $salaries = $query->paginate(20)->withQueryString();

        // ── Summary totals (respect active filters) ──────────────────────────
        $filteredIds = (clone $query)->pluck('id');

        $summary = Salary::whereIn('id', $filteredIds)
            ->selectRaw("
                COUNT(*)                                        AS total_count,
                SUM(basic_salary)                               AS total_basic,
                SUM(allowances)                                 AS total_allowances,
                SUM(deductions)                                 AS total_deductions,
                SUM(net_salary)                                 AS total_net,
                SUM(CASE WHEN payment_status='paid'    THEN net_salary ELSE 0 END) AS total_paid,
                SUM(CASE WHEN payment_status='pending' THEN net_salary ELSE 0 END) AS total_pending,
                COUNT(CASE WHEN payment_status='paid'    THEN 1 END)               AS count_paid,
                COUNT(CASE WHEN payment_status='pending' THEN 1 END)               AS count_pending
            ")
            ->first();

        // ── Distinct months for filter dropdown ───────────────────────────────
        $months = Salary::select('month')
            ->distinct()
            ->orderByDesc('month')
            ->get();

        // ── Employees for filter dropdown ─────────────────────────────────────
        $employees = Staff::orderBy('first_name')->get();

        // ── Monthly trend (last 12 months) for chart ──────────────────────────
        $trend = Salary::select(
                'month',
                DB::raw('SUM(net_salary) as total'),
                DB::raw('SUM(CASE WHEN payment_status="paid" THEN net_salary ELSE 0 END) as paid'),
                DB::raw('SUM(CASE WHEN payment_status="pending" THEN net_salary ELSE 0 END) as pending')
            )
            ->where('month', '>=', now()->subMonths(11)->format('Y-m'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($r) => [
                'month'   => Carbon::createFromFormat('Y-m', $r->month)->format('M Y'),
                'total'   => (float) $r->total,
                'paid'    => (float) $r->paid,
                'pending' => (float) $r->pending,
            ]);

        return view('admin.salaries.index', compact(
            'salaries', 'employees', 'months', 'summary', 'trend'
        ));
    }

    // ── CREATE ────────────────────────────────────────────────────────────────
    public function create()
    {
        $employees = Staff::orderBy('first_name')->get();
        return view('admin.salaries.create', compact('employees'));
    }

    // ── STORE ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'staff_id'       => 'required|exists:staff,id',
            'month'          => 'required|date_format:Y-m',
            'basic_salary'   => 'required|numeric|min:0',
            'allowances'     => 'required|numeric|min:0',
            'deductions'     => 'required|numeric|min:0',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:bank,cash,cheque',
            'payment_status' => 'required|in:paid,pending',
            'notes'          => 'nullable|string|max:500',
        ]);

        // Prevent duplicate for same staff + month
        $exists = Salary::where('staff_id', $data['staff_id'])
            ->where('month', $data['month'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['month' => 'Salary for this employee and month already exists.'])->withInput();
        }

        $data['net_salary'] = $data['basic_salary'] + $data['allowances'] - $data['deductions'];

        Salary::create($data);

        return redirect()->route('admin.salaries.index')
            ->with('success', 'Salary record created successfully.');
    }

    // ── SHOW ──────────────────────────────────────────────────────────────────
    public function show(Salary $salary)
    {
        $salary->load('staff');

        // History for this employee (last 12 months)
        $history = Salary::where('staff_id', $salary->staff_id)
            ->orderByDesc('month')
            ->limit(12)
            ->get()
            ->map(fn($s) => [
                'month'          => Carbon::createFromFormat('Y-m', $s->month)->format('M Y'),
                'basic_salary'   => (float) $s->basic_salary,
                'allowances'     => (float) $s->allowances,
                'deductions'     => (float) $s->deductions,
                'net_salary'     => (float) $s->net_salary,
                'payment_status' => $s->payment_status,
                'payment_date'   => optional(Carbon::parse($s->payment_date))->format('d M Y'),
                'is_current'     => $s->id === $salary->id,
            ]);

        // YTD totals for this employee
        $ytd = Salary::where('staff_id', $salary->staff_id)
            ->where('month', 'like', Carbon::now()->year . '-%')
            ->selectRaw("
                SUM(basic_salary) as ytd_basic,
                SUM(allowances)   as ytd_allowances,
                SUM(deductions)   as ytd_deductions,
                SUM(net_salary)   as ytd_net
            ")
            ->first();

        return view('admin.salaries.show', compact('salary', 'history', 'ytd'));
    }

    // ── EDIT ──────────────────────────────────────────────────────────────────
    public function edit(Salary $salary)
    {
        $salary->load('staff');
        $employees = Staff::orderBy('first_name')->get();
        return view('admin.salaries.edit', compact('salary', 'employees'));
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, Salary $salary)
    {
        $data = $request->validate([
            'basic_salary'   => 'required|numeric|min:0',
            'allowances'     => 'required|numeric|min:0',
            'deductions'     => 'required|numeric|min:0',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:bank,cash,cheque',
            'payment_status' => 'required|in:paid,pending',
            'notes'          => 'nullable|string|max:500',
        ]);

        $data['net_salary'] = $data['basic_salary'] + $data['allowances'] - $data['deductions'];

        $salary->update($data);

        return redirect()->route('admin.salaries.index')
            ->with('success', 'Salary record updated successfully.');
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────
    public function destroy(Salary $salary)
    {
        $salary->delete();
        return back()->with('success', 'Salary record deleted.');
    }

    // ── GENERATE PAYROLL (bulk) ───────────────────────────────────────────────
    public function generatePayroll(Request $request)
    {
        $data = $request->validate([
            'month'          => 'required|date_format:Y-m',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:bank,cash,cheque',
            'payment_status' => 'required|in:paid,pending',
        ]);

        $staff = Staff::all();
        $created = 0;
        $skipped = 0;

        foreach ($staff as $employee) {
            $exists = Salary::where('staff_id', $employee->id)
                ->where('month', $data['month'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $basic      = $employee->basic_salary ?? 0;
            $allowances = $employee->allowances   ?? 0;
            $deductions = $employee->deductions   ?? 0;
            $net        = $basic + $allowances - $deductions;

            Salary::create([
                'staff_id'       => $employee->id,
                'month'          => $data['month'],
                'basic_salary'   => $basic,
                'allowances'     => $allowances,
                'deductions'     => $deductions,
                'net_salary'     => $net,
                'payment_date'   => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_status'],
            ]);

            $created++;
        }

        $msg = "Payroll generated: {$created} records created.";
        if ($skipped > 0) $msg .= " {$skipped} skipped (already exist).";

        return redirect()->route('admin.salaries.index')->with('success', $msg);
    }

    // ── MARK AS PAID (quick action) ───────────────────────────────────────────
    public function markPaid(Salary $salary)
    {
        $salary->update([
            'payment_status' => 'paid',
            'payment_date'   => now()->toDateString(),
        ]);
        return back()->with('success', 'Salary marked as paid.');
    }

    // ── EXPORT CSV ────────────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $query = Salary::with('staff')
            ->when($request->employee_id, fn($q) => $q->where('staff_id', $request->employee_id))
            ->when($request->month,       fn($q) => $q->where('month', $request->month))
            ->when($request->status,      fn($q) => $q->where('payment_status', $request->status))
            ->orderByDesc('month')
            ->get();

        $filename = 'salary_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Employee', 'Month', 'Basic Salary', 'Allowances',
                'Deductions', 'Net Salary', 'Payment Date', 'Method', 'Status',
            ]);
            foreach ($query as $s) {
                fputcsv($handle, [
                    $s->staff->first_name . ' ' . $s->staff->last_name,
                    $s->month,
                    $s->basic_salary,
                    $s->allowances,
                    $s->deductions,
                    $s->net_salary,
                    $s->payment_date,
                    $s->payment_method,
                    $s->payment_status,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
    // Show the "Mark as Paid" form
public function markPaidForm(Salary $salary)
{
    return view('admin.salaries.mark-paid', compact('salary'));
}

// Process the "Mark as Paid" submission
public function markPaidUpdate(Request $request, Salary $salary)
{
    $request->validate([
        'payment_date'   => 'required|date',
        'payment_method' => 'required|in:bank,cash,cheque',
        'transaction_ref' => 'nullable|string|max:100',
        'remarks'        => 'nullable|string|max:500',
    ]);

    $salary->update([
        'payment_status'   => 'paid',
        'payment_date'     => $request->payment_date,
        'payment_method'   => $request->payment_method,
        'transaction_ref'  => $request->transaction_ref,
        'notes'            => $request->remarks, // assuming 'notes' column exists
    ]);

    return redirect()->route('admin.salaries.index')
        ->with('success', 'Salary marked as paid successfully.');
}
}