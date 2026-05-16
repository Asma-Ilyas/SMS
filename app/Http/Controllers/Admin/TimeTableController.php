<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeTable;
use App\Models\Subject;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimeTableController extends Controller
{
    public function index()
    {
        $timetables = TimeTable::with(['class.grade', 'class.stream', 'subject', 'teacher'])
            ->orderBy('class_id')
            ->orderBy('day_of_week')
            ->orderBy('period_number')
            ->get();

        return view('admin.timetable.index', compact('timetables'));
    }

    public function create()
    {
        // Build combined dropdown from classes (using grades and streams)
        $classes = DB::table('classes')
            ->join('grades', 'classes.grade_id', '=', 'grades.id')
            ->leftJoin('streams', 'classes.stream_id', '=', 'streams.id')
            ->select(
                'classes.id',
                DB::raw("CONCAT(grades.name, ' ', COALESCE(streams.name, ''), ' - ', classes.section) as display")
            )
            ->orderBy('grades.name')
            ->orderBy('streams.name')
            ->orderBy('classes.section')
            ->get();

        // Order by ID – safe fallback (no 'name' column needed)
        $subjects = Subject::orderBy('id')->get();
        $teachers = Staff::orderBy('id')->get();

        return view('admin.timetable.create', compact('classes', 'subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id'      => 'required|exists:classes,id',
            'day_of_week'   => 'required|integer|min:1|max:7',
            'period_number' => 'required|integer|min:1',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'subject_id'    => 'required|exists:subjects,id',
            'teacher_id'    => 'required|exists:staff,id',
        ]);

        try {
            TimeTable::create($validated);
            return redirect()->route('admin.timetable.index')
                ->with('success', 'Timetable entry added successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Database error: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(TimeTable $timetable)
    {
        $classes = DB::table('classes')
            ->join('grades', 'classes.grade_id', '=', 'grades.id')
            ->leftJoin('streams', 'classes.stream_id', '=', 'streams.id')
            ->select(
                'classes.id',
                DB::raw("CONCAT(grades.name, ' ', COALESCE(streams.name, ''), ' - ', classes.section) as display")
            )
            ->orderBy('grades.name')
            ->orderBy('streams.name')
            ->orderBy('classes.section')
            ->get();

        $subjects = Subject::orderBy('id')->get();
        $teachers = Staff::orderBy('id')->get();

        return view('admin.timetable.edit', compact('timetable', 'classes', 'subjects', 'teachers'));
    }

    public function update(Request $request, TimeTable $timetable)
    {
        $validated = $request->validate([
            'class_id'      => 'required|exists:classes,id',
            'day_of_week'   => 'required|integer|min:1|max:7',
            'period_number' => 'required|integer|min:1',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'subject_id'    => 'required|exists:subjects,id',
            'teacher_id'    => 'required|exists:staff,id',
        ]);

        try {
            $timetable->update($validated);
            return redirect()->route('admin.timetable.index')
                ->with('success', 'Timetable entry updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Database error: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(TimeTable $timetable)
    {
        $timetable->delete();
        return redirect()->route('admin.timetable.index')
            ->with('success', 'Timetable entry deleted successfully.');
    }
}