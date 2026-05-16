<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\Section;
use App\Models\ClassSubjectTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassSubjectController extends Controller
{
    public function index()
    {
        $assignments = ClassSubjectTeacher::with(['class', 'subject', 'teacher'])
            ->orderBy('class_id')
            ->get();

        // Build combined display for each class (grade + stream + section)
        foreach ($assignments as $assignment) {
            $class = $assignment->class;
            $gradeName = optional($class->grade)->name ?? '?';
            $streamName = optional($class->stream)->name ?? '';
            $section = $class->section ?? '?';
            $assignment->class_display = trim($gradeName . ' ' . $streamName . ' - ' . $section);
        }

        return view('admin.class-subject.index', compact('assignments'));
    }

    public function create()
    {
        // Get all classes with their grade & stream
        $classes = Classes::with(['grade', 'stream'])->get();
        $formattedClasses = $classes->map(function ($class) {
            $gradeName = optional($class->grade)->name ?? '?';
            $streamName = optional($class->stream)->name ?? '';
            $section = $class->section ?? '?';
            $display = trim($gradeName . ' ' . $streamName . ' - ' . $section);
            return (object) ['id' => $class->id, 'display' => $display];
        });

        $subjects = Subject::orderBy('id')->get();
        $teachers = Staff::orderBy('id')->get();

        return view('admin.class-subject.create', compact('formattedClasses', 'subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:staff,id',
        ]);

        $exists = ClassSubjectTeacher::where('class_id', $validated['class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'This subject is already assigned to this class.'])->withInput();
        }

        ClassSubjectTeacher::create($validated);

        return redirect()->route('admin.class-subject.index')
            ->with('success', 'Subject assigned successfully.');
    }

    public function edit(ClassSubjectTeacher $classSubject)
    {
        $classes = Classes::with(['grade', 'stream'])->get();
        $formattedClasses = $classes->map(function ($class) {
            $gradeName = optional($class->grade)->name ?? '?';
            $streamName = optional($class->stream)->name ?? '';
            $section = $class->section ?? '?';
            $display = trim($gradeName . ' ' . $streamName . ' - ' . $section);
            return (object) ['id' => $class->id, 'display' => $display];
        });

        $subjects = Subject::orderBy('id')->get();
        $teachers = Staff::orderBy('id')->get();

        return view('admin.class-subject.edit', compact('classSubject', 'formattedClasses', 'subjects', 'teachers'));
    }

    public function update(Request $request, ClassSubjectTeacher $classSubject)
    {
        $validated = $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:staff,id',
        ]);

        $exists = ClassSubjectTeacher::where('class_id', $validated['class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('id', '!=', $classSubject->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'This subject is already assigned to this class.'])->withInput();
        }

        $classSubject->update($validated);

        return redirect()->route('admin.class-subject.index')
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(ClassSubjectTeacher $classSubject)
    {
        $classSubject->delete();
        return redirect()->route('admin.class-subject.index')
            ->with('success', 'Assignment removed successfully.');
    }
}