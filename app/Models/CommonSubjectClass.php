<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonSubjectClass extends Model
{
    use HasFactory;

    protected $table = 'common_subject_classes';

    protected $fillable = [
        'subject_id',
        'name',
        'description',
        'room_id',
        'class_section_ids',
        'is_active'
    ];

    protected $casts = [
        'class_section_ids' => 'array',  // THIS IS CRITICAL
        'is_active' => 'boolean',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getSectionsAttribute()
    {
        return ClassSection::whereIn('id', $this->class_section_ids ?? [])->get();
    }

    public function getSectionNamesAttribute()
    {
        $sections = $this->getSectionsAttribute();
        return $sections->pluck('full_name')->implode(', ');
    }
}