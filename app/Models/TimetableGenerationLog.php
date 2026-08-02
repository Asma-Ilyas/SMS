<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableGenerationLog extends Model
{
    protected $fillable = ['generated_at', 'total_entries', 'conflicts_resolved', 'errors'];
    protected $casts = ['errors' => 'array'];
}
