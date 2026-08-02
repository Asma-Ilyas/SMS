<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $table = 'student_attendance';

    protected $fillable = [
        'student_id',
        'class_section_id',
        'subject_id',
        'teacher_id',
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
        // Common
        'remarks',
        'leave_reason',
        'is_approved',
        'approved_by',
        'approved_at',
        'marked_by'
    ];

    protected $casts = [
        'date' => 'date',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'half_day_in_time' => 'datetime',
        'half_day_out_time' => 'datetime',
        'late_arrival_time' => 'datetime',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    // Status Constants
    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_LATE = 'late';
    const STATUS_HALF_DAY = 'half_day';
    const STATUS_LEAVE = 'leave';
    const STATUS_HOLIDAY = 'holiday';
    const STATUS_ON_DUTY = 'on_duty';

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
            self::STATUS_LEAVE => 'Leave',
            self::STATUS_HOLIDAY => 'Holiday',
            self::STATUS_ON_DUTY => 'On Duty',
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
            self::STATUS_LEAVE => '<span class="badge bg-primary">Leave</span>',
            self::STATUS_HOLIDAY => '<span class="badge bg-secondary">Holiday</span>',
            self::STATUS_ON_DUTY => '<span class="badge bg-purple">On Duty</span>',
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
            self::STATUS_LEAVE => 'primary',
            self::STATUS_HOLIDAY => 'secondary',
            self::STATUS_ON_DUTY => 'purple',
            default => 'secondary'
        };
    }

    /**
     * Get half day type label
     */
    public function getHalfDayTypeLabelAttribute()
    {
        return self::getHalfDayTypeOptions()[$this->half_day_type] ?? 'N/A';
    }

    /**
     * Check if attendance is approved
     */
    public function getIsApprovedLabelAttribute()
    {
        return $this->is_approved 
            ? '<span class="badge bg-success">Approved</span>'
            : '<span class="badge bg-warning">Pending</span>';
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Staff::class, 'approved_by');
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

    public function scopeLeave($query)
    {
        return $query->where('status', self::STATUS_LEAVE);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Calculate late minutes
     */
    public function calculateLateMinutes($arrivalTime, $schoolStartTime = '08:00:00')
    {
        $arrival = \Carbon\Carbon::parse($arrivalTime);
        $start = \Carbon\Carbon::parse($schoolStartTime);
        
        if ($arrival->gt($start)) {
            return $arrival->diffInMinutes($start);
        }
        
        return 0;
    }

    /**
     * Get attendance value for payroll calculation
     */
    public function getAttendanceValue()
    {
        return match($this->status) {
            self::STATUS_PRESENT, self::STATUS_ON_DUTY => 1.0,
            self::STATUS_HALF_DAY => 0.5,
            self::STATUS_LATE => 0.75, // Late but present
            self::STATUS_LEAVE => 0.0, // Treated as absent for payroll
            self::STATUS_ABSENT => 0.0,
            default => 0.0
        };
    }

    /**
     * Check if student was present (including late and half-day)
     */
    public function wasPresent()
    {
        return in_array($this->status, [
            self::STATUS_PRESENT,
            self::STATUS_LATE,
            self::STATUS_HALF_DAY,
            self::STATUS_ON_DUTY
        ]);
    }

    /**
     * Get attendance summary for a student
     */
    public static function getStudentSummary($studentId, $month = null)
    {
        if (!$month) {
            $month = now()->format('Y-m');
        }
        
        $attendances = self::where('student_id', $studentId)
            ->where('date', 'like', $month . '%')
            ->get();

        return [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', self::STATUS_PRESENT)->count(),
            'absent' => $attendances->where('status', self::STATUS_ABSENT)->count(),
            'late' => $attendances->where('status', self::STATUS_LATE)->count(),
            'half_day' => $attendances->where('status', self::STATUS_HALF_DAY)->count(),
            'leave' => $attendances->where('status', self::STATUS_LEAVE)->count(),
            'half_day_reasons' => $attendances->where('status', self::STATUS_HALF_DAY)
                ->pluck('half_day_reason', 'date')
                ->toArray(),
            'late_reasons' => $attendances->where('status', self::STATUS_LATE)
                ->pluck('late_reason', 'date')
                ->toArray(),
            'attendance_percentage' => $attendances->count() > 0 
                ? round(($attendances->whereIn('status', [self::STATUS_PRESENT, self::STATUS_LATE, self::STATUS_HALF_DAY])->count() / $attendances->count()) * 100, 2)
                : 0,
        ];
    }
}