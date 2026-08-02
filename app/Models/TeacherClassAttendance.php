<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherClassAttendance extends Model
{
    use HasFactory;

    protected $table = 'teacher_class_attendance';

    protected $fillable = [
        'teacher_id', 'class_section_id', 'subject_id', 'time_slot_id',
        'date', 'status', 'class_taken', 'duration_minutes', 'remarks',
        'substitute_teacher_id'
    ];

    protected $casts = [
        'date' => 'date',
        'class_taken' => 'boolean',
    ];

    // ========== RELATIONSHIPS ==========

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function substituteTeacher()
    {
        return $this->belongsTo(Staff::class, 'substitute_teacher_id');
    }

    // ========== ACCESSORS ==========

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'present' => 'success',
            'absent' => 'danger',
            'late' => 'warning',
            'cancelled' => 'secondary',
            'substitute' => 'info',
        ];
        return $badges[$this->status] ?? 'secondary';
    }
}