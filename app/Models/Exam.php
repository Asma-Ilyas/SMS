<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_type_id',
        'exam_group_id',
        'class_id',
        'name',
        'exam_center',
        'start_date',
        'end_date',
        'time_table',
        'description',
        'is_published',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'is_published' => 'boolean',
    ];

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function examGroup()
    {
        return $this->belongsTo(ExamGroup::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function marks()
    {
        return $this->hasMany(ExamMark::class);
    }

   public function subjects()
{
    return Subject::whereHas('classes', function ($query) {
        $query->where('class_id', $this->class_id);
    })->get();
}

    public function students()
{
    return Student::where('class_id', $this->class_id)
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();
}

    // Accessor for formatted start date
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('d-m-Y') : null;
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('d-m-Y') : null;
    }
}