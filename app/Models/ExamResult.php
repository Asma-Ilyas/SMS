<?php
// app/Models/ExamResult.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'class_section_id',
        'total_marks',
        'total_max_marks',
        'percentage',
        'grade',
        'remarks',
        'rank_in_class',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function getGradeAttribute()
    {
        // This will be calculated from grade scale
        return $this->attributes['grade'] ?? 'N/A';
    }
}