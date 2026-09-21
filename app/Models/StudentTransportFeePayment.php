<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTransportFeePayment extends Model
{
    protected $fillable = [
        'student_transport_id', 'month', 'amount', 'paid_amount', 'due_date',
        'payment_date', 'status', 'receipt_number', 'remarks',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    public function studentTransport()
    {
        return $this->belongsTo(StudentTransport::class);
    }
}
