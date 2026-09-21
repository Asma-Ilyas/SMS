<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentHostelFeePayment extends Model
{
    protected $fillable = [
        'student_hostel_allocation_id', 'month', 'amount', 'paid_amount',
        'due_date', 'payment_date', 'status', 'receipt_number', 'remarks',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    public function allocation()
    {
        return $this->belongsTo(StudentHostelAllocation::class, 'student_hostel_allocation_id');
    }
}
