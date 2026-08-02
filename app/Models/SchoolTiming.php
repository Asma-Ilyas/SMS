<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolTiming extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_name',
        'session_start',
        'session_end',
        'school_start',
        'school_end',
        'period_duration',
        'breaks',
        'has_activity',
        'activity_label',
        'activity_start',
        'activity_end',
        'is_active',
    ];

    protected $casts = [
        'breaks'       => 'array',
        'has_activity' => 'boolean',
        'is_active'    => 'boolean',
        'session_start' => 'date',
        'session_end'   => 'date',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class)->orderBy('sort_order');
    }

    public function periods()
    {
        return $this->hasMany(TimeSlot::class)->where('type', 'period')->orderBy('sort_order');
    }

    // ─── Helpers ────────────────────────────────────────────────────

    /**
     * Mark this timing as active and deactivate all others.
     */
    public function activate(): void
    {
        static::query()->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    /**
     * Auto-generate time_slots from current settings.
     * Deletes existing slots first, then rebuilds.
     */
    public function generateSlots(): void
    {
        $this->timeSlots()->delete();

        $slots     = [];
        $sortOrder = 1;

        // Build a flat timeline: breaks keyed by their start time
        $breakMap = [];
        foreach ($this->breaks ?? [] as $break) {
            $breakMap[$break['start']] = $break;
        }

        // Activity as a special entry
        $activityStart = $this->has_activity ? $this->activity_start : null;

        $current     = $this->school_start;   // "HH:MM"
        $schoolEnd   = $this->school_end;
        $duration    = $this->period_duration; // minutes
        $periodNum   = 1;

        while ($this->timeToMinutes($current) < $this->timeToMinutes($schoolEnd)) {

            // ── Activity slot ────────────────────────────────────────
            if ($activityStart && $current === $activityStart) {
                $slots[] = [
                    'school_timing_id' => $this->id,
                    'sort_order'       => $sortOrder++,
                    'label'            => $this->activity_label ?? 'Activity',
                    'start_time'       => $this->activity_start,
                    'end_time'         => $this->activity_end,
                    'type'             => 'activity',
                    'period_number'    => null,
                    'is_active'        => true,
                ];
                $current = $this->activity_end;
                continue;
            }

            // ── Break slot ───────────────────────────────────────────
            if (isset($breakMap[$current])) {
                $break = $breakMap[$current];
                $slots[] = [
                    'school_timing_id' => $this->id,
                    'sort_order'       => $sortOrder++,
                    'label'            => $break['label'],
                    'start_time'       => $break['start'],
                    'end_time'         => $break['end'],
                    'type'             => 'break',
                    'period_number'    => null,
                    'is_active'        => true,
                ];
                $current = $break['end'];
                continue;
            }

            // ── Period slot ──────────────────────────────────────────
            $nextMinutes = $this->timeToMinutes($current) + $duration;

            // Clamp: don't overflow school end
            $endMinutes = min($nextMinutes, $this->timeToMinutes($schoolEnd));

            // Clamp: don't overflow into the next break / activity
            foreach ($breakMap as $bStart => $_) {
                $bMin = $this->timeToMinutes($bStart);
                if ($bMin > $this->timeToMinutes($current) && $bMin < $endMinutes) {
                    $endMinutes = $bMin;
                }
            }
            if ($activityStart) {
                $aMin = $this->timeToMinutes($activityStart);
                if ($aMin > $this->timeToMinutes($current) && $aMin < $endMinutes) {
                    $endMinutes = $aMin;
                }
            }

            $endTime = $this->minutesToTime($endMinutes);

            $slots[] = [
                'school_timing_id' => $this->id,
                'sort_order'       => $sortOrder++,
                'label'            => 'Period ' . $periodNum,
                'start_time'       => $current,
                'end_time'         => $endTime,
                'type'             => 'period',
                'period_number'    => $periodNum++,
                'is_active'        => true,
            ];

            $current = $endTime;
        }

        TimeSlot::insert(array_map(fn($s) => array_merge($s, [
            'created_at' => now(),
            'updated_at' => now(),
        ]), $slots));
    }

    // ─── Private Utilities ───────────────────────────────────────────

    private function timeToMinutes(string $time): int
    {
        [$h, $m] = explode(':', $time);
        return (int)$h * 60 + (int)$m;
    }

    private function minutesToTime(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    public function generateTimeSlots()
{
    // Delete old slots
    $this->timeSlots()->delete();

    $start = strtotime($this->school_start);
    $end = strtotime($this->school_end);
    $duration = $this->period_duration * 60;
    $breaks = is_string($this->breaks) ? json_decode($this->breaks, true) : ($this->breaks ?? []);
    $sortOrder = 1;

    // Add activity if enabled
    if ($this->has_activity && $this->activity_start && $this->activity_end) {
        $this->timeSlots()->create([
            'sort_order' => $sortOrder++,
            'label' => $this->activity_label ?: 'Activity',
            'start_time' => $this->activity_start,
            'end_time' => $this->activity_end,
            'type' => 'activity',
            'period_number' => null,
            'is_active' => true,
        ]);
        $current = strtotime($this->activity_end);
    } else {
        $current = $start;
    }

    $periodNumber = 1;
    while ($current < $end) {
        $break = null;
        foreach ($breaks as $b) {
            $breakStart = strtotime($b['start']);
            $breakEnd = strtotime($b['end']);
            if ($current >= $breakStart && $current < $breakEnd) {
                $break = $b;
                break;
            }
        }
        if ($break) {
            $this->timeSlots()->create([
                'sort_order' => $sortOrder++,
                'label' => $break['label'],
                'start_time' => $break['start'],
                'end_time' => $break['end'],
                'type' => 'break',
                'period_number' => null,
                'is_active' => true,
            ]);
            $current = $breakEnd;
            continue;
        }
        $periodEnd = min($current + $duration, $end);
        $this->timeSlots()->create([
            'sort_order' => $sortOrder++,
            'label' => 'Period ' . $periodNumber,
            'start_time' => date('H:i:s', $current),
            'end_time' => date('H:i:s', $periodEnd),
            'type' => 'period',
            'period_number' => $periodNumber++,
            'is_active' => true,
        ]);
        $current = $periodEnd;
    }
}
}