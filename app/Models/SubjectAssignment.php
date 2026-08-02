<?php
// app/Models/SubjectAssignment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectAssignment extends Model
{
    use HasFactory;

    protected $table = 'subject_assignments';

    protected $fillable = [
        'class_section_id',
        'subject_id',
        'teacher_id',
        'weekly_frequency',
        'is_elective',
        'is_active',
    ];

    protected $casts = [
        'weekly_frequency' => 'integer',
        'is_elective' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }
}