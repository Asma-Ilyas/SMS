<?php
// app/Models/TeacherAvailability.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAvailability extends Model
{
    use HasFactory;

    protected $table = 'teacher_availabilities';

    protected $fillable = [
        'teacher_id',
        'day_of_week',
        'time_slot_id',
        'is_available',
        'reason',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    /**
     * Get teacher's unavailable slots (exceptions only)
     */
    public static function getExceptions($teacherId)
    {
        return self::where('teacher_id', $teacherId)
            ->where('is_available', false)
            ->with('timeSlot')
            ->get();
    }

    /**
     * Check if a teacher is available for a specific slot
     * Default: true (available) unless an exception exists with is_available = false
     */
    public static function isTeacherAvailable($teacherId, $dayOfWeek, $timeSlotId)
    {
        $exception = self::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where('time_slot_id', $timeSlotId)
            ->first();

        // If no exception record exists, teacher IS available (default)
        if (!$exception) {
            return true;
        }

        // If exception exists, return the is_available value
        return $exception->is_available;
    }

    /**
     * Set a teacher as unavailable (create exception)
     */
    public static function setUnavailable($teacherId, $dayOfWeek, $timeSlotId, $reason = null)
    {
        return self::updateOrCreate(
            [
                'teacher_id' => $teacherId,
                'day_of_week' => $dayOfWeek,
                'time_slot_id' => $timeSlotId,
            ],
            [
                'is_available' => false,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Remove an exception (teacher becomes available again)
     */
    public static function removeException($teacherId, $dayOfWeek, $timeSlotId)
    {
        return self::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where('time_slot_id', $timeSlotId)
            ->delete();
    }

    /**
     * Check if a teacher has any exceptions
     */
    public static function hasExceptions($teacherId)
    {
        return self::where('teacher_id', $teacherId)
            ->where('is_available', false)
            ->exists();
    }

    /**
     * Get count of exceptions for a teacher
     */
    public static function getExceptionCount($teacherId)
    {
        return self::where('teacher_id', $teacherId)
            ->where('is_available', false)
            ->count();
    }
}