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
        $employees = Staff::with('category')->orderBy('first_name')->orderBy('last_name')->get();
        return view('admin.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'    => 'required|exists:staff,id',
            'date'           => 'required|date',
            'arrival_time'   => 'nullable|date_format:H:i',
            'departure_time' => 'nullable|date_format:H:i|after:arrival_time',
            'remarks'        => 'nullable|string',
        ]);

        $employee = Staff::find($validated['employee_id']);
        $arrival = $validated['arrival_time'] ? Carbon::parse($validated['arrival_time']) : null;
        $departure = $validated['departure_time'] ? Carbon::parse($validated['departure_time']) : null;

        // Calculate metrics
        $metrics = $this->calculateAttendanceMetrics($employee, $arrival, $departure);
        $remarks = $this->buildRemarks($employee, $arrival, $departure, $validated['remarks'] ?? null, $metrics);

        $attendance = Attendance::updateOrCreate(
            [
                'staff_id' => $validated['employee_id'],
                'date'     => $validated['date'],
            ],
            [
                'check_in'        => $arrival ? $arrival->toTimeString() : null,
                'check_out'       => $departure ? $departure->toTimeString() : null,
                'status'          => $metrics['status'],
                'late_minutes'    => $metrics['late_minutes'],
                'overtime_minutes'=> $metrics['overtime_minutes'],
                'work_hours'      => $metrics['work_hours'],
                'remarks'         => $remarks,
            ]
        );

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record saved.');
    }

    public function edit(Attendance $attendance)
    {
        $employees = Staff::with('category')->orderBy('first_name')->orderBy('last_name')->get();
        return view('admin.attendance.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'employee_id'    => 'required|exists:staff,id',
            'date'           => 'required|date',
            'arrival_time'   => 'nullable|date_format:H:i',
            'departure_time' => 'nullable|date_format:H:i|after:arrival_time',
            'remarks'        => 'nullable|string',
        ]);

        // Check uniqueness if employee/date changed
        if ($attendance->staff_id != $validated['employee_id'] || $attendance->date != $validated['date']) {
            $exists = Attendance::where('staff_id', $validated['employee_id'])
                ->where('date', $validated['date'])
                ->exists();
            if ($exists) {
                return back()->withErrors(['error' => 'Attendance record already exists for this employee on this date.'])->withInput();
            }
        }

        $employee = Staff::find($validated['employee_id']);
        $arrival = $validated['arrival_time'] ? Carbon::parse($validated['arrival_time']) : null;
        $departure = $validated['departure_time'] ? Carbon::parse($validated['departure_time']) : null;

        $metrics = $this->calculateAttendanceMetrics($employee, $arrival, $departure);
        $remarks = $this->buildRemarks($employee, $arrival, $departure, $validated['remarks'] ?? null, $metrics);

        $attendance->update([
            'staff_id'        => $validated['employee_id'],
            'date'            => $validated['date'],
            'check_in'        => $arrival ? $arrival->toTimeString() : null,
            'check_out'       => $departure ? $departure->toTimeString() : null,
            'status'          => $metrics['status'],
            'late_minutes'    => $metrics['late_minutes'],
            'overtime_minutes'=> $metrics['overtime_minutes'],
            'work_hours'      => $metrics['work_hours'],
            'remarks'         => $remarks,
        ]);

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

    // ------------------------------------------------------------------
    // Core calculation helper – returns all metrics
    // ------------------------------------------------------------------
    private function calculateAttendanceMetrics($employee, $arrival, $departure)
    {
        $lateMinutes = 0;
        $overtimeMinutes = 0;
        $workHours = 0;
        $status = 'absent';

        if (!$arrival) {
            return [
                'status' => 'absent',
                'late_minutes' => 0,
                'overtime_minutes' => 0,
                'work_hours' => 0,
            ];
        }

        // Check against category arrival time
        $categoryArrival = $employee->category?->arrival_time;
        if ($categoryArrival) {
            $expectedArrival = Carbon::parse($categoryArrival);
            if ($arrival->gt($expectedArrival)) {
                $lateMinutes = $arrival->diffInMinutes($expectedArrival);
            }
        }

        // Default status – if late, set 'late', else 'present'
        $status = ($lateMinutes > 0) ? 'late' : 'present';

        // If check-out exists, calculate work hours and possible half‑day / overtime
        if ($departure) {
            $workMinutes = $arrival->diffInMinutes($departure);
            $workHours = round($workMinutes / 60, 2);

            // Half‑day if less than 4 hours (240 minutes)
            if ($workMinutes < 240) {
                $status = 'half‑day';
            }

            // Overtime: compare with category departure time
            $categoryDeparture = $employee->category?->departure_time;
            if ($categoryDeparture) {
                $expectedDeparture = Carbon::parse($categoryDeparture);
                if ($departure->gt($expectedDeparture)) {
                    $overtimeMinutes = $expectedDeparture->diffInMinutes($departure);
                }
            }
        }

        return [
            'status'           => $status,
            'late_minutes'     => $lateMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'work_hours'       => $workHours,
        ];
    }

    // ------------------------------------------------------------------
    // Build detailed remarks
    // ------------------------------------------------------------------
    private function buildRemarks($employee, $arrival, $departure, $userRemarks, $metrics)
    {
        $remarks = [];

        if ($arrival) {
            $remarks[] = "Checked in at {$arrival->format('H:i')}";
            if ($metrics['late_minutes'] > 0) {
                $remarks[] = "Late by {$metrics['late_minutes']} minutes";
            }
        }

        if ($departure) {
            $remarks[] = "Checked out at {$departure->format('H:i')}";
            $remarks[] = "Worked {$metrics['work_hours']} hours";
            if ($metrics['overtime_minutes'] > 0) {
                $remarks[] = "Overtime: {$metrics['overtime_minutes']} minutes";
            }
            // Check early departure against category
            $categoryDeparture = $employee->category?->departure_time;
            if ($categoryDeparture && $departure->lt(Carbon::parse($categoryDeparture))) {
                $earlyMinutes = $departure->diffInMinutes(Carbon::parse($categoryDeparture));
                $remarks[] = "Left early by {$earlyMinutes} minutes";
            }
        }

        if ($userRemarks) {
            $remarks[] = $userRemarks;
        }

        return implode('; ', $remarks);
    }
}