<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelStaffAssignment extends Model
{
    protected $fillable = [
        'hostel_id', 'staff_id', 'role', 'assigned_date', 'is_active',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
