<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\Salary;
use App\Models\StaffAttendance;
use App\Models\AttendanceSummary;
use App\Models\Leave;
use Carbon\Carbon;

class SalaryCalculator
{
    protected $staff;
    protected $year;
    protected $month;
    protected $totalWorkingDays = 26; // Default working days per month
    protected $deductionRules;

    public function __construct(Staff $staff, $year, $month)
    {
        $this->staff = $staff;
        $this->year = $year;
        $this->month = $month;
        
        // Define deduction rules
        $this->deductionRules = [
            'absent' => [
                'per_day' => 1.0, // 100% of daily rate
                'deduct_allowances' => true,
                'deduct_bonus' => true,
            ],
            'late' => [
                'first_3_free' => 3, // First 3 lates are free
                'per_late' => 0.25, // 25% of daily rate per late day after 3
                'max_late' => 10,
            ],
            'leave' => [
                'sick' => 0, // No deduction for sick leave
                'casual' => 0.5, // 50% deduction for casual leave
                'annual' => 1.0, // 100% deduction for annual leave
            ],
            'half_day' => 0.5, // 50% of daily rate
        ];
    }

    /**
     * Main calculation method
     */
    public function calculate()
    {
        $summary = $this->getAttendanceSummary();
        $dailyRate = $this->calculateDailyRate();
        
        // Get leaves for the month
        $leaves = $this->getLeaves();
        
        // Calculate basic pay based on present days
        $basicPay = $dailyRate * $summary['present'];
        
        // Calculate all deductions
        $deductionDetails = $this->calculateAllDeductions($summary, $leaves, $dailyRate);
        
        // Calculate allowances
        $allowances = $this->calculateAllowances();
        
        // Calculate overtime pay
        $overtimePay = $this->calculateOvertimePay($summary['overtimeHours'], $dailyRate);
        
        // Calculate attendance bonus
        $attendanceBonus = $this->calculateAttendanceBonus($summary);
        
        // Calculate PF
        $pfEmployee = $this->calculatePF($basicPay);
        $pfEmployer = $pfEmployee * 1.5;
        
        // Calculate tax
        $taxableIncome = $basicPay + $allowances + $attendanceBonus + $overtimePay - $deductionDetails['total'];
        $tax = $this->calculateTax($taxableIncome);
        
        // Calculate net salary
        $netSalary = $basicPay 
            + $allowances 
            + $attendanceBonus 
            + $overtimePay 
            - $deductionDetails['total'] 
            - $pfEmployee 
            - $tax;

        // Get total working days
        $totalWorkingDays = $this->getTotalWorkingDays();

        return [
            'staff_id' => $this->staff->id,
            'month' => sprintf('%d-%02d', $this->year, $this->month),
            'total_working_days' => $totalWorkingDays,
            'days_present' => $summary['present'],
            'days_absent' => $summary['absent'],
            'days_late' => $summary['late'],
            'days_leave' => $summary['leave'],
            'days_half' => $summary['half_day'],
            'basic_salary' => $this->staff->basic_salary,
            'daily_rate' => $dailyRate,
            'attendance_bonus' => $attendanceBonus,
            'overtime_pay' => $overtimePay,
            'allowances' => $allowances,
            'deductions' => $deductionDetails['absent'],
            'penalties' => $deductionDetails['penalties'],
            'leave_deductions' => $deductionDetails['leave'],
            'late_deductions' => $deductionDetails['late'],
            'total_deductions' => $deductionDetails['total'],
            'tax' => $tax,
            'pf_employee' => $pfEmployee,
            'pf_employer' => $pfEmployer,
            'net_salary' => $netSalary,
            'deduction_breakdown' => $deductionDetails, // For detailed view
        ];
    }

    /**
     * Save salary record
     */
    public function saveSalaryRecord($paymentDate = null, $paymentMethod = 'bank', $remarks = null)
    {
        $calculated = $this->calculate();
        $monthString = sprintf('%d-%02d', $this->year, $this->month);

        $data = [
            'staff_id' => $this->staff->id,
            'month' => $monthString,
            'total_working_days' => $calculated['total_working_days'],
            'days_present' => $calculated['days_present'],
            'days_absent' => $calculated['days_absent'],
            'days_late' => $calculated['days_late'],
            'basic_salary' => $calculated['basic_salary'],
            'daily_rate' => $calculated['daily_rate'],
            'attendance_bonus' => $calculated['attendance_bonus'],
            'overtime_pay' => $calculated['overtime_pay'],
            'allowances' => $calculated['allowances'],
            'deductions' => $calculated['deductions'],
            'penalties' => $calculated['penalties'],
            'leave_deductions' => $calculated['leave_deductions'],
            'tax' => $calculated['tax'],
            'pf_employee' => $calculated['pf_employee'],
            'pf_employer' => $calculated['pf_employer'],
            'net_salary' => $calculated['net_salary'],
            'payment_date' => $paymentDate ?? now(),
            'payment_method' => $paymentMethod,
            'payment_status' => 'pending',
            'remarks' => $remarks,
        ];

        // Save or update salary record
        $salary = Salary::updateOrCreate(
            ['staff_id' => $this->staff->id, 'month' => $monthString],
            $data
        );

        // Update attendance summary
        $this->updateAttendanceSummary($calculated);

        return $salary;
    }

    /**
     * Get attendance summary for the month
     */
    protected function getAttendanceSummary()
    {
        $startDate = Carbon::create($this->year, $this->month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = StaffAttendance::where('staff_id', $this->staff->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $present = 0;
        $absent = 0;
        $late = 0;
        $leave = 0;
        $half_day = 0;
        $on_duty = 0;
        $holiday = 0;
        $overtimeHours = 0;

        foreach ($attendances as $att) {
            switch ($att->status) {
                case 'present':
                    $present++;
                    break;
                case 'absent':
                    $absent++;
                    break;
                case 'late':
                    $late++;
                    $present++; // Late is also counted as present but with deduction
                    break;
                case 'leave':
                    $leave++;
                    break;
                case 'half_day':
                    $half_day++;
                    $present += 0.5; // Half day counts as half present
                    break;
                case 'on_duty':
                    $on_duty++;
                    $present++;
                    break;
                case 'holiday':
                    $holiday++;
                    break;
            }

            // Calculate overtime hours (if check_out > 18:00)
            if ($att->check_out && $att->check_out > '18:00:00') {
                $diff = strtotime($att->check_out) - strtotime('18:00:00');
                if ($diff > 0) {
                    $overtimeHours += $diff / 3600;
                }
            }
        }

        // If no attendance records, assume all working days absent
        $totalWorkingDays = $this->getTotalWorkingDays();
        if ($attendances->isEmpty()) {
            $absent = $totalWorkingDays;
        }

        return compact(
            'present', 'absent', 'late', 'leave', 
            'half_day', 'on_duty', 'holiday', 'overtimeHours'
        );
    }

    /**
     * Get leaves for the month
     */
    protected function getLeaves()
    {
        $startDate = Carbon::create($this->year, $this->month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return Leave::where('staff_id', $this->staff->id)
            ->where('status', 'approved')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            })
            ->get();
    }

    /**
     * Calculate all deductions
     */
    protected function calculateAllDeductions($summary, $leaves, $dailyRate)
    {
        // 1. Absence Deduction
        $absentDeduction = $summary['absent'] * $dailyRate * $this->deductionRules['absent']['per_day'];
        
        // 2. Late Deduction
        $lateDays = $summary['late'];
        $firstFree = $this->deductionRules['late']['first_3_free'];
        $latePenalty = $this->deductionRules['late']['per_late'];
        $maxLate = $this->deductionRules['late']['max_late'];
        
        $lateDeduction = 0;
        $penalties = 0;
        
        if ($lateDays > $firstFree) {
            $chargeableLate = min($lateDays - $firstFree, $maxLate);
            $lateDeduction = $chargeableLate * $dailyRate * $latePenalty;
            
            // Extra penalty for excessive lates
            if ($lateDays > $maxLate) {
                $penalties += ($lateDays - $maxLate) * $dailyRate * 0.5;
            }
        }

        // 3. Leave Deduction
        $leaveDeduction = 0;
        foreach ($leaves as $leave) {
            $leaveDays = $this->calculateLeaveDays($leave);
            $deductionRate = $this->getLeaveDeductionRate($leave->type);
            $leaveDeduction += $leaveDays * $dailyRate * $deductionRate;
        }

        // 4. Half Day Deduction
        $halfDayDeduction = $summary['half_day'] * $dailyRate * $this->deductionRules['half_day'];

        // Calculate total deductions
        $totalDeductions = $absentDeduction + $lateDeduction + $leaveDeduction + $halfDayDeduction + $penalties;

        return [
            'absent' => $absentDeduction,
            'late' => $lateDeduction,
            'leave' => $leaveDeduction,
            'half_day' => $halfDayDeduction,
            'penalties' => $penalties,
            'total' => $totalDeductions,
        ];
    }

    /**
     * Get leave deduction rate based on leave type
     */
    protected function getLeaveDeductionRate($leaveType)
    {
        $rates = $this->deductionRules['leave'];
        return $rates[$leaveType] ?? 1.0;
    }

    /**
     * Calculate leave days within the month
     */
    protected function calculateLeaveDays($leave)
    {
        $startDate = Carbon::create($this->year, $this->month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $start = Carbon::parse($leave->start_date)->max($startDate);
        $end = Carbon::parse($leave->end_date)->min($endDate);

        if ($start->gt($end)) {
            return 0;
        }

        $days = 0;
        for ($date = clone $start; $date->lte($end); $date->addDay()) {
            if (!$date->isWeekend()) {
                $days++;
            }
        }

        return $days;
    }

    /**
     * Calculate daily rate based on salary type
     */
    protected function calculateDailyRate()
    {
        if ($this->staff->salary_type == 'daily') {
            return $this->staff->daily_rate > 0 ? $this->staff->daily_rate : $this->staff->basic_salary / 30;
        } elseif ($this->staff->salary_type == 'hourly') {
            return $this->staff->hourly_rate * 8; // 8 hours per day
        }
        return $this->staff->basic_salary / $this->getTotalWorkingDays();
    }

    /**
     * Get total working days (excluding weekends and holidays)
     */
    protected function getTotalWorkingDays()
    {
        $startDate = Carbon::create($this->year, $this->month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $workingDays = 0;
        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            if (!$date->isWeekend()) {
                $workingDays++;
            }
        }
        
        return $workingDays;
    }

    /**
     * Calculate allowances
     */
    protected function calculateAllowances()
    {
        return ($this->staff->allowances ?? 0) 
            + ($this->staff->medical_allowance ?? 0) 
            + ($this->staff->transport_allowance ?? 0);
    }

    /**
     * Calculate attendance bonus
     */
    protected function calculateAttendanceBonus($summary)
    {
        $totalWorkingDays = $this->getTotalWorkingDays();
        $attendancePercentage = $totalWorkingDays > 0 
            ? ($summary['present'] / $totalWorkingDays) * 100 
            : 0;

        if ($attendancePercentage >= 100) {
            return $this->staff->basic_salary * 0.05; // 5% bonus for perfect attendance
        } elseif ($attendancePercentage >= 95) {
            return $this->staff->basic_salary * 0.02; // 2% bonus for 95%+
        } elseif ($attendancePercentage >= 90) {
            return $this->staff->basic_salary * 0.01; // 1% bonus for 90%+
        }
        return 0;
    }

    /**
     * Calculate overtime pay
     */
    protected function calculateOvertimePay($overtimeHours, $dailyRate)
    {
        if ($overtimeHours <= 0) {
            return 0;
        }

        $hourlyRate = $dailyRate / 8; // Assuming 8 hours per day
        $overtimeRate = $hourlyRate * 1.5; // 1.5x for overtime
        
        return $overtimeHours * $overtimeRate;
    }

    /**
     * Calculate PF (Provident Fund)
     */
    protected function calculatePF($basicPay)
    {
        $pfPercentage = $this->staff->pf_percentage ?? 5;
        return $basicPay * ($pfPercentage / 100);
    }

    /**
     * Calculate tax
     */
    protected function calculateTax($income)
    {
        // Progressive tax rates
        if ($income <= 25000) {
            return 0;
        } elseif ($income <= 50000) {
            return round(($income - 25000) * 0.05, 2);
        } elseif ($income <= 100000) {
            return round(($income - 50000) * 0.10 + 1250, 2);
        } elseif ($income <= 200000) {
            return round(($income - 100000) * 0.15 + 6250, 2);
        } else {
            return round(($income - 200000) * 0.20 + 21250, 2);
        }
    }

    /**
     * Update attendance summary
     */
    protected function updateAttendanceSummary($calculated)
    {
        AttendanceSummary::updateOrCreate(
            [
                'staff_id' => $this->staff->id,
                'month' => sprintf('%d-%02d', $this->year, $this->month),
            ],
            [
                'total_days' => $calculated['total_working_days'],
                'present_days' => $calculated['days_present'],
                'absent_days' => $calculated['days_absent'],
                'late_days' => $calculated['days_late'],
                'leave_days' => $calculated['days_leave'] ?? 0,
                'holiday_days' => 0,
                'classes_taken' => $calculated['days_present'],
                'total_classes' => $calculated['total_working_days'],
                'attendance_percentage' => $calculated['total_working_days'] > 0 
                    ? round(($calculated['days_present'] / $calculated['total_working_days']) * 100, 2) 
                    : 0,
                'class_taken_percentage' => $calculated['total_working_days'] > 0 
                    ? round(($calculated['days_present'] / $calculated['total_working_days']) * 100, 2) 
                    : 0,
                'total_hours' => 0,
                'overtime_hours' => 0,
            ]
        );
    }

    /**
     * Process payroll for all staff
     */
    public static function processPayroll($year, $month, $paymentMethod = 'bank')
    {
        $staff = Staff::where('is_active', true)->get();
        $results = [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($staff as $employee) {
            try {
                $calculator = new self($employee, $year, $month);
                $calculator->saveSalaryRecord(null, $paymentMethod);
                $results['success']++;
                $results['total']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = $employee->full_name . ': ' . $e->getMessage();
            }
        }

        return $results;
    }
}