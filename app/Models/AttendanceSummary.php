<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{
    use HasFactory;

    protected $table = 'attendance_summaries';

    protected $fillable = [
        'staff_id', 'month', 'total_days', 'present_days', 'absent_days',
        'late_days', 'leave_days', 'holiday_days', 'classes_taken', 'total_classes',
        'attendance_percentage', 'class_taken_percentage', 'total_hours', 'overtime_hours'
    ];

    protected $casts = [
        'attendance_percentage' => 'decimal:2',
        'class_taken_percentage' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}