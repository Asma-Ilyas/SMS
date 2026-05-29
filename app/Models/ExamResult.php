<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $fillable = [
        'exam_id', 'student_id', 'class_id', 'section_id',
        'total_marks', 'total_max_marks', 'percentage', 'grade', 'remarks', 'rank_in_class'
    ];

    public function exam() { return $this->belongsTo(Exam::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function class() { return $this->belongsTo(Classes::class); }
    public function section() { return $this->belongsTo(Section::class); }
}