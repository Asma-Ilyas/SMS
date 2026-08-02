<?php
// app/Http/Controllers/Admin/TeacherAvailabilityController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\SchoolTiming;
use App\Models\TimeSlot;
use App\Models\TeacherAvailability;
use Illuminate\Http\Request;

class TeacherAvailabilityController extends Controller
{
    /**
     * Show teacher availability (exceptions only)
     */
    public function index(Request $request)
    {
        $teachers = Staff::where('is_teacher', true)->get();
        $selectedTeacher = null;
        $exceptions = collect();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $timeSlots = TimeSlot::where('type', 'period')->orderBy('sort_order')->get();

        if ($request->has('teacher_id') && $request->teacher_id) {
            $selectedTeacher = Staff::find($request->teacher_id);
            if ($selectedTeacher) {
                $exceptions = TeacherAvailability::where('teacher_id', $selectedTeacher->id)
                    ->where('is_available', false)
                    ->with('timeSlot')
                    ->get();
            }
        }

        return view('admin.teacher-availability.index', compact(
            'teachers', 'selectedTeacher', 'exceptions', 'days', 'timeSlots'
        ));
    }

    /**
     * Show form to set teacher unavailability (exception)
     */
    public function create(Request $request)
    {
        $teachers = Staff::where('is_teacher', true)->get();
        $selectedTeacher = null;
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $timeSlots = TimeSlot::where('type', 'period')->orderBy('sort_order')->get();

        if ($request->has('teacher_id') && $request->teacher_id) {
            $selectedTeacher = Staff::find($request->teacher_id);
        }

        return view('admin.teacher-availability.create', compact(
            'teachers', 'selectedTeacher', 'days', 'timeSlots'
        ));
    }

    /**
     * Store a teacher unavailability exception
     */
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:staff,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday',
            'time_slot_id' => 'required|exists:time_slots,id',
            'reason' => 'nullable|string|max:255',
        ]);

        TeacherAvailability::setUnavailable(
            $request->teacher_id,
            $request->day_of_week,
            $request->time_slot_id,
            $request->reason
        );

        return redirect()->route('admin.teacher-availability.index', ['teacher_id' => $request->teacher_id])
            ->with('success', 'Teacher marked as unavailable for this slot.');
    }

    /**
     * Remove an exception (teacher becomes available again)
     */
    public function destroy($id)
    {
        $exception = TeacherAvailability::findOrFail($id);
        $teacherId = $exception->teacher_id;

        TeacherAvailability::removeException(
            $exception->teacher_id,
            $exception->day_of_week,
            $exception->time_slot_id
        );

        return redirect()->route('admin.teacher-availability.index', ['teacher_id' => $teacherId])
            ->with('success', 'Teacher is now available for this slot.');
    }

    /**
     * Edit redirect (for backward compatibility with staff index)
     */
    public function edit($teacherId)
    {
        return redirect()->route('admin.teacher-availability.index', ['teacher_id' => $teacherId]);
    }
}