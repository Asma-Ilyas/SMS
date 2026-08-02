<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendanceSummary extends Model
{
    use HasFactory;

    protected $table = 'student_attendance_summaries';

    protected $fillable = [
        'student_id',
        'month',
        'total_days',
        'present_days',
        'absent_days',
        'late_days',
        'half_days',
        'leave_days',
        'holiday_days',
        'on_duty_days',
        'attendance_percentage',
        'half_day_reasons',
        'late_reasons',
    ];

    protected $casts = [
        'half_day_reasons' => 'array',
        'late_reasons' => 'array',
        'attendance_percentage' => 'decimal:2',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopeForMonth($query, $month)
    {
        return $query->where('month', $month);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Generate summary for a student for a specific month
     */
    public static function generateSummary($studentId, $year, $month)
    {
        $monthString = sprintf('%d-%02d', $year, $month);
        
        $attendances = StudentAttendance::where('student_id', $studentId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $halfDayReasons = $attendances->where('status', StudentAttendance::STATUS_HALF_DAY)
            ->pluck('half_day_reason', 'date')
            ->filter()
            ->toArray();

        $lateReasons = $attendances->where('status', StudentAttendance::STATUS_LATE)
            ->pluck('late_reason', 'date')
            ->filter()
            ->toArray();

        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', StudentAttendance::STATUS_PRESENT)->count();
        $attendedDays = $attendances->whereIn('status', [
            StudentAttendance::STATUS_PRESENT,
            StudentAttendance::STATUS_LATE,
            StudentAttendance::STATUS_HALF_DAY,
            StudentAttendance::STATUS_ON_DUTY
        ])->count();

        return self::updateOrCreate(
            [
                'student_id' => $studentId,
                'month' => $monthString,
            ],
            [
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $attendances->where('status', StudentAttendance::STATUS_ABSENT)->count(),
                'late_days' => $attendances->where('status', StudentAttendance::STATUS_LATE)->count(),
                'half_days' => $attendances->where('status', StudentAttendance::STATUS_HALF_DAY)->count(),
                'leave_days' => $attendances->where('status', StudentAttendance::STATUS_LEAVE)->count(),
                'holiday_days' => $attendances->where('status', StudentAttendance::STATUS_HOLIDAY)->count(),
                'on_duty_days' => $attendances->where('status', StudentAttendance::STATUS_ON_DUTY)->count(),
                'attendance_percentage' => $totalDays > 0 ? round(($attendedDays / $totalDays) * 100, 2) : 0,
                'half_day_reasons' => $halfDayReasons,
                'late_reasons' => $lateReasons,
            ]
        );
    }
}