<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Salary;
use App\Models\SalaryTemplate;
use App\Models\AttendanceSummary;
use App\Services\SalaryCalculator;
use App\Support\PortalAlert;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryController extends Controller
{
    /**
     * Display salary dashboard
     */
    public function index(Request $request)
    {
        $staff = Staff::with(['category'])
                     ->active()
                     ->orderBy('first_name')
                     ->get();

        $months = $this->getMonthsList();
        $selectedMonth = $request->month ?? now()->format('Y-m');

        $salaries = Salary::with(['staff'])
                         ->where('month', $selectedMonth)
                         ->get()
                         ->keyBy('staff_id');

        // Get attendance summaries for the selected month
        $attendanceSummaries = AttendanceSummary::where('month', $selectedMonth)
            ->get()
            ->keyBy('staff_id');

        return view('admin.salaries.index', compact(
            'staff', 
            'months', 
            'selectedMonth', 
            'salaries',
            'attendanceSummaries'
        ));
    }

    /**
     * Generate payroll for all staff
     */
    public function generatePayroll(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $year = (int) substr($month, 0, 4);
        $monthNum = (int) substr($month, 5, 2);

        // Remember who already had a salary row, so re-running payroll doesn't re-notify them
        $alreadyHad = Salary::where('month', $month)->pluck('staff_id')->all();

        try {
            $results = SalaryCalculator::processPayroll($year, $monthNum, $request->payment_method ?? 'bank');

            $message = "Payroll processed for {$results['success']} employees in {$month}.";
            if ($results['failed'] > 0) {
                $message .= " Failed: {$results['failed']}. Errors: " . implode('; ', $results['errors']);
            }

            // Tell the staff whose salary slip was just created
            $newStaffIds = Salary::where('month', $month)
                ->whereNotIn('staff_id', $alreadyHad)
                ->pluck('staff_id')
                ->all();

            if (!empty($newStaffIds)) {
                PortalAlert::toStaff(
                    $newStaffIds,
                    'Salary slip ready',
                    'Your salary for ' . $this->monthLabel($month) . ' has been processed.',
                    PortalAlert::link('teacher.salary.index', '/notifications'),
                    'salary',
                    'info'
                );
            }

            return redirect()->route('admin.salaries.index', ['month' => $month])
                            ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process payroll: ' . $e->getMessage());
        }
    }

    /**
     * Calculate salary for an individual staff member
     */
    public function calculateIndividual(Request $request, $staffId)
    {
        $month = $request->month ?? now()->format('Y-m');
        $year = (int) substr($month, 0, 4);
        $monthNum = (int) substr($month, 5, 2);
        
        try {
            $staff = Staff::findOrFail($staffId);
            $calculator = new SalaryCalculator($staff, $year, $monthNum);
            $salary = $calculator->saveSalaryRecord();
            
            return redirect()->route('admin.salaries.index', ['month' => $month])
                            ->with('success', "Salary calculated for {$staff->full_name}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to calculate salary: ' . $e->getMessage());
        }
    }

    /**
     * Show salary details
     */
    public function show($id)
    {
        $salary = Salary::with(['staff', 'approvedBy'])->findOrFail($id);
        $attendanceSummary = AttendanceSummary::where('staff_id', $salary->staff_id)
            ->where('month', $salary->month)
            ->first();

        return view('admin.salaries.show', compact('salary', 'attendanceSummary'));
    }

    /**
     * Show mark paid form
     */
    public function markPaidForm($id)
    {
        $salary = Salary::with(['staff'])->findOrFail($id);
        return view('admin.salaries.mark-paid', compact('salary'));
    }

    /**
     * Update salary as paid
     */
    public function markPaidUpdate(Request $request, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:bank,cash,cheque',
            'transaction_ref' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:500',
        ]);

        $salary = Salary::with('staff')->findOrFail($id);
        $wasPaid = $salary->payment_status === 'paid';

        $salary->update([
            'payment_status' => 'paid',
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_ref' => $request->transaction_ref,
            'remarks' => $request->remarks,
            // approved_by references staff.id, so find the staff record of the logged-in admin
            'approved_by' => Staff::where('email', auth()->user()?->email)->value('id'),
            'approved_at' => now(),
        ]);

        // Tell the staff member their salary was paid (only the first time)
        if (!$wasPaid) {
            PortalAlert::toStaff(
                $salary->staff_id,
                'Salary paid',
                'Your salary for ' . $this->monthLabel($salary->month) . ' has been paid'
                    . ($request->payment_method ? ' by ' . $request->payment_method : '') . '.',
                PortalAlert::link('teacher.salary.index', '/notifications'),
                'salary',
                'success'
            );
        }

        $name = trim(($salary->staff->first_name ?? '') . ' ' . ($salary->staff->last_name ?? ''));

        return redirect()->route('admin.salaries.index')
                        ->with('success', "Salary marked as paid for {$name}");
    }

    /**
     * "2026-09" -> "September 2026"
     */
    private function monthLabel(string $month): string
    {
        try {
            return Carbon::createFromFormat('Y-m', $month)->format('F Y');
        } catch (\Throwable $e) {
            return $month;
        }
    }

    /**
     * Preview salary before saving
     */
    public function preview($staffId, Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $year = (int) substr($month, 0, 4);
        $monthNum = (int) substr($month, 5, 2);
        
        $staff = Staff::findOrFail($staffId);
        $calculator = new SalaryCalculator($staff, $year, $monthNum);
        $calculated = $calculator->calculate();
        
        return view('admin.salaries.preview', compact('staff', 'calculated', 'month'));
    }

    /**
     * Export payroll to CSV
     */
    public function export(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $salaries = Salary::with(['staff'])
                         ->where('month', $month)
                         ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=payroll_{$month}.csv",
        ];

        $callback = function() use ($salaries, $month) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Payroll Report - ' . $month]);
            fputcsv($handle, []);
            fputcsv($handle, [
                'Employee ID', 'Employee Name', 'Designation', 'Basic Salary',
                'Days Present', 'Days Absent', 'Days Late', 'Allowances',
                'Attendance Bonus', 'Overtime Pay', 'Absent Deduction',
                'Late Deduction', 'Leave Deduction', 'Penalties',
                'Total Deductions', 'Tax', 'PF', 'Net Salary', 'Status'
            ]);

            foreach ($salaries as $salary) {
                $staff = $salary->staff;
                $totalDeductions = $salary->deductions + $salary->penalties + $salary->leave_deductions;
                
                fputcsv($handle, [
                    $staff->employee_id ?? 'N/A',
                    $staff->full_name ?? 'N/A',
                    $staff->designation ?? 'N/A',
                    number_format($salary->basic_salary, 2),
                    $salary->days_present ?? 0,
                    $salary->days_absent ?? 0,
                    $salary->days_late ?? 0,
                    number_format($salary->allowances, 2),
                    number_format($salary->attendance_bonus ?? 0, 2),
                    number_format($salary->overtime_pay ?? 0, 2),
                    number_format($salary->deductions, 2),
                    number_format(0, 2),
                    number_format($salary->leave_deductions ?? 0, 2),
                    number_format($salary->penalties ?? 0, 2),
                    number_format($totalDeductions, 2),
                    number_format($salary->tax, 2),
                    number_format($salary->pf_employee, 2),
                    number_format($salary->net_salary, 2),
                    ucfirst($salary->payment_status),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =============================================
    // SALARY TEMPLATES
    // =============================================

    /**
     * Display salary templates
     */
    public function templates()
    {
        $templates = SalaryTemplate::orderBy('name')->get();
        return view('admin.salaries.templates', compact('templates'));
    }

    /**
     * Show create template form
     */
    public function createTemplate()
    {
        return view('admin.salaries.create-template');
    }

    /**
     * Store a new template
     */
    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:salary_templates',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'pf_percentage' => 'nullable|numeric|min:0|max:100',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        SalaryTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'basic_salary' => $request->basic_salary,
            'allowances' => $request->allowances ?? 0,
            'medical_allowance' => $request->medical_allowance ?? 0,
            'transport_allowance' => $request->transport_allowance ?? 0,
            'pf_percentage' => $request->pf_percentage ?? 5,
            'tax_percentage' => $request->tax_percentage ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.salaries.templates')
                        ->with('success', 'Salary template created successfully!');
    }

    /**
     * Edit template (AJAX)
     */
    public function editTemplate($id)
    {
        $template = SalaryTemplate::findOrFail($id);
        return response()->json($template);
    }

    /**
     * Update template
     */
    public function updateTemplate(Request $request, $id)
    {
        $template = SalaryTemplate::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:salary_templates,name,' . $id,
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'pf_percentage' => 'nullable|numeric|min:0|max:100',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        $template->update([
            'name' => $request->name,
            'description' => $request->description,
            'basic_salary' => $request->basic_salary,
            'allowances' => $request->allowances ?? 0,
            'medical_allowance' => $request->medical_allowance ?? 0,
            'transport_allowance' => $request->transport_allowance ?? 0,
            'pf_percentage' => $request->pf_percentage ?? 5,
            'tax_percentage' => $request->tax_percentage ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.salaries.templates')
                        ->with('success', 'Salary template updated successfully!');
    }

    /**
     * Delete template
     */
    public function destroyTemplate($id)
    {
        $template = SalaryTemplate::findOrFail($id);
        
        // Check if template is being used
        $usedCount = Staff::where('basic_salary', $template->basic_salary)->count();
        if ($usedCount > 0) {
            return redirect()->route('admin.salaries.templates')
                            ->with('error', 'Cannot delete template as it is being used by ' . $usedCount . ' staff members.');
        }
        
        $template->delete();

        return redirect()->route('admin.salaries.templates')
                        ->with('success', 'Salary template deleted successfully!');
    }

    /**
     * Apply template to staff
     */
    public function applyTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:salary_templates,id',
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:staff,id',
        ]);

        $template = SalaryTemplate::findOrFail($request->template_id);
        $staffIds = $request->staff_ids;

        Staff::whereIn('id', $staffIds)->update([
            'basic_salary' => $template->basic_salary,
            'allowances' => $template->allowances,
            'medical_allowance' => $template->medical_allowance,
            'transport_allowance' => $template->transport_allowance,
            'pf_percentage' => $template->pf_percentage,
        ]);

        return redirect()->route('admin.salaries.templates')
                        ->with('success', 'Salary template applied to ' . count($staffIds) . ' staff members.');
    }

    /**
     * Get months list for dropdown
     */
    private function getMonthsList()
    {
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $months[$date->format('Y-m')] = $date->format('F Y');
        }
        return $months;
    }
}