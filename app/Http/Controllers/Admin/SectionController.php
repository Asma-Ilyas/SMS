<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Display a listing of sections with class and stream.
     */
    public function index()
    {
        $sections = Section::with(['class', 'stream'])
            ->orderBy('class_id')
            ->orderBy('stream_id')
            ->orderBy('name')
            ->get();

        return view('admin.sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new section.
     */
    public function create()
    {
        $classes = Classes::orderBy('name')->get();
        $streams = Stream::orderBy('name')->get();
        return view('admin.sections.create', compact('classes', 'streams'));
    }

    /**
     * Store a newly created section.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id'  => 'required|exists:classes,id',
            'stream_id' => 'nullable|exists:streams,id',
            'name'      => 'required|string|max:20',
            'capacity'  => 'nullable|integer',
        ]);

        Section::create($request->all());

        return redirect()->route('admin.sections.index')
                         ->with('success', 'Section created successfully.');
    }

    /**
     * Show the form for editing a section.
     */
    public function edit(Section $section)
    {
        $classes = Classes::orderBy('name')->get();
        $streams = Stream::orderBy('name')->get();
        return view('admin.sections.edit', compact('section', 'classes', 'streams'));
    }

    /**
     * Update the specified section.
     */
    public function update(Request $request, Section $section)
    {
        $request->validate([
            'class_id'  => 'required|exists:classes,id',
            'stream_id' => 'nullable|exists:streams,id',
            'name'      => 'required|string|max:20',
            'capacity'  => 'nullable|integer',
        ]);

        $section->update($request->all());

        return redirect()->route('admin.sections.index')
                         ->with('success', 'Section updated successfully.');
    }

    /**
     * Remove the specified section.
     */
    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->route('admin.sections.index')
                         ->with('success', 'Section deleted successfully.');
    }
}