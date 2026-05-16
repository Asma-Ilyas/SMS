<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Salary extends Model
{
    use HasFactory;

    protected $table = 'salaries';

    protected $fillable = [
        'staff_id',
        'month',
        'total_working_days',
        'days_present',
        'days_absent',
        'days_late',
        'basic_salary',
        'daily_rate',
        'attendance_bonus',
        'overtime_pay',
        'allowances',
        'deductions',
        'tax',
        'pf_employee',
        'pf_employer',
        'net_salary',
        'payment_date',
        'payment_method',
        'transaction_ref',
        'remarks',
    ];

    protected $casts = [
        'payment_date'        => 'date',
        'basic_salary'        => 'decimal:2',
        'daily_rate'          => 'decimal:2',
        'attendance_bonus'    => 'decimal:2',
        'overtime_pay'        => 'decimal:2',
        'allowances'          => 'decimal:2',
        'deductions'          => 'decimal:2',
        'tax'                 => 'decimal:2',
        'pf_employee'         => 'decimal:2',
        'pf_employer'         => 'decimal:2',
        'net_salary'          => 'decimal:2',
    ];

    /**
     * Get the employee that owns the salary.
     */
   public function staff()
{
    return $this->belongsTo(Staff::class, 'staff_id');
}
    /**
     * Get the payroll batch (if any) that generated this salary.
     * Assumes you store batch_id in salaries table – add if needed.
     */
    public function payrollBatch()
    {
        return $this->belongsTo(PayrollBatch::class, 'batch_id');
    }

    /**
     * Accessor for formatted month (e.g., "June 2025").
     */
    public function getFormattedMonthAttribute()
    {
        return Carbon::createFromFormat('Y-m', $this->month)->format('F Y');
    }

    /**
     * Automatically calculate net salary before saving.
     */
    protected static function booted()
    {
        static::saving(function ($salary) {
            $salary->net_salary = $salary->basic_salary
                                + $salary->attendance_bonus
                                + $salary->overtime_pay
                                + $salary->allowances
                                - $salary->deductions
                                - $salary->tax
                                - $salary->pf_employee;
        });
    }
}