<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\Salary;
use App\Models\Attendance;
use Carbon\Carbon;

class SalaryCalculator
{
    protected $staff;
    protected $year;
    protected $month;
    protected $totalWorkingDays = 26; // or fetch from settings

    public function __construct(Staff $staff, $year, $month)
    {
        $this->staff = $staff;
        $this->year = $year;
        $this->month = $month;
    }

    public function calculate()
    {
        $summary = $this->getAttendanceSummary();
        $dailyRate = $this->staff->basic_salary / $this->totalWorkingDays;

        $basicPay = $dailyRate * $summary['present'];
        $deductions = $dailyRate * $summary['absent']; // full day deduction
        $allowances = $this->staff->allowances ?? 0;
        $overtimePay = $summary['overtimeHours'] * ($dailyRate / 8 * 1.5);
        // Simple tax (adjust as needed)
        $tax = $this->calculateTax($basicPay + $allowances + $overtimePay - $deductions);
        $pfEmployee = $basicPay * 0.05;

        $netSalary = $basicPay + $allowances + $overtimePay - $deductions - $tax - $pfEmployee;

        return [
            'total_working_days' => $this->totalWorkingDays,
            'days_present'       => $summary['present'],
            'days_absent'        => $summary['absent'],
            'days_late'          => $summary['late'],
            'basic_salary'       => $this->staff->basic_salary,
            'daily_rate'         => $dailyRate,
            'attendance_bonus'   => 0,
            'overtime_pay'       => $overtimePay,
            'allowances'         => $allowances,
            'deductions'         => $deductions,
            'tax'                => $tax,
            'pf_employee'        => $pfEmployee,
            'pf_employer'        => $basicPay * 0.05,
            'net_salary'         => $netSalary,
        ];
    }

   public function saveSalaryRecord($paymentDate, $paymentMethod, $remarks = null)
{
    $calculated = $this->calculate();
    $monthString = sprintf('%d-%02d', $this->year, $this->month);

    $data = array_merge($calculated, [
        'staff_id'       => $this->staff->id,
        'month'          => $monthString,
        'payment_date'   => $paymentDate,
        'payment_method' => $paymentMethod,
        // 'remarks' => $remarks, // remove this line
    ]);

    // Optionally add remarks only if the column exists, but for now skip.

    return Salary::updateOrCreate(
        ['staff_id' => $this->staff->id, 'month' => $monthString],
        $data
    );
}

    protected function getAttendanceSummary()
    {
        $startDate = Carbon::create($this->year, $this->month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = Attendance::where('staff_id', $this->staff->id)
                                 ->whereBetween('date', [$startDate, $endDate])
                                 ->get();

        $present = 0;
        $absent = 0;
        $late = 0;
        $overtimeHours = 0;

        foreach ($attendances as $att) {
            if (in_array($att->status, ['present', 'late'])) {
                $present++;
                if ($att->status == 'late') $late++;
                if ($att->check_out && $att->check_out > '18:00:00') {
                   $diff = (strtotime($att->check_out) - strtotime('18:00:00'));
if ($diff > 0) {
    $overtimeHours += $diff / 3600;
}
                }
            } elseif ($att->status == 'absent') {
                $absent++;
            }
        }

        // If no attendance records, assume all days absent
        if ($attendances->isEmpty()) {
            $absent = $this->totalWorkingDays;
        }

        return compact('present', 'absent', 'late', 'overtimeHours');
    }

    protected function calculateTax($income)
    {
        if ($income <= 50000) return 0;
        if ($income <= 100000) return $income * 0.05;
        if ($income <= 200000) return $income * 0.10;
        return $income * 0.15;
    }
}