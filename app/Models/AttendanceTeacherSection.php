<?php
// app/Models/AttendanceTeacherSection.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceTeacherSection extends Model
{
    use HasFactory;

    protected $table = 'attendance_teacher_section';

    protected $fillable = [
        'teacher_id',
        'class_section_id',
        'subject_id',
        'can_mark_attendance',
        'can_approve_leave',
        'assigned_by'
    ];

    protected $casts = [
        'can_mark_attendance' => 'boolean',
        'can_approve_leave' => 'boolean',
    ];

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

    public function assigner()
    {
        return $this->belongsTo(Staff::class, 'assigned_by');
    }
}