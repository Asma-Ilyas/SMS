<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamGroup;
use Illuminate\Http\Request;

class ExamGroupController extends Controller
{
    public function index()
    {
        $groups = ExamGroup::orderBy('sort_order')->orderBy('name')->paginate(20);
        return view('admin.exam-groups.index', compact('groups'));
    }

    public function create()
    {
        return view('admin.exam-groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:exam_groups',
            'code'        => 'nullable|string|max:50|unique:exam_groups',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'sometimes|boolean',
        ]);

        ExamGroup::create($validated);
        return redirect()->route('admin.exam-groups.index')->with('success', 'Exam group created.');
    }

    public function edit(ExamGroup $examGroup)
    {
        return view('admin.exam-groups.edit', compact('examGroup'));
    }

    public function update(Request $request, ExamGroup $examGroup)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:exam_groups,name,' . $examGroup->id,
            'code'        => 'nullable|string|max:50|unique:exam_groups,code,' . $examGroup->id,
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'sometimes|boolean',
        ]);

        $examGroup->update($validated);
        return redirect()->route('admin.exam-groups.index')->with('success', 'Exam group updated.');
    }

    public function destroy(ExamGroup $examGroup)
    {
        if ($examGroup->exams()->count() > 0) {
            return back()->with('error', 'Cannot delete group with associated exams.');
        }
        $examGroup->delete();
        return redirect()->route('admin.exam-groups.index')->with('success', 'Exam group deleted.');
    }
}