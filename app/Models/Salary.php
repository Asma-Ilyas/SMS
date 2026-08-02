<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $table = 'salaries';

    protected $fillable = [
        'staff_id', 'month', 'total_working_days', 'days_present', 'days_absent',
        'days_late', 'basic_salary', 'daily_rate', 'attendance_bonus', 'overtime_pay',
        'allowances', 'bonus', 'commission', 'deductions', 'penalties',
        'leave_deductions', 'tax', 'pf_employee', 'pf_employer',
        'net_salary', 'payment_date', 'payment_method', 'payment_status',
        'transaction_ref', 'bank_name', 'account_number', 'approved_by', 'approved_at',
        'remarks'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'approved_at' => 'datetime',
        'basic_salary' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'attendance_bonus' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'allowances' => 'decimal:2',
        'bonus' => 'decimal:2',
        'commission' => 'decimal:2',
        'deductions' => 'decimal:2',
        'penalties' => 'decimal:2',
        'leave_deductions' => 'decimal:2',
        'tax' => 'decimal:2',
        'pf_employee' => 'decimal:2',
        'pf_employer' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }

    // ========== ACCESSORS ==========

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'paid' => 'success',
            'pending' => 'warning',
        ];
        return $badges[$this->payment_status] ?? 'secondary';
    }

    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'paid' => '✅ Paid',
            'pending' => '⏳ Pending',
        ];
        return $labels[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    // ========== SCOPES ==========

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeForMonth($query, $month)
    {
        return $query->where('month', $month);
    }
}