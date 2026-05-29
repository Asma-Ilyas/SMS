<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\TimeSlot;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\Room;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;
use App\Services\RealTimetableGenerator;

class TimetableController extends Controller
{
    protected $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

    public function index()
    {
        $classSections = ClassSection::with('class')->get();
        return view('admin.timetable.index', compact('classSections'));
    }

    public function show($classSectionId)
    {
        $classSection = ClassSection::with('class')->findOrFail($classSectionId);
        $timeSlots = TimeSlot::where('type', 'normal')->orderBy('order')->get();
        $subjects = Subject::all();
        $teachers = Staff::where('is_teacher', true)->get();
        $rooms = Room::where('is_active', true)->get();

        // Get existing entries keyed by day_timeSlot
        $entries = TimetableEntry::where('class_section_id', $classSectionId)
            ->get()
            ->keyBy(fn($e) => $e->day_of_week . '_' . $e->time_slot_id);

        return view('admin.timetable.edit', compact('classSection', 'timeSlots', 'subjects', 'teachers', 'rooms', 'entries'));
    }

    public function update(Request $request, $classSectionId)
    {
        $request->validate([
            'entries' => 'array',
            'entries.*.*.subject_id' => 'nullable|exists:subjects,id',
            'entries.*.*.teacher_id' => 'required_with:entries.*.*.subject_id|exists:staff,id',
            'entries.*.*.room_id'    => 'required_with:entries.*.*.subject_id|exists:rooms,id',
        ]);

        foreach ($request->entries as $day => $slots) {
            foreach ($slots as $slotId => $data) {
                if (empty($data['subject_id'])) {
                    // Delete if exists
                    TimetableEntry::where('class_section_id', $classSectionId)
                        ->where('day_of_week', $day)
                        ->where('time_slot_id', $slotId)
                        ->delete();
                    continue;
                }

                // Check conflicts before saving
                if (!TimetableEntry::isAvailable(
                    $data['teacher_id'],
                    $data['room_id'],
                    $classSectionId,
                    $day,
                    $slotId,
                    $existingId ?? null
                )) {
                    return back()->withErrors("Conflict: Teacher, room, or class section already occupied on $day slot $slotId.");
                }

                TimetableEntry::updateOrCreate(
                    [
                        'class_section_id' => $classSectionId,
                        'day_of_week' => $day,
                        'time_slot_id' => $slotId,
                    ],
                    [
                        'subject_id' => $data['subject_id'],
                        'teacher_id' => $data['teacher_id'],
                        'room_id'    => $data['room_id'],
                    ]
                );
            }
        }

        return redirect()->route('admin.timetable.show', $classSectionId)
            ->with('success', 'Timetable updated successfully.');
    }

    public function regenerate(RealTimetableGenerator $generator)
    {
        $result = $generator->generate();
        if ($result['success']) {
            return redirect()->route('admin.timetable.index')
                ->with('success', "Timetable regenerated! {$result['entries']} entries created.");
        }
        return back()->withErrors('Generation failed. Check logs.');
    }
}