<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\EmployeeCategory;      // <-- import the category model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::with('category')->latest()->paginate(15); // eager load category
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        $categories = EmployeeCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.staff.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'cnic'          => 'required|string|unique:staff,cnic',
            'email'         => 'required|email|unique:staff,email',
            'employee_id'   => 'required|string|unique:staff,employee_id',
            'category_id'   => 'nullable|exists:employee_categories,id',  // <-- added
            'gender'        => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'phone'         => 'nullable|string',
            'mobile'        => 'nullable|string',
            'present_address'=> 'nullable|string',
            'designation'   => 'nullable|string',
            'department'    => 'nullable|string',
            'joining_date'  => 'nullable|date',
            'qualification' => 'nullable|string',
            'basic_salary'  => 'nullable|numeric',
            'is_active'     => 'boolean',
        ]);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('staff/photos', 'public');
        }

        Staff::create($validated);
        return redirect()->route('admin.staff.index')->with('success', 'Staff created.');
    }

    public function show(Staff $staff)
    {
        $staff->load('category');
        return view('admin.staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        $categories = EmployeeCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.staff.edit', compact('staff', 'categories'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'cnic'          => 'required|string|unique:staff,cnic,'.$staff->id,
            'email'         => 'required|email|unique:staff,email,'.$staff->id,
            'employee_id'   => 'required|string|unique:staff,employee_id,'.$staff->id,
            'category_id'   => 'nullable|exists:employee_categories,id',  // <-- added
            'gender'        => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'phone'         => 'nullable|string',
            'mobile'        => 'nullable|string',
            'present_address'=> 'nullable|string',
            'designation'   => 'nullable|string',
            'department'    => 'nullable|string',
            'joining_date'  => 'nullable|date',
            'qualification' => 'nullable|string',
            'basic_salary'  => 'nullable|numeric',
            'is_active'     => 'boolean',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($staff->profile_photo) Storage::disk('public')->delete($staff->profile_photo);
            $validated['profile_photo'] = $request->file('profile_photo')->store('staff/photos', 'public');
        }

        $staff->update($validated);
        return redirect()->route('admin.staff.index')->with('success', 'Staff updated.');
    }

    public function destroy(Staff $staff)
    {
        if ($staff->profile_photo) Storage::disk('public')->delete($staff->profile_photo);
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Staff deleted.');
    }
}