<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeInstallment extends Model
{
    use HasFactory;

    protected $table = 'student_fee_installments';

    protected $fillable = [
        'student_id',
        'fee_submission_type_id',
        'installment_number',
        'amount',
        'due_date',
        'status',
        'paid_amount',
        'payment_date',
        'receipt_number',
        'remarks'
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeSubmissionType()
    {
        return $this->belongsTo(FeeSubmissionType::class, 'fee_submission_type_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    // ========== ACCESSORS ==========

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'partial' => 'info',
            'paid' => 'success',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'partial' => 'Partial',
            'paid' => 'Paid',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getIsOverdueAttribute()
    {
        return $this->status != 'paid' && $this->due_date < now();
    }

    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    // ========== SCOPES ==========

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')->where('due_date', '<', now());
    }
}