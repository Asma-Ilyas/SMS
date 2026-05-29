<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSection extends Model
{
    protected $table = 'class_sections';
    protected $fillable = ['class_id', 'section_name', 'capacity', 'student_count'];

    public function class() { return $this->belongsTo(Classes::class); }
    public function subjectAssignments() { return $this->hasMany(SubjectAssignment::class); }
    public function timetableEntries() { return $this->hasMany(TimetableEntry::class); }
    public function students() { return $this->hasMany(Student::class, 'class_section_id'); }

    // Helper to get full name
    public function getFullNameAttribute()
    {
        $className = $this->class->name ?? 'Class';
        return $className . ' - Section ' . $this->section_name;
    }
}