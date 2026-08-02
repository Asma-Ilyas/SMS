<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_timing_id',
        'sort_order',
        'label',
        'start_time',
        'end_time',
        'type',
        'period_number',
        'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'period_number' => 'integer',
        'sort_order'    => 'integer',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    public function schoolTiming()
    {
        return $this->belongsTo(SchoolTiming::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePeriods($query)
    {
        return $query->where('type', 'period');
    }

    public function scopeBreaks($query)
    {
        return $query->where('type', 'break');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // ─── Helpers ────────────────────────────────────────────────────

    public function getDurationMinutesAttribute(): int
    {
        [$sh, $sm] = explode(':', $this->start_time);
        [$eh, $em] = explode(':', $this->end_time);
        return ((int)$eh * 60 + (int)$em) - ((int)$sh * 60 + (int)$sm);
    }

    public function getFormattedRangeAttribute(): string
    {
        return $this->formatTime($this->start_time) . ' – ' . $this->formatTime($this->end_time);
    }

    private function formatTime(string $time): string
    {
        return date('g:i A', strtotime($time));
    }
}