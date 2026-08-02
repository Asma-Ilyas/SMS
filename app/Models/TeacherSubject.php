<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    protected $table = 'teacher_subjects';
    protected $fillable = ['teacher_id', 'subject_id', 'preference_level'];
    public function teacher() { return $this->belongsTo(Staff::class, 'teacher_id'); }
    public function subject() { return $this->belongsTo(Subject::class); }
}
