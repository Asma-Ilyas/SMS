<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeSubmission extends Model
{
    use HasFactory;

    protected $table = 'student_fee_submissions';

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
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    // ========== RELATIONSHIPS ==========

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeSubmissionType()
    {
        return $this->belongsTo(FeeSubmissionType::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeSubmissionType::class, 'fee_submission_type_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'student_fee_submission_id');
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

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
                    ->where('due_date', '<', now());
    }

    // ========== ACCESSORS ==========

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'partial' => '<span class="badge bg-info">Partial</span>',
            'paid' => '<span class="badge bg-success">Paid</span>',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getIsOverdueAttribute()
    {
        return $this->status !== 'paid' && $this->due_date < now();
    }

    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->amount == 0) return 0;
        return round(($this->paid_amount / $this->amount) * 100, 2);
    }
}