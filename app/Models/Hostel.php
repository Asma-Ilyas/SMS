<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hostel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'type', 'address', 'warden_id', 'total_capacity',
        'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function warden()
    {
        return $this->belongsTo(Staff::class, 'warden_id');
    }

    public function staffAssignments()
    {
        return $this->hasMany(HostelStaffAssignment::class);
    }

    public function rooms()
    {
        return $this->hasMany(HostelRoom::class);
    }

    public function feeTypes()
    {
        return $this->hasMany(HostelFeeType::class);
    }

    public function allocations()
    {
        return $this->hasMany(StudentHostelAllocation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function occupiedCount()
    {
        return $this->allocations()->where('status', 'active')->count();
    }
}
