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
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeSubmissionType::class, 'fee_submission_type_id');
    }

    public function getRemainingAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function markAsPaid($paymentDate = null, $receipt = null)
    {
        $this->status = 'paid';
        $this->paid_amount = $this->amount;
        $this->payment_date = $paymentDate ?? now();
        if ($receipt) $this->receipt_number = $receipt;
        $this->save();
    }

    public function recordPartialPayment($amount, $paymentDate = null, $receipt = null)
    {
        $this->paid_amount += $amount;
        if ($this->paid_amount >= $this->amount) {
            $this->status = 'paid';
            $this->paid_amount = $this->amount;
        } else {
            $this->status = 'partial';
        }
        $this->payment_date = $paymentDate ?? now();
        if ($receipt) $this->receipt_number = $receipt;
        $this->save();
    }
    public function invoice()
{
    return $this->hasOne(Invoice::class);
}
}
