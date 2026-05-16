<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Staff;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('staff');

        if ($request->filled('employee_id')) {
            $query->where('staff_id', $request->employee_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);
        $employees = Staff::orderBy('first_name')->orderBy('last_name')->get();

        return view('admin.attendance.index', compact('attendances', 'employees'));
    }

    public function create()
    {
        $employees = Staff::orderBy('first_name')->orderBy('last_name')->get();
        return view('admin.attendance.create', compact('employees'));
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'date' => 'required|date',
        'status' => 'required|in:present,absent,late,half-day,leave',
        'arrival_time' => 'nullable|date_format:H:i',
        'departure_time' => 'nullable|date_format:H:i',
        'remarks' => 'nullable|string',
    ]);

    // If status is leave or absent, force times to null
    if (in_array($validated['status'], ['leave', 'absent'])) {
        $validated['arrival_time'] = null;
        $validated['departure_time'] = null;
    }

    Attendance::create($validated);
        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record created.');
    }

    public function edit(Attendance $attendance)
    {
        $employees = Staff::orderBy('first_name')->orderBy('last_name')->get();
        return view('admin.attendance.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:staff,id',
            'date'        => 'required|date|unique:attendances,date,' . $attendance->id . ',id,employee_id,' . $request->employee_id,
            'check_in'    => 'nullable|date_format:H:i',
            'check_out'   => 'nullable|date_format:H:i|after:check_in',
            'status'      => 'required|in:present,absent,late,half-day',
            'remarks'     => 'nullable|string',
        ]);

        $attendance->update($validated);
        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record updated.');
    }

    public function show(Attendance $attendance)
{
    return redirect()->route('admin.attendance.index');
}
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record deleted.');
    }
}