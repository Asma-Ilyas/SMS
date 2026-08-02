<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SectionRoomAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'room_id',
        'school_timing_id',
        'assignment_type',
        'section_strength',
        'room_capacity',
        'fit_status',
        'suggested_room_id',
        'notes',
    ];

    protected $casts = [
        'section_strength' => 'integer',
        'room_capacity'    => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────

   public function section()
{
    return $this->belongsTo(ClassSection::class, 'section_id');
}
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function schoolTiming()
    {
        return $this->belongsTo(SchoolTiming::class);
    }

    public function suggestedRoom()
    {
        return $this->belongsTo(Room::class, 'suggested_room_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────

    public function isOverCapacity(): bool
    {
        return $this->fit_status === 'over_capacity';
    }

    public function isTight(): bool
    {
        return $this->fit_status === 'tight';
    }

    public function getFitBadgeClassAttribute(): string
    {
        return match($this->fit_status) {
            'perfect'       => 'bg-success',
            'comfortable'   => 'bg-primary',
            'tight'         => 'bg-warning text-dark',
            'over_capacity' => 'bg-danger',
            default         => 'bg-secondary',
        };
    }

    public function getFitLabelAttribute(): string
    {
        return match($this->fit_status) {
            'perfect'       => 'Perfect Fit',
            'comfortable'   => 'Comfortable',
            'tight'         => 'Tight Fit',
            'over_capacity' => 'Over Capacity',
            default         => 'Unknown',
        };
    }

    public function getSpareSeatsAttribute(): int
    {
        return $this->room_capacity - $this->section_strength;
    }
}