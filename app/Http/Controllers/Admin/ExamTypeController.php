<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use Illuminate\Http\Request;

class ExamTypeController extends Controller
{
    public function index()
    {
        $examTypes = ExamType::orderBy('sort_order')->orderBy('name')->paginate(20);
        return view('admin.exam-types.index', compact('examTypes'));
    }

    public function create()
    {
        return view('admin.exam-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255|unique:exam_types',
            'code'       => 'nullable|string|max:50|unique:exam_types',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'sometimes|boolean',
        ]);

        ExamType::create($validated);
        return redirect()->route('admin.exam-types.index')
            ->with('success', 'Exam type created successfully.');
    }

    public function edit(ExamType $examType)
    {
        return view('admin.exam-types.edit', compact('examType'));
    }

    public function update(Request $request, ExamType $examType)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255|unique:exam_types,name,' . $examType->id,
            'code'       => 'nullable|string|max:50|unique:exam_types,code,' . $examType->id,
            'sort_order' => 'nullable|integer',
            'is_active'  => 'sometimes|boolean',
        ]);

        $examType->update($validated);
        return redirect()->route('admin.exam-types.index')
            ->with('success', 'Exam type updated successfully.');
    }

    public function destroy(ExamType $examType)
    {
        // Prevent deletion if exams exist
        if ($examType->exams()->count() > 0) {
            return back()->with('error', 'Cannot delete exam type with associated exams.');
        }
        $examType->delete();
        return redirect()->route('admin.exam-types.index')
            ->with('success', 'Exam type deleted.');
    }
}