<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'grades',
        'is_default',
    ];

    protected $casts = [
        'grades' => 'array',  // THIS IS CRITICAL - converts JSON to array
        'is_default' => 'boolean',
    ];

    public function getGrade($percentage)
    {
        $grades = $this->grades; // Already an array because of the cast
        foreach ($grades as $grade) {
            if ($percentage >= $grade['min'] && $percentage <= $grade['max']) {
                return $grade['grade'];
            }
        }
        return 'F';
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public static function getDefault()
    {
        return self::where('is_default', true)->first();
    }
}