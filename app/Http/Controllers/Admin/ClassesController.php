<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\ClassSection;
use App\Models\AcademicSession;
use App\Models\Grade;
use App\Models\Stream;
use App\Models\ElectiveTrack;
use App\Models\Student;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    /**
     * Display a listing of class templates.
     */
    public function index()
    {
        $classes = Classes::with(['academicSession', 'grade', 'stream', 'electiveTrack'])
            ->withCount('classSections')
            ->orderBy('academic_session_id', 'desc')
            ->orderBy('grade_id')
            ->orderBy('stream_id')
            ->paginate(20);

        // For promotion modal (whole class)
        $allClasses = Classes::with(['grade', 'stream'])->get();
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $allClassSections = ClassSection::with('class.grade')->get();

        return view('admin.classes.index', compact('classes', 'allClasses', 'sessions', 'allClassSections'));
    }

    /**
     * Show form to create a new class template.
     */
    public function create()
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $grades = Grade::orderBy('numeric_value')->get();
        $streams = Stream::all();
        $electiveTracks = ElectiveTrack::with('stream')->get();

        return view('admin.classes.create', compact('sessions', 'grades', 'streams', 'electiveTracks'));
    }

    /**
     * Store a new class template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'grade_id'            => 'required|exists:grades,id',
            'stream_id'           => 'required|exists:streams,id',
            'elective_track_id'   => 'nullable|exists:elective_tracks,id',
            'section'             => 'required|string|max:10',
            'capacity'            => 'required|integer|min:1',
        ]);

        // Prevent duplicate
        if (Classes::where($validated)->exists()) {
            return back()->withErrors(['section' => 'This class combination already exists.'])->withInput();
        }

        Classes::create($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    /**
     * Show a single class template.
     */
    public function show(Classes $class)
    {
        $class->load('academicSession', 'grade', 'stream', 'electiveTrack', 'classSections');
        return view('admin.classes.show', compact('class'));
    }

    /**
     * Show form to edit a class template.
     */
    public function edit(Classes $class)
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $grades = Grade::orderBy('numeric_value')->get();
        $streams = Stream::all();
        $electiveTracks = ElectiveTrack::with('stream')->get();

        return view('admin.classes.edit', compact('class', 'sessions', 'grades', 'streams', 'electiveTracks'));
    }

    /**
     * Update a class template.
     */
    public function update(Request $request, Classes $class)
    {
        $validated = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'grade_id'            => 'required|exists:grades,id',
            'stream_id'           => 'required|exists:streams,id',
            'elective_track_id'   => 'nullable|exists:elective_tracks,id',
            'section'             => 'required|string|max:10',
            'capacity'            => 'required|integer|min:1',
        ]);

        // Prevent duplicate (ignore current)
        $duplicate = Classes::where($validated)->where('id', '!=', $class->id)->exists();
        if ($duplicate) {
            return back()->withErrors(['section' => 'Another class with this combination already exists.'])->withInput();
        }

        $class->update($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    /**
     * Delete a class template (only if no class sections are linked).
     */
    public function destroy(Classes $class)
    {
        if ($class->classSections()->exists()) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Cannot delete class with active sections.');
        }
        $class->delete();
        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted.');
    }

    /**
     * Promote all students from all sections of this class template
     * to the matching sections of another class template.
     */
    public function promoteWholeClass(Request $request, Classes $class)
    {
        $request->validate([
            'target_class_id' => 'required|exists:classes,id',
            'target_academic_session_id' => 'nullable|exists:academic_sessions,id',
        ]);

        $targetClass = Classes::findOrFail($request->target_class_id);
        $sourceSections = $class->classSections;

        $count = 0;
        foreach ($sourceSections as $sourceSection) {
            $targetSection = ClassSection::where('class_id', $targetClass->id)
                ->where('section_name', $sourceSection->section_name)
                ->first();

            if (!$targetSection) continue;

            $students = Student::where('class_section_id', $sourceSection->id)->get();
            foreach ($students as $student) {
                $student->class_section_id = $targetSection->id;
                if ($request->filled('target_academic_session_id')) {
                    // $student->academic_session_id = $request->target_academic_session_id;
                }
                $student->save();
                $count++;
            }
        }

        return redirect()->route('admin.classes.index')
            ->with('success', "{$count} students promoted from {$class->full_name} to {$targetClass->full_name}.");
    }

    /**
     * Promote all students from a specific class section to another class section.
     * This is usually called from the class sections list.
     */
    public function promoteSectionAll(Request $request, ClassSection $classSection)
    {
        $request->validate([
            'target_class_section_id' => 'required|exists:class_sections,id',
        ]);

        $targetSection = ClassSection::findOrFail($request->target_class_section_id);
        $students = Student::where('class_section_id', $classSection->id)->get();
        $count = 0;
        foreach ($students as $student) {
            $student->class_section_id = $targetSection->id;
            $student->save();
            $count++;
        }

        return redirect()->route('admin.class-sections.index')
            ->with('success', "{$count} students promoted from {$classSection->full_name} to {$targetSection->full_name}.");
    }
}