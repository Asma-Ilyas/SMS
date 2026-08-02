<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSection extends Model
{
    protected $table = 'class_sections';
    protected $fillable = ['class_id', 'section_name', 'capacity', 'student_count','student_strength'];
    
    public function class() { return $this->belongsTo(Classes::class); }
    public function subjectAssignments() { return $this->hasMany(SubjectAssignment::class); }
    public function timetableEntries() { return $this->hasMany(TimetableEntry::class); }
    public function students() { return $this->hasMany(Student::class, 'class_section_id'); }
    public function attendances() { return $this->hasMany(StudentAttendance::class, 'class_section_id'); }
    public function exams() { return $this->hasMany(Exam::class, 'class_section_id'); }
    public function examResults() { return $this->hasMany(ExamResult::class, 'class_section_id'); }
    
    /**
     * Get the grade through the class relationship
     */
    public function grade()
    {
        return $this->hasOneThrough(
            Grade::class,
            Classes::class,
            'id', // Foreign key on classes table
            'id', // Foreign key on grades table
            'class_id', // Local key on class_sections table
            'grade_id' // Local key on classes table
        );
    }
    
    public function getFullNameAttribute()
    {
        $className = optional($this->class->grade)->name ?? 'Class';
        return $className . ' - Section ' . $this->section_name;
    }

    public function roomAssignments()
    {
        return $this->hasMany(\App\Models\SectionRoomAssignment::class, 'section_id');
    }
    
    public function currentRoomAssignment()
    {
        $activeSession = \App\Models\SchoolTiming::where('is_active', true)->first();
        if (!$activeSession) return null;

        return $this->roomAssignments()
                    ->where('school_timing_id', $activeSession->id)
                    ->with('room')
                    ->first();
    }

    public function currentRoom()
    {
        return optional($this->currentRoomAssignment())->room;
    }
}