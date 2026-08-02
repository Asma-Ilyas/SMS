<?php

namespace App\Services;

use App\Models\TimetableEntry;
use App\Models\ClassSection;
use App\Models\Staff;
use App\Models\Room;
use App\Models\SchoolTiming;
use App\Models\TeacherAvailability;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimetableGeneratorService
{
    protected $timing;
    protected $periodSlots;
    protected $days = [1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday'];
    protected $maxAttempts = 5;

    public function __construct(SchoolTiming $timing)
    {
        $this->timing = $timing;
        $this->periodSlots = $timing->timeSlots()
            ->where('type', 'period')
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Main generate method
     */
    public function generate(): array
    {
        $this->logInfo('Starting timetable generation...');
        
        $sections = ClassSection::with(['subjectAssignments.subject'])->get();
        
        if ($sections->isEmpty()) {
            return $this->error('No sections found. Please create sections first.');
        }
        
        if ($this->periodSlots->isEmpty()) {
            return $this->error('No active period slots defined. Please create time slots.');
        }

        // Clear old entries for this timing
        $deleted = TimetableEntry::whereHas('timeSlot', fn($q) => $q->where('school_timing_id', $this->timing->id))->delete();
        $this->logInfo("Cleared {$deleted} old timetable entries");

        // Get available rooms with capacity
        $rooms = Room::where('is_available', true)
            ->orderBy('capacity', 'desc')
            ->get();
        
        if ($rooms->isEmpty()) {
            return $this->error('No available rooms found. Please add rooms.');
        }

        // Prepare section needs with subject requirements
        $sectionNeeds = $this->prepareSectionNeeds($sections);
        if (empty($sectionNeeds)) {
            return $this->error('No subject assignments found for sections.');
        }

        // Track usage
        $teacherBusy = [];
        $roomBusy = [];
        $assignmentCount = 0;
        $failedAssignments = [];

        // For each day and slot, assign subjects
        foreach ($this->days as $dayNumber => $dayName) {
            foreach ($this->periodSlots as $slot) {
                $availableRooms = $rooms->shuffle();
                
                // Get sections that still need periods for this day/slot
                $needySections = $this->getNeedySections($sectionNeeds);
                
                foreach ($needySections as $sectionId => $need) {
                    if ($availableRooms->isEmpty()) break;
                    
                    // Find a subject that still needs periods
                    $subjectNeed = $this->findPendingSubject($need);
                    if (!$subjectNeed) continue;
                    
                    // Find available teacher for this subject
                    $teacher = $this->findAvailableTeacher(
                        $subjectNeed->subject, 
                        $dayNumber, 
                        $slot->id, 
                        $teacherBusy,
                        $dayName
                    );
                    
                    if (!$teacher) {
                        $failedAssignments[] = "No teacher available for {$subjectNeed->subject->name} - Section {$need->section->section_name} on {$dayName}";
                        continue;
                    }
                    
                    // Find available room
                    $room = $this->findAvailableRoom($availableRooms, $roomBusy, $dayNumber, $slot->id);
                    if (!$room) {
                        $failedAssignments[] = "No suitable room for Section {$need->section->section_name} on {$dayName}";
                        continue;
                    }
                    
                    // Create timetable entry
                    TimetableEntry::create([
                        'class_section_id' => $need->section->id,
                        'subject_id' => $subjectNeed->subject->id,
                        'teacher_id' => $teacher->id,
                        'room_id' => $room->id,
                        'time_slot_id' => $slot->id,
                        'day_of_week' => $dayNumber,
                        'is_manual' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Mark resources as busy
                    $this->markBusy($teacherBusy, $dayNumber, $slot->id, $teacher->id);
                    $this->markBusy($roomBusy, $dayNumber, $slot->id, $room->id);
                    
                    // Remove room from available
                    $availableRooms = $availableRooms->filter(fn($r) => $r->id !== $room->id);
                    
                    $subjectNeed->placed++;
                    $need->total--;
                    $assignmentCount++;
                }
            }
        }

        // Check for unfulfilled needs
        $unfulfilled = $this->countUnfulfilled($sectionNeeds);
        
        // Second pass - try to fill remaining slots with any available teacher/room
        if ($unfulfilled > 0) {
            $this->logInfo("Second pass: Trying to fulfill {$unfulfilled} remaining periods...");
            
            foreach ($this->days as $dayNumber => $dayName) {
                foreach ($this->periodSlots as $slot) {
                    $needySections = $this->getNeedySections($sectionNeeds);
                    
                    foreach ($needySections as $sectionId => $need) {
                        if ($need->total <= 0) continue;
                        
                        $subjectNeed = $this->findPendingSubject($need);
                        if (!$subjectNeed) continue;
                        
                        // Find any teacher (less strict)
                        $teacher = $this->findAnyAvailableTeacher(
                            $subjectNeed->subject, 
                            $dayNumber, 
                            $slot->id, 
                            $teacherBusy,
                            $dayName
                        );
                        
                        if (!$teacher) continue;
                        
                        // Find any room
                        $availableRooms = $rooms->whereNotIn('id', $this->getBusyRooms($roomBusy, $dayNumber, $slot->id));
                        $room = $availableRooms->first();
                        if (!$room) continue;
                        
                        try {
                            TimetableEntry::create([
                                'class_section_id' => $need->section->id,
                                'subject_id' => $subjectNeed->subject->id,
                                'teacher_id' => $teacher->id,
                                'room_id' => $room->id,
                                'time_slot_id' => $slot->id,
                                'day_of_week' => $dayNumber,
                                'is_manual' => false,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            
                            $this->markBusy($teacherBusy, $dayNumber, $slot->id, $teacher->id);
                            $this->markBusy($roomBusy, $dayNumber, $slot->id, $room->id);
                            
                            $subjectNeed->placed++;
                            $need->total--;
                            $assignmentCount++;
                            
                            $this->logInfo("SECOND PASS: Assigned Section {$need->section->section_name}, Subject {$subjectNeed->subject->name}");
                            
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            }
            
            // Recalculate unfulfilled
            $unfulfilled = $this->countUnfulfilled($sectionNeeds);
        }

        $totalRequired = $this->getTotalRequired($sectionNeeds);
        $totalAssigned = TimetableEntry::count();
        
        $result = [
            'success' => $unfulfilled == 0,
            'summary' => [
                'entries_created' => $totalAssigned,
                'unfulfilled' => $unfulfilled,
                'total_required' => $totalRequired,
                'total_sections' => $sections->count(),
                'total_rooms' => $rooms->count(),
                'total_slots' => $this->periodSlots->count(),
                'max_possible' => $rooms->count() * $this->periodSlots->count() * 5,
            ],
            'failures' => array_merge(
                $unfulfilled > 0 ? ["{$unfulfilled} subject periods could not be scheduled."] : [],
                array_slice($failedAssignments, 0, 10)
            ),
        ];
        
        $this->logInfo("Generation complete: {$totalAssigned} entries created, {$unfulfilled} unfulfilled");
        
        return $result;
    }

    /**
     * Prepare section needs from subject assignments
     */
    protected function prepareSectionNeeds($sections)
    {
        $sectionNeeds = [];
        
        foreach ($sections as $section) {
            $needs = [];
            foreach ($section->subjectAssignments as $sa) {
                $subject = $sa->subject;
                if ($subject) {
                    $needs[] = (object)[
                        'subject' => $subject,
                        'frequency' => $sa->weekly_frequency,
                        'placed' => 0,
                    ];
                }
            }
            
            if (!empty($needs)) {
                $sectionNeeds[$section->id] = (object)[
                    'section' => $section,
                    'needs' => $needs,
                    'total' => array_sum(array_column($needs, 'frequency')),
                ];
            }
        }
        
        return collect($sectionNeeds);
    }

    /**
     * Get sections that still need periods
     */
    protected function getNeedySections($sectionNeeds)
    {
        return $sectionNeeds->filter(fn($need) => $need->total > 0);
    }

    /**
     * Find a pending subject that still needs periods
     */
    protected function findPendingSubject($need)
    {
        foreach ($need->needs as $n) {
            if ($n->placed < $n->frequency) {
                return $n;
            }
        }
        return null;
    }

    /**
     * Find available teacher for a subject (checks exceptions)
     */
    protected function findAvailableTeacher($subject, $dayNumber, $slotId, $teacherBusy, $dayName)
    {
        $teachers = Staff::where('is_teacher', true)
            ->whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
            ->orderByRaw('CASE WHEN (SELECT preference_level FROM teacher_subjects WHERE teacher_id = staff.id AND subject_id = ?) IS NOT NULL THEN (SELECT preference_level FROM teacher_subjects WHERE teacher_id = staff.id AND subject_id = ?) ELSE 999 END', [$subject->id, $subject->id])
            ->get();

        foreach ($teachers as $teacher) {
            // Check if teacher is already busy in this slot
            if ($this->isResourceBusy($teacherBusy, $dayNumber, $slotId, $teacher->id)) {
                continue;
            }
            
            // Check teacher availability (exceptions only)
            // Default: teacher IS available unless an exception exists
            $exception = TeacherAvailability::where('teacher_id', $teacher->id)
                ->where('day_of_week', $dayName)
                ->where('time_slot_id', $slotId)
                ->first();
            
            // If exception exists and is_available = false, teacher is NOT available
            if ($exception && !$exception->is_available) {
                continue;
            }
            
            // Check max periods per day
            if ($this->hasReachedMaxPeriods($teacher->id, $dayNumber, $teacherBusy)) {
                continue;
            }
            
            return $teacher;
        }
        
        return null;
    }

    /**
     * Find any available teacher (second pass - less strict)
     */
    protected function findAnyAvailableTeacher($subject, $dayNumber, $slotId, $teacherBusy, $dayName)
    {
        $teachers = Staff::where('is_teacher', true)
            ->whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
            ->get();

        foreach ($teachers as $teacher) {
            if ($this->isResourceBusy($teacherBusy, $dayNumber, $slotId, $teacher->id)) {
                continue;
            }
            
            // Check teacher availability (exceptions only)
            $exception = TeacherAvailability::where('teacher_id', $teacher->id)
                ->where('day_of_week', $dayName)
                ->where('time_slot_id', $slotId)
                ->first();
            
            if ($exception && !$exception->is_available) {
                continue;
            }
            
            return $teacher;
        }
        
        return null;
    }

    /**
     * Find available room
     */
    protected function findAvailableRoom($rooms, $roomBusy, $dayNumber, $slotId)
    {
        foreach ($rooms as $room) {
            if (!$this->isResourceBusy($roomBusy, $dayNumber, $slotId, $room->id)) {
                return $room;
            }
        }
        return null;
    }

    /**
     * Check if a resource is busy
     */
    protected function isResourceBusy($busyArray, $day, $slotId, $resourceId)
    {
        return isset($busyArray[$day][$slotId]) && in_array($resourceId, $busyArray[$day][$slotId]);
    }

    /**
     * Get busy rooms for a specific day and slot
     */
    protected function getBusyRooms($roomBusy, $dayNumber, $slotId)
    {
        return isset($roomBusy[$dayNumber][$slotId]) ? $roomBusy[$dayNumber][$slotId] : [];
    }

    /**
     * Mark a resource as busy
     */
    protected function markBusy(&$busyArray, $day, $slotId, $resourceId)
    {
        if (!isset($busyArray[$day][$slotId])) {
            $busyArray[$day][$slotId] = [];
        }
        if (!in_array($resourceId, $busyArray[$day][$slotId])) {
            $busyArray[$day][$slotId][] = $resourceId;
        }
    }

    /**
     * Check if teacher has reached max periods per day
     */
    protected function hasReachedMaxPeriods($teacherId, $dayNumber, $teacherBusy)
    {
        $teacher = Staff::find($teacherId);
        if (!$teacher || !$teacher->max_periods_per_day) {
            return false;
        }
        
        $periodsToday = isset($teacherBusy[$dayNumber]) ? count($teacherBusy[$dayNumber]) : 0;
        return $periodsToday >= $teacher->max_periods_per_day;
    }

    /**
     * Count unfulfilled needs
     */
    protected function countUnfulfilled($sectionNeeds)
    {
        $count = 0;
        foreach ($sectionNeeds as $need) {
            $count += $need->total;
        }
        return $count;
    }

    /**
     * Get total required periods
     */
    protected function getTotalRequired($sectionNeeds)
    {
        $total = 0;
        foreach ($sectionNeeds as $need) {
            foreach ($need->needs as $n) {
                $total += $n->frequency;
            }
        }
        return $total;
    }

    /**
     * Log info message
     */
    protected function logInfo($message)
    {
        Log::info("TimetableGenerator: " . $message);
    }

    /**
     * Return error response
     */
    protected function error($msg)
    {
        $this->logInfo("ERROR: " . $msg);
        return [
            'success' => false,
            'summary' => [
                'entries_created' => 0,
                'unfulfilled' => 0,
            ],
            'failures' => [$msg],
        ];
    }
}