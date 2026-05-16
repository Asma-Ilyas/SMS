<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeCategory;
use Illuminate\Http\Request;

class EmployeeCategoryController extends Controller
{
    public function index()
    {
        $categories = EmployeeCategory::orderBy('name')->paginate(20);
        return view('admin.employee-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.employee-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:employee_categories',
            'code'        => 'nullable|string|max:50|unique:employee_categories',
            'description' => 'nullable|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        EmployeeCategory::create($validated);
        return redirect()->route('admin.employee-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(EmployeeCategory $employeeCategory)
    {
        return view('admin.employee-categories.edit', compact('employeeCategory'));
    }

    public function update(Request $request, EmployeeCategory $employeeCategory)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:employee_categories,name,' . $employeeCategory->id,
            'code'        => 'nullable|string|max:50|unique:employee_categories,code,' . $employeeCategory->id,
            'description' => 'nullable|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        $employeeCategory->update($validated);
        return redirect()->route('admin.employee-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(EmployeeCategory $employeeCategory)
    {
        // Prevent deletion if it has employees
        if ($employeeCategory->employees()->count() > 0) {
            return back()->with('error', 'Cannot delete category with assigned employees.');
        }
        $employeeCategory->delete();
        return redirect()->route('admin.employee-categories.index')
            ->with('success', 'Category deleted.');
    }
}