<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    protected $fillable = ['name', 'grades', 'is_default'];
    protected $casts = ['grades' => 'array', 'is_default' => 'boolean'];
}