<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use App\Models\Grade;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::orderBy('numeric_value')->paginate(15);
        return view('admin.grades.index', compact('grades'));
    }

    public function create()
    {
        return view('admin.grades.create');
    }

    public function store(StoreGradeRequest $request)
    {
        Grade::create($request->validated());
        return redirect()->route('admin.grades.index')->with('success', 'Grade created.');
    }

    public function edit(Grade $grade)
    {
        return view('admin.grades.edit', compact('grade'));
    }

    public function update(UpdateGradeRequest $request, Grade $grade)
    {
        $grade->update($request->validated());
        return redirect()->route('admin.grades.index')->with('success', 'Grade updated.');
    }

    public function destroy(Grade $grade)
    {
        if ($grade->classes()->exists()) {
            return back()->with('error', 'Cannot delete grade because it has associated classes.');
        }
        $grade->delete();
        return redirect()->route('admin.grades.index')->with('success', 'Grade deleted.');
    }
}