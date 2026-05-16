<?php

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
        if ($this->max_marks && $this->max_marks > 0) {
            return round(($this->marks_obtained / $this->max_marks) * 100, 2);
        }
        return 0;
    }

    public function getGradeAttribute()
    {
        $percentage = $this->percentage;
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    public function getIsPassAttribute()
    {
        $passing = $this->passing_marks ?? ($this->max_marks * 0.33);
        return $this->marks_obtained >= $passing;
    }
}