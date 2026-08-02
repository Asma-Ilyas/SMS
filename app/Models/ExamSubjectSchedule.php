<?php
// app/Models/ExamSubjectSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubjectSchedule extends Model
{
    use HasFactory;

    protected $table = 'exam_subject_schedules';

    protected $fillable = [
        'exam_id',
        'subject_id',
        'exam_date',
        'start_time',
        'end_time',
        'room',
        'sort_order',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}