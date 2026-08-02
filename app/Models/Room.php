<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'room_number',
        'block',        // legacy string field
        'floor',        // legacy integer field
        'type',
        'capacity',
        'has_projector',
        'has_ac',
        'is_available',
        'notes',
        'block_id',     // foreign key to blocks table
        'floor_id',     // foreign key to floors table
    ];

    protected $casts = [
        'has_projector' => 'boolean',
        'has_ac'        => 'boolean',
        'is_available'  => 'boolean',
        'capacity'      => 'integer',
        'floor'         => 'integer',
        'block_id'      => 'integer',
        'floor_id'      => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function assignments()
    {
        return $this->hasMany(SectionRoomAssignment::class);
    }

    public function currentAssignment()
    {
        $activeSession = SchoolTiming::where('is_active', true)->first();
        if (!$activeSession) return null;

        return $this->assignments()
                    ->where('school_timing_id', $activeSession->id)
                    ->with('section')
                    ->first();
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeClassrooms($query)
    {
        return $query->where('type', 'classroom');
    }

    public function scopeWithCapacity($query, int $minCapacity)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }

    /**
     * Rooms not yet assigned in a given session.
     */
    public function scopeUnassignedInSession($query, int $sessionId)
    {
        return $query->whereDoesntHave('assignments', fn($q) =>
            $q->where('school_timing_id', $sessionId)
        );
    }

    // ─── Helpers ─────────────────────────────────────────────────

    public function isAssignedInSession(int $sessionId): bool
    {
        return $this->assignments()->where('school_timing_id', $sessionId)->exists();
    }

    public function getFitLabel(int $sectionStrength): string
    {
        $diff = $this->capacity - $sectionStrength;

        if ($diff < 0)           return 'over_capacity';
        if ($diff <= 3)          return 'tight';
        if ($diff <= 10)         return 'perfect';
        return 'comfortable';
    }

    public function getFloorLabelAttribute(): string
    {
        return $this->floor === 0 ? 'Ground Floor' : 'Floor ' . $this->floor;
    }

    public function getFullNameAttribute(): string
    {
        return $this->room_number . ' — ' . $this->name;
    }
}