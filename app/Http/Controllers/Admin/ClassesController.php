<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreClassRequest;
use App\Http\Requests\UpdateClassRequest;
use App\Models\Classes;
use App\Models\AcademicSession;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Stream;
use App\Models\ElectiveTrack;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $classes = Classes::with(['session', 'grade', 'stream', 'electiveTrack'])
        ->withCount('students')  // if you have a students relationship
        ->latest()
        ->paginate(15);
    
    $allClasses = Classes::with(['grade', 'stream', 'electiveTrack'])->get();
    $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
    
    return view('admin.classes.index', compact('classes', 'allClasses', 'sessions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    $electiveTracks = ElectiveTrack::with('stream')->get()->map(function($track) {
    return (object) [
        'id' => $track->id,
        'name' => $track->name,
        'stream_id' => $track->stream_id
    ];
});

         $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $grades = Grade::orderBy('numeric_value')->get();
        $streams = Stream::all();
        $electiveTracks = ElectiveTrack::with('stream')->get();

        return view('admin.classes.create', compact('sessions', 'grades', 'streams', 'electiveTracks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassRequest $request)
    {
        Classes::create($request->validated());

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classes $class)
{
    $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
    $grades = Grade::orderBy('numeric_value')->get();
    $streams = Stream::all();
    
    // Get elective tracks with stream_id attached for JavaScript filtering
    $electiveTracks = ElectiveTrack::with('stream')->get()->map(function($track) {
        return (object) [
            'id' => $track->id,
            'name' => $track->name,
            'stream_id' => $track->stream_id
        ];
    });

    return view('classes.edit', compact('class', 'sessions', 'grades', 'streams', 'electiveTracks'));
}
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassRequest $request, Classes $class)
    {
        $class->update($request->validated());

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated successfully.');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classes $class)
    {
         $class->delete();
        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted successfully.');
    
    }

    public function promoteAll(Request $request, Classes $class)
{
    $request->validate([
        'target_class_id' => 'required|exists:classes,id',
        'target_academic_session_id' => 'nullable|exists:academic_sessions,id',
    ]);

    $students = Student::where('class_id', $class->id)->get();
    $count = 0;
    foreach ($students as $student) {
        $student->class_id = $request->target_class_id;
        if ($request->target_academic_session_id) {
            $student->academic_session_id = $request->target_academic_session_id;
        }
        $student->save();
        $count++;
    }

    return redirect()->route('admin.classes.index')
        ->with('success', "{$count} students promoted from {$class->full_name} to new class.");
}
}
