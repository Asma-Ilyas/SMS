<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'staff_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'late_minutes',
        'overtime_minutes',
        'work_hours',
        'remarks',
        'is_approved',
    ];

    protected $casts = [
        'date'          => 'date',
        'check_in'      => 'datetime:H:i:s',
        'check_out'     => 'datetime:H:i:s',
        'late_minutes'  => 'integer',
        'overtime_minutes' => 'integer',
        'work_hours'    => 'decimal:2',
        'is_approved'   => 'boolean',
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    // ------------------------------------------------------------------
    // Accessors (optional)
    // ------------------------------------------------------------------
    public function getWorkHoursLabelAttribute()
    {
        if ($this->work_hours === null) return '—';
        $hours = floor($this->work_hours);
        $minutes = round(($this->work_hours - $hours) * 60);
        return "{$hours}h {$minutes}m";
    }

    public function getLateMinutesLabelAttribute()
    {
        if ($this->late_minutes === null) return '—';
        return $this->late_minutes . ' min';
    }

    // ------------------------------------------------------------------
    // Helper to auto‑calculate status, work hours, late/overtime
    // (used by controllers)
    // ------------------------------------------------------------------
    public static function calculateFromTimes($staff, $checkInTime, $checkOutTime = null)
    {
        $category = $staff->category;
        $lateMinutes = 0;
        $overtimeMinutes = 0;
        $workHours = 0;

        if ($checkInTime) {
            $expectedArrival = $category?->arrival_time ? Carbon::parse($category->arrival_time) : null;
            if ($expectedArrival && $checkInTime->gt($expectedArrival)) {
                $lateMinutes = $checkInTime->diffInMinutes($expectedArrival);
            }
        }

        if ($checkInTime && $checkOutTime) {
            $workMinutes = $checkInTime->diffInMinutes($checkOutTime);
            $workHours = round($workMinutes / 60, 2);

            $expectedDeparture = $category?->departure_time ? Carbon::parse($category->departure_time) : null;
            if ($expectedDeparture && $checkOutTime->gt($expectedDeparture)) {
                $overtimeMinutes = $expectedDeparture->diffInMinutes($checkOutTime);
            }
        }

        $status = 'absent';
        if ($checkInTime) {
            $status = ($lateMinutes > 0) ? 'late' : 'present';
        }
        if ($workHours > 0 && $workHours < 4) {
            $status = 'half‑day';
        }

        return [
            'status'           => $status,
            'late_minutes'     => $lateMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'work_hours'       => $workHours,
        ];
    }
}