<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeScale;
use Illuminate\Http\Request;

class GradeScaleController extends Controller
{
    public function index()
    {
        $scales = GradeScale::all();
        return view('admin.grade-scales.index', compact('scales'));
    }

    public function create()
    {
        return view('admin.grade-scales.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:grade_scales',
            'grades' => 'required|array',
            'grades.*.min' => 'required|numeric',
            'grades.*.max' => 'required|numeric',
            'grades.*.grade' => 'required|string',
        ]);

        $gradeScale = GradeScale::create([
            'name' => $request->name,
            'grades' => $request->grades,
            'is_default' => $request->has('is_default'),
        ]);

        if ($request->has('is_default')) {
            GradeScale::where('id', '!=', $gradeScale->id)->update(['is_default' => false]);
        }

        return redirect()->route('admin.grade-scales.index')->with('success', 'Grade scale created.');
    }

    public function edit(GradeScale $gradeScale)
    {
        return view('admin.grade-scales.edit', compact('gradeScale'));
    }

    public function update(Request $request, GradeScale $gradeScale)
    {
        $request->validate([
            'name' => 'required|unique:grade_scales,name,'.$gradeScale->id,
            'grades' => 'required|array',
            'grades.*.min' => 'required|numeric',
            'grades.*.max' => 'required|numeric',
            'grades.*.grade' => 'required|string',
        ]);

        $gradeScale->update([
            'name' => $request->name,
            'grades' => $request->grades,
            'is_default' => $request->has('is_default'),
        ]);

        if ($request->has('is_default')) {
            GradeScale::where('id', '!=', $gradeScale->id)->update(['is_default' => false]);
        }

        return redirect()->route('admin.grade-scales.index')->with('success', 'Grade scale updated.');
    }

    public function destroy(GradeScale $gradeScale)
    {
        $gradeScale->delete();
        return back()->with('success', 'Grade scale deleted.');
    }
}