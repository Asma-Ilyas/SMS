<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDiscount extends Model
{
    protected $fillable = ['student_id', 'discount_id', 'fee_submission_type_id', 'valid_from', 'valid_until'];
    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeSubmissionType::class, 'fee_submission_type_id');
    }

    public function isActive()
    {
        $today = now();
        return (!$this->valid_from || $this->valid_from <= $today) &&
               (!$this->valid_until || $this->valid_until >= $today);
    }
}