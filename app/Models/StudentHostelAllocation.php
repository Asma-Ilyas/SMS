<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentHostelAllocation extends Model
{
    protected $fillable = [
        'student_id', 'hostel_id', 'room_id', 'hostel_fee_type_id', 'bed_number',
        'allocation_date', 'vacate_date', 'status', 'remarks',
    ];

    protected $casts = [
        'allocation_date' => 'date',
        'vacate_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function room()
    {
        return $this->belongsTo(HostelRoom::class, 'room_id');
    }

    public function feeType()
    {
        return $this->belongsTo(HostelFeeType::class, 'hostel_fee_type_id');
    }

    public function feePayments()
    {
        return $this->hasMany(StudentHostelFeePayment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
