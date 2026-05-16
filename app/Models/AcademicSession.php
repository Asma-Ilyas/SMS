<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    protected $table = 'academic_sessions';
    protected $fillable = ['name', 'start_date', 'end_date', 'is_active', 'description'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function classes()
    {
        return $this->hasMany(Classes::class);
    }

    // Scope to get active session
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
