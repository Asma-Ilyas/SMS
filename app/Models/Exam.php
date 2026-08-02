<?php
// app/Models/Exam.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_type_id',
        'exam_group_id',
        'class_section_id',
        'name',
        'start_date',
        'end_date',
        'description',
        'is_published',
        'passing_percentage',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_published' => 'boolean',
        'passing_percentage' => 'decimal:2',
    ];

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function examGroup()
    {
        return $this->belongsTo(ExamGroup::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function marks()
    {
        return $this->hasMany(ExamMark::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function subjectMarks()
    {
        return $this->hasMany(ExamSubjectMark::class);
    }

    public function subjectSchedules()
    {
        return $this->hasMany(ExamSubjectSchedule::class)->orderBy('sort_order');
    }

    public function getTotalStudentsAttribute()
    {
        return Student::where('class_section_id', $this->class_section_id)->count();
    }

    public function getTotalMarksAttribute()
    {
        return $this->marks()->sum('marks_obtained');
    }

    public function getMaxMarksAttribute()
    {
        return $this->marks()->sum('max_marks');
    }

    public function getPassingMarksAttribute()
    {
        return ($this->total_marks * $this->passing_percentage) / 100;
    }
}