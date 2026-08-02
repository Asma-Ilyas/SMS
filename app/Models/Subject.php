<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'code', 'description', 'type', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function teacherSubjects() { return $this->hasMany(TeacherSubject::class); }
    public function subjectAssignments() { return $this->hasMany(SubjectAssignment::class); }
    public function timetableEntries() { return $this->hasMany(TimetableEntry::class); }
    public function examMarks() { return $this->hasMany(ExamMark::class); }
    public function assignments()
{
    return $this->hasMany(SubjectAssignment::class);
}

public function classSections()
{
    return $this->belongsToMany(ClassSection::class, 'subject_assignments', 'subject_id', 'class_section_id');
}
}