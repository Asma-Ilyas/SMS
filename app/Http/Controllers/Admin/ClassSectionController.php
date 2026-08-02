<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\Classes;
use Illuminate\Http\Request;

class ClassSectionController extends Controller
{
    public function index()
    {
        $sections = ClassSection::with('class.grade', 'class.stream')
            ->withCount('students')
            ->orderBy('class_id')
            ->orderBy('section_name')
            ->paginate(20);
        return view('admin.class-sections.index', compact('sections'));
    }

    public function create()
    {
        $classes = Classes::with('grade', 'stream')->orderBy('id')->get();
        return view('admin.class-sections.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id'     => 'required|exists:classes,id',
            'section_name' => 'required|string|max:10',
            'capacity'     => 'nullable|integer|min:1',
        ]);

        $exists = ClassSection::where('class_id', $validated['class_id'])
            ->where('section_name', $validated['section_name'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['section_name' => 'This section already exists for this class.'])->withInput();
        }

        ClassSection::create($validated);
        return redirect()->route('admin.class-sections.index')
            ->with('success', 'Class section created successfully.');
    }

    public function edit(ClassSection $classSection)
    {
        $classes = Classes::with('grade', 'stream')->orderBy('id')->get();
        return view('admin.class-sections.edit', compact('classSection', 'classes'));
    }

    public function update(Request $request, ClassSection $classSection)
    {
        $validated = $request->validate([
            'class_id'     => 'required|exists:classes,id',
            'section_name' => 'required|string|max:10',
            'capacity'     => 'nullable|integer|min:1',
        ]);

        $exists = ClassSection::where('class_id', $validated['class_id'])
            ->where('section_name', $validated['section_name'])
            ->where('id', '!=', $classSection->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['section_name' => 'This section already exists for this class.'])->withInput();
        }

        $classSection->update($validated);
        return redirect()->route('admin.class-sections.index')
            ->with('success', 'Class section updated successfully.');
    }

    public function destroy(ClassSection $classSection)
    {
        if ($classSection->students()->count() > 0) {
            return back()->with('error', 'Cannot delete section with enrolled students.');
        }
        $classSection->delete();
        return redirect()->route('admin.class-sections.index')
            ->with('success', 'Class section deleted successfully.');
    }
}