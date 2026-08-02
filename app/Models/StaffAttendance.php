<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $table = 'staff_attendances';

    protected $fillable = [
        'staff_id',
        'date',
        'status',
        // Half Day Fields
        'half_day_type',
        'half_day_in_time',
        'half_day_out_time',
        'half_day_reason',
        // Late Fields
        'late_arrival_time',
        'late_minutes',
        'late_reason',
        // Check-in/out
        'check_in',
        'check_out',
        'arrival_time',
        'departure_time',
        // Work metrics
        'overtime_minutes',
        'work_hours',
        'total_minutes',
        // Class related
        'class_section_id',
        'subject_id',
        'time_slot_id',
        'duration_minutes',
        'class_taken',
        // Common
        'remarks',
        'early_departure_reason',
        'marked_by',
        'is_approved',
    ];

    protected $casts = [
        'date' => 'date',
        'is_approved' => 'boolean',
        'class_taken' => 'boolean',
        'half_day_in_time' => 'datetime',
        'half_day_out_time' => 'datetime',
        'late_arrival_time' => 'datetime',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'arrival_time' => 'datetime',
        'departure_time' => 'datetime',
    ];

    // Status Constants
    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_LATE = 'late';
    const STATUS_HALF_DAY = 'half_day';
    const STATUS_HOLIDAY = 'holiday';
    const STATUS_LEAVE = 'leave';
    const STATUS_ON_DUTY = 'on_duty';
    const STATUS_WEEKEND = 'weekend';
    const STATUS_TRAINING = 'training';

    // Half Day Types
    const HALF_DAY_MORNING = 'morning';
    const HALF_DAY_AFTERNOON = 'afternoon';
    const HALF_DAY_CUSTOM = 'custom';

    /**
     * Get all status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PRESENT => 'Present',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_LATE => 'Late',
            self::STATUS_HALF_DAY => 'Half Day',
            self::STATUS_HOLIDAY => 'Holiday',
            self::STATUS_LEAVE => 'Leave',
            self::STATUS_ON_DUTY => 'On Duty',
            self::STATUS_WEEKEND => 'Weekend',
            self::STATUS_TRAINING => 'Training',
        ];
    }

    /**
     * Get half day type options
     */
    public static function getHalfDayTypeOptions()
    {
        return [
            self::HALF_DAY_MORNING => 'Morning Session',
            self::HALF_DAY_AFTERNOON => 'Afternoon Session',
            self::HALF_DAY_CUSTOM => 'Custom Hours',
        ];
    }

    /**
     * Get status label with badge
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_PRESENT => '<span class="badge bg-success">Present</span>',
            self::STATUS_ABSENT => '<span class="badge bg-danger">Absent</span>',
            self::STATUS_LATE => '<span class="badge bg-warning">Late</span>',
            self::STATUS_HALF_DAY => '<span class="badge bg-info">Half Day</span>',
            self::STATUS_HOLIDAY => '<span class="badge bg-secondary">Holiday</span>',
            self::STATUS_LEAVE => '<span class="badge bg-primary">Leave</span>',
            self::STATUS_ON_DUTY => '<span class="badge bg-purple">On Duty</span>',
            self::STATUS_WEEKEND => '<span class="badge bg-secondary">Weekend</span>',
            self::STATUS_TRAINING => '<span class="badge bg-info">Training</span>',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status color for styling
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PRESENT => 'success',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_LATE => 'warning',
            self::STATUS_HALF_DAY => 'info',
            self::STATUS_HOLIDAY => 'secondary',
            self::STATUS_LEAVE => 'primary',
            self::STATUS_ON_DUTY => 'purple',
            self::STATUS_WEEKEND => 'secondary',
            self::STATUS_TRAINING => 'info',
            default => 'secondary'
        };
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(Staff::class, 'marked_by');
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', self::STATUS_ABSENT);
    }

    public function scopeLate($query)
    {
        return $query->where('status', self::STATUS_LATE);
    }

    public function scopeHalfDay($query)
    {
        return $query->where('status', self::STATUS_HALF_DAY);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Calculate work hours
     */
    public function calculateWorkHours()
    {
        if ($this->check_in && $this->check_out) {
            $in = \Carbon\Carbon::parse($this->check_in);
            $out = \Carbon\Carbon::parse($this->check_out);
            $minutes = $in->diffInMinutes($out);
            $this->total_minutes = $minutes;
            $this->work_hours = round($minutes / 60, 2);
            return $this->work_hours;
        }
        return 0;
    }

    /**
     * Calculate overtime minutes
     */
    public function calculateOvertime($standardEndTime = '18:00:00')
    {
        if ($this->check_out) {
            $out = \Carbon\Carbon::parse($this->check_out);
            $standard = \Carbon\Carbon::parse($standardEndTime);
            
            if ($out->gt($standard)) {
                $this->overtime_minutes = $standard->diffInMinutes($out);
                return $this->overtime_minutes;
            }
        }
        $this->overtime_minutes = 0;
        return 0;
    }

    /**
     * Calculate late minutes
     */
    public function calculateLateMinutes($arrivalTime, $schoolStartTime = '08:00:00')
    {
        $arrival = \Carbon\Carbon::parse($arrivalTime);
        $start = \Carbon\Carbon::parse($schoolStartTime);
        
        if ($arrival->gt($start)) {
            $this->late_minutes = $arrival->diffInMinutes($start);
            return $this->late_minutes;
        }
        
        $this->late_minutes = 0;
        return 0;
    }

    /**
     * Get attendance value for payroll
     */
    public function getAttendanceValue()
    {
        return match($this->status) {
            self::STATUS_PRESENT, self::STATUS_ON_DUTY, self::STATUS_TRAINING => 1.0,
            self::STATUS_HALF_DAY => 0.5,
            self::STATUS_LATE => 0.75,
            self::STATUS_LEAVE => 0.0,
            self::STATUS_ABSENT => 0.0,
            default => 0.0
        };
    }

    /**
     * Get attendance summary for a staff member
     */
    public static function getStaffSummary($staffId, $month = null)
    {
        if (!$month) {
            $month = now()->format('Y-m');
        }
        
        $attendances = self::where('staff_id', $staffId)
            ->where('date', 'like', $month . '%')
            ->get();

        return [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', self::STATUS_PRESENT)->count(),
            'absent' => $attendances->where('status', self::STATUS_ABSENT)->count(),
            'late' => $attendances->where('status', self::STATUS_LATE)->count(),
            'half_day' => $attendances->where('status', self::STATUS_HALF_DAY)->count(),
            'leave' => $attendances->where('status', self::STATUS_LEAVE)->count(),
            'holiday' => $attendances->where('status', self::STATUS_HOLIDAY)->count(),
            'on_duty' => $attendances->where('status', self::STATUS_ON_DUTY)->count(),
            'total_work_hours' => $attendances->sum('work_hours'),
            'total_overtime_minutes' => $attendances->sum('overtime_minutes'),
            'half_day_reasons' => $attendances->where('status', self::STATUS_HALF_DAY)
                ->pluck('half_day_reason', 'date')
                ->toArray(),
            'late_reasons' => $attendances->where('status', self::STATUS_LATE)
                ->pluck('late_reason', 'date')
                ->toArray(),
            'attendance_percentage' => $attendances->count() > 0 
                ? round(($attendances->whereIn('status', [self::STATUS_PRESENT, self::STATUS_LATE, self::STATUS_HALF_DAY, self::STATUS_ON_DUTY])->count() / $attendances->count()) * 100, 2)
                : 0,
        ];
    }
}