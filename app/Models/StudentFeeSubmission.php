<?php
// app/Models/StudentFeeSubmission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeSubmission extends Model
{
    use HasFactory;

    protected $table = 'student_fee_submissions';

    protected $fillable = [
        'student_id', 'fee_type_id', 'period', 'amount',
        'submission_date', 'receipt_number', 'remarks'
    ];

    protected $casts = [
        'submission_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }
}