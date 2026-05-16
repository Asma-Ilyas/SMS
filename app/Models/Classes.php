<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
     protected $table = 'classes';
    protected $fillable = [
        'academic_session_id',
        'grade_id',
        'stream_id',
        'elective_track_id',
        'section',
        'capacity'
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function electiveTrack()
    {
        return $this->belongsTo(ElectiveTrack::class);
    }

    // Accessor for full class name
    public function getFullNameAttribute()
    {
        $name = $this->grade->name . ' ' . $this->stream->name;
        if ($this->electiveTrack) {
            $name .= ' (' . $this->electiveTrack->name . ')';
        }
        if ($this->section) {
            $name .= ' - Section ' . $this->section;
        }
        return $name;
    }

    public function students()
{
    return $this->hasMany(Student::class, 'class_id');
}

public function discounts()
{
    return $this->belongsToMany(Discount::class, 'discount_class', 'classes_id', 'discount_id');
}
public function subjects()
{
    return $this->belongsToMany(Subject::class, 'class_subject_teacher', 'class_id', 'subject_id')
                ->withPivot('teacher_id', 'academic_session_id', 'max_weekly_periods', 'term')
                ->withTimestamps();
}

public function teachers()
{
    return $this->belongsToMany(Staff::class, 'class_subject_teacher', 'class_id', 'teacher_id')
                ->withPivot('subject_id', 'academic_session_id', 'max_weekly_periods', 'term')
                ->withTimestamps();
}
}
