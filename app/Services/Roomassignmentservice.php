<?php

namespace App\Services;

use App\Models\Room;
use App\Models\SchoolTiming;
use App\Models\SectionRoomAssignment;
use App\Models\ClassSection;
use Illuminate\Support\Facades\Log;

class RoomAssignmentService
{
    public function getAvailabilityStats($timingId)
    {
        $totalRooms = Room::count();
        $assignedCount = SectionRoomAssignment::where('school_timing_id', $timingId)->count();
        $overCapacity = SectionRoomAssignment::where('school_timing_id', $timingId)
                        ->where('fit_status', 'over_capacity')
                        ->count();
        $assignedRoomIds = SectionRoomAssignment::where('school_timing_id', $timingId)->pluck('room_id')->unique();
        $freeRooms = Room::where('is_available', true)->whereNotIn('id', $assignedRoomIds)->count();

        return [
            'totalRooms'    => $totalRooms,
            'freeRooms'     => $freeRooms,
            'assignedCount' => $assignedCount,
            'overCapacity'  => $overCapacity,
        ];
    }

   public function assignAll(SchoolTiming $session)
{
    // Delete previous auto assignments
    SectionRoomAssignment::where('school_timing_id', $session->id)
        ->where('assignment_type', 'auto')
        ->delete();

    // Get all sections (ignore strength – assign anyway)
    $sections = ClassSection::all();
    if ($sections->isEmpty()) {
        return $this->errorResult('No sections found.');
    }

    // Get available rooms (order by capacity)
    $availableRooms = Room::where('is_available', true)->orderBy('capacity')->get();
    if ($availableRooms->isEmpty()) {
        return $this->errorResult('No available rooms.');
    }

    // Get sections already manually assigned
    $manuallyAssignedIds = SectionRoomAssignment::where('school_timing_id', $session->id)
        ->where('assignment_type', 'manual')
        ->pluck('section_id')
        ->toArray();

    $assignedRoomIds = []; // track rooms used in this auto run
    $results = ['assigned' => 0, 'over_capacity' => [], 'no_room' => []];

    foreach ($sections as $section) {
        // Skip manual assignments
        if (in_array($section->id, $manuallyAssignedIds)) {
            continue;
        }

        $strength = $section->student_strength ?? $section->student_count ?? 30; // default 30

        // First try to find a room with enough capacity (spare >= 0)
        $bestFitRoom = null;
        $bestSpare = PHP_INT_MAX;
        $bestFit = null;

        foreach ($availableRooms as $room) {
            if (in_array($room->id, $assignedRoomIds)) continue;
            $spare = $room->capacity - $strength;
            if ($spare >= 0 && $spare < $bestSpare) {
                $bestSpare = $spare;
                $bestFitRoom = $room;
                $bestFit = $spare <= 3 ? 'tight' : ($spare <= 10 ? 'perfect' : 'comfortable');
            }
        }

        // If no room with enough capacity, take any available room (even over capacity)
        if (!$bestFitRoom) {
            foreach ($availableRooms as $room) {
                if (in_array($room->id, $assignedRoomIds)) continue;
                $spare = $room->capacity - $strength;
                if ($spare < 0) {
                    $bestFitRoom = $room;
                    $bestFit = 'over_capacity';
                    break;
                }
            }
        }

        if ($bestFitRoom) {
            SectionRoomAssignment::create([
                'section_id' => $section->id,
                'room_id' => $bestFitRoom->id,
                'school_timing_id' => $session->id,
                'section_strength' => $strength,
                'room_capacity' => $bestFitRoom->capacity,
                'fit_status' => $bestFit,
                'spare_seats' => $bestFitRoom->capacity - $strength,
                'assignment_type' => 'auto',
                'suggested_room_id' => null,
            ]);
            $assignedRoomIds[] = $bestFitRoom->id;
            $results['assigned']++;

            if ($bestFit === 'over_capacity') {
                $results['over_capacity'][] = [
                    'section' => $section->section_name,
                    'strength' => $strength,
                    'capacity' => $bestFitRoom->capacity,
                    'shortage' => $strength - $bestFitRoom->capacity,
                ];
            }
        } else {
            // No room left at all (should not happen if rooms >= sections)
            $results['no_room'][] = ['section' => $section->section_name, 'strength' => $strength];
        }
    }

    $summary = [
        'total_sections' => $sections->count(),
        'assigned' => $results['assigned'],
        'over_capacity' => count($results['over_capacity']),
        'no_room' => count($results['no_room']),
        'skipped_manual' => count($manuallyAssignedIds),
        'rooms_free' => $availableRooms->count() - count($assignedRoomIds),
    ];

    return [
        'summary' => $summary,
        'over_capacity' => $results['over_capacity'],
        'no_room' => $results['no_room'],
    ];
}

    public function assignManually($sectionId, $roomId, $timingId)
    {
        $section = ClassSection::findOrFail($sectionId);
        $room = Room::findOrFail($roomId);
        $strength = $section->student_strength ?? $section->student_count ?? 0;
        $spare = $room->capacity - $strength;

        $fit = $spare < 0 ? 'over_capacity' : ($spare <= 3 ? 'tight' : ($spare <= 10 ? 'perfect' : 'comfortable'));

        $suggestedId = null;
        if ($fit === 'over_capacity') {
            $betterRoom = Room::available()
                ->where('capacity', '>=', $strength)
                ->orderBy('capacity')
                ->first();
            if ($betterRoom) $suggestedId = $betterRoom->id;
        }

        SectionRoomAssignment::updateOrCreate(
            ['section_id' => $sectionId, 'school_timing_id' => $timingId],
            [
                'room_id' => $roomId,
                'section_strength' => $strength,
                'room_capacity' => $room->capacity,
                'fit_status' => $fit,
                'spare_seats' => $spare,
                'assignment_type' => 'manual',
                'suggested_room_id' => $suggestedId,
            ]
        );

        return [
            'section' => $section->section_name,
            'room' => $room->name,
            'fit_status' => $fit,
            'spare_seats' => $spare,
            'suggested_id' => $suggestedId,
        ];
    }

    public function adjustSection($sectionId, $timingId)
    {
        $section = ClassSection::findOrFail($sectionId);
        $strength = $section->student_strength ?? $section->student_count ?? 0;

        $currentAssignment = SectionRoomAssignment::where('section_id', $sectionId)
            ->where('school_timing_id', $timingId)
            ->first();
        $currentRoomId = $currentAssignment ? $currentAssignment->room_id : null;

        $availableRooms = Room::available()
            ->orderBy('capacity')
            ->get()
            ->filter(fn($room) => $room->id != $currentRoomId);

        $bestRoom = null;
        $bestScore = PHP_INT_MAX;
        $bestFit = null;

        foreach ($availableRooms as $room) {
            $spare = $room->capacity - $strength;
            $score = $spare < 0 ? 10000 + abs($spare) : $spare;
            if ($score < $bestScore) {
                $bestScore = $score;
                $bestRoom = $room;
                $bestFit = $spare < 0 ? 'over_capacity' : ($spare <= 3 ? 'tight' : ($spare <= 10 ? 'perfect' : 'comfortable'));
            }
        }

        if (!$bestRoom) {
            return ['status' => 'no_room', 'section' => $section->section_name];
        }

        SectionRoomAssignment::updateOrCreate(
            ['section_id' => $sectionId, 'school_timing_id' => $timingId],
            [
                'room_id' => $bestRoom->id,
                'section_strength' => $strength,
                'room_capacity' => $bestRoom->capacity,
                'fit_status' => $bestFit,
                'spare_seats' => $bestRoom->capacity - $strength,
                'assignment_type' => 'manual',
                'suggested_room_id' => null,
            ]
        );

        return [
            'status' => 'assigned',
            'section' => $section->section_name,
            'room' => $bestRoom->name,
            'fit_status' => $bestFit,
        ];
    }

    private function errorResult($message)
    {
        return [
            'summary' => [
                'total_sections' => 0,
                'assigned' => 0,
                'over_capacity' => 0,
                'no_room' => 0,
                'skipped_manual' => 0,
                'rooms_free' => 0,
                'error' => $message,
            ],
            'over_capacity' => [],
            'no_room' => [],
        ];
    }
}