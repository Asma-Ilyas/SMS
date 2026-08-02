<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Leave extends Model
{
    protected $fillable = ['staff_id', 'start_date', 'end_date', 'type', 'status', 'reason'];
    public function staff() { return $this->belongsTo(Staff::class, 'staff_id'); }
}