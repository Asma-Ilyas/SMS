<?php

namespace App\Models;

class TimetableEntry extends Model
{
    protected $fillable = ['class_section_id', 'subject_id', 'teacher_id', 'room_id', 'time_slot_id', 'day_of_week'];

    public function classSection() { return $this->belongsTo(ClassSection::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function teacher() { return $this->belongsTo(Staff::class, 'teacher_id'); }
    public function room() { return $this->belongsTo(Room::class); }
    public function timeSlot() { return $this->belongsTo(TimeSlot::class); }

    public static function isAvailable($teacherId, $roomId, $classSectionId, $day, $timeSlotId, $ignoreId = null)
    {
        $query = self::where('day_of_week', $day)->where('time_slot_id', $timeSlotId);
        if ($ignoreId) $query->where('id', '!=', $ignoreId);
        return !$query->where(function($q) use ($teacherId, $roomId, $classSectionId) {
            $q->where('teacher_id', $teacherId)
              ->orWhere('room_id', $roomId)
              ->orWhere('class_section_id', $classSectionId);
        })->exists();
    }
}
