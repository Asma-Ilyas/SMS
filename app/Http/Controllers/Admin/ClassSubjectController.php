<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\SubjectAssignment;
use Illuminate\Http\Request;

class ClassSubjectController extends Controller
{
    public function index()
    {
        $assignments = SubjectAssignment::with(['classSection.class.grade', 'classSection.class.stream', 'subject'])
            ->orderBy('class_section_id')
            ->get();

        // Build display name for each class section
        foreach ($assignments as $assignment) {
            $classSection = $assignment->classSection;
            $assignment->class_display = $classSection->full_name;
        }

        return view('admin.class-subject.index', compact('assignments'));
    }

    public function create()
    {
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('admin.class-subject.create', compact('classSections', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id'       => 'required|exists:subjects,id',
            'weekly_frequency' => 'required|integer|min:1|max:10',
            'is_elective'      => 'sometimes|boolean',
        ]);

        $exists = SubjectAssignment::where('class_section_id', $validated['class_section_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'This subject is already assigned to this class section.'])->withInput();
        }

        SubjectAssignment::create([
            'class_section_id' => $validated['class_section_id'],
            'subject_id'       => $validated['subject_id'],
            'weekly_frequency' => $validated['weekly_frequency'],
            'is_elective'      => $request->boolean('is_elective'),
        ]);

        return redirect()->route('admin.class-subject.index')
            ->with('success', 'Subject assigned successfully.');
    }

    public function edit(SubjectAssignment $classSubject)
    {
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('admin.class-subject.edit', compact('classSubject', 'classSections', 'subjects'));
    }

    public function update(Request $request, SubjectAssignment $classSubject)
    {
        $validated = $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id'       => 'required|exists:subjects,id',
            'weekly_frequency' => 'required|integer|min:1|max:10',
            'is_elective'      => 'sometimes|boolean',
        ]);

        $exists = SubjectAssignment::where('class_section_id', $validated['class_section_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('id', '!=', $classSubject->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'This subject is already assigned to this class section.'])->withInput();
        }

        $classSubject->update([
            'class_section_id' => $validated['class_section_id'],
            'subject_id'       => $validated['subject_id'],
            'weekly_frequency' => $validated['weekly_frequency'],
            'is_elective'      => $request->boolean('is_elective'),
        ]);

        return redirect()->route('admin.class-subject.index')
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(SubjectAssignment $classSubject)
    {
        $classSubject->delete();
        return redirect()->route('admin.class-subject.index')
            ->with('success', 'Assignment removed successfully.');
    }
}