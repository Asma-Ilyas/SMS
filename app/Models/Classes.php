<?php
// app/Models/Classes.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'academic_session_id',
        'grade_id',
        'stream_id',
        'elective_track_id',
        'capacity',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function electiveTrack()
    {
        return $this->belongsTo(ElectiveTrack::class);
    }

    public function sections()
    {
        return $this->hasMany(ClassSection::class, 'class_id');
    }

    public function classSections()
    {
        return $this->hasMany(ClassSection::class, 'class_id');
    }

    public function getFullNameAttribute()
    {
        $gradeName = $this->grade->name ?? 'Class';
        $streamName = $this->stream->name ?? '';
        return trim($gradeName . ' ' . $streamName);
    }
}