<?php
// app/Models/ExamMark.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'subject_id',
        'marks_obtained',
        'max_marks',
        'passing_marks',
        'remarks',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'max_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getPercentageAttribute()
    {
        return $this->max_marks > 0 
            ? round(($this->marks_obtained / $this->max_marks) * 100, 2) 
            : 0;
    }

    public function getIsPassAttribute()
    {
        return $this->marks_obtained >= $this->passing_marks;
    }
}