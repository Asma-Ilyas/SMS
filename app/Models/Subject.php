<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'code', 'description', 'type', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function teachers()
    {
        return $this->belongsToMany(Staff::class, 'class_subject_teacher', 'subject_id', 'teacher_id')
                    ->withPivot('class_id', 'academic_session_id', 'max_weekly_periods', 'term')
                    ->withTimestamps();
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_subject_teacher', 'subject_id', 'class_id')
                    ->withPivot('teacher_id', 'academic_session_id', 'max_weekly_periods', 'term')
                    ->withTimestamps();
    }

    public function marks()
{
    return $this->hasMany(ExamMark::class, 'subject_id');
}
}