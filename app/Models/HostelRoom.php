<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelRoom extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'hostel_id', 'room_type_id', 'room_number', 'floor', 'capacity',
        'current_occupancy', 'status', 'notes',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(HostelRoomType::class, 'room_type_id');
    }

    public function allocations()
    {
        return $this->hasMany(StudentHostelAllocation::class, 'room_id');
    }

    public function activeAllocations()
    {
        return $this->allocations()->where('status', 'active');
    }

    public function hasVacancy()
    {
        return $this->current_occupancy < $this->capacity;
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
