<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectAssignmentController extends Controller
{
    /**
     * Display a listing of all assignments.
     */
    public function index()
    {
        // Use Eloquent relationships to avoid column errors
        $assignments = DB::table('class_subject_teacher')
            ->select(
                'class_subject_teacher.*',
                'class_subject_teacher.id as assignment_id',
                'classes.id as class_id',
                'subjects.id as subject_id',
                'staff.id as teacher_id',
                'subjects.name as subject_name',
                'staff.first_name',
                'staff.last_name',
                'academic_sessions.name as session_name'
            )
            ->join('classes', 'class_subject_teacher.class_id', '=', 'classes.id')
            ->join('subjects', 'class_subject_teacher.subject_id', '=', 'subjects.id')
            ->join('staff', 'class_subject_teacher.teacher_id', '=', 'staff.id')
            ->leftJoin('academic_sessions', 'class_subject_teacher.academic_session_id', '=', 'academic_sessions.id')
            ->get();

        // Load class models to get the full_name accessor
        $classes = Classes::with(['grade', 'stream'])->get()->keyBy('id');
        foreach ($assignments as $assignment) {
            $class = $classes->get($assignment->class_id);
            $assignment->class_name = $class ? $class->full_name : 'Class #' . $assignment->class_id;
        }

        return view('admin.subject-assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create()
    {
        $classes = Classes::all();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Staff::where('is_active', true)->get();
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();

        return view('admin.subject-assignments.create', compact('classes', 'subjects', 'teachers', 'sessions'));
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:staff,id',
            'academic_session_id' => 'nullable|exists:academic_sessions,id',
            'max_weekly_periods' => 'nullable|integer|min:0',
            'term' => 'required|in:first,second,third,full_year',
            'notes' => 'nullable|string',
        ]);

        // Prevent duplicate assignments
        $exists = DB::table('class_subject_teacher')
            ->where('class_id', $validated['class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('teacher_id', $validated['teacher_id'])
            ->where('academic_session_id', $validated['academic_session_id'])
            ->where('term', $validated['term'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'This assignment already exists.'])->withInput();
        }

        DB::table('class_subject_teacher')->insert([
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'academic_session_id' => $validated['academic_session_id'],
            'max_weekly_periods' => $validated['max_weekly_periods'] ?? 0,
            'term' => $validated['term'],
            'notes' => $validated['notes'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.subject-assignments.index')
            ->with('success', 'Subject assigned to teacher and class successfully.');
    }

    /**
     * Remove the specified assignment from storage.
     */
    public function destroy($id)
    {
        $deleted = DB::table('class_subject_teacher')->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('admin.subject-assignments.index')
                ->with('success', 'Assignment removed successfully.');
        }

        return back()->withErrors(['error' => 'Assignment not found.']);
    }
}