<?php
// app/Models/ExamSubjectMark.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubjectMark extends Model
{
    use HasFactory;

    protected $table = 'exam_subject_marks';

    protected $fillable = [
        'exam_id',
        'subject_id',
        'max_marks',
        'passing_marks',
        'weightage',
    ];

    protected $casts = [
        'max_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'weightage' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getPassingPercentageAttribute()
    {
        return $this->max_marks > 0 
            ? round(($this->passing_marks / $this->max_marks) * 100, 2) 
            : 0;
    }
}