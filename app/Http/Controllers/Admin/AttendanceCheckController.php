<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceCheckController extends Controller
{
    public function showCheckinForm()
    {
        $employees = Staff::orderBy('first_name')->orderBy('last_name')->get();
        return view('admin.attendance.check', compact('employees'));
    }

    public function checkin(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:staff,id',
        ]);

        $employeeId = $request->employee_id;
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $employeeId,
                'date'        => $today,
            ],
            [
                'status'      => 'present',
                'check_in'    => Carbon::now()->toTimeString(),
                'check_out'   => null,
                'remarks'     => 'Auto check-in',
            ]
        );

        if ($attendance->wasRecentlyCreated) {
            return back()->with('success', 'Checked in at ' . $attendance->check_in);
        }

        if ($attendance->check_in && is_null($attendance->check_out)) {
            return back()->with('error', 'Already checked in today. Please check out.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Already completed attendance for today.');
        }

        return back()->with('error', 'Unable to mark attendance.');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:staff,id',
        ]);

        $employeeId = $request->employee_id;
        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'No check-in found for today. Please check in first.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Already checked out today.');
        }

        $attendance->check_out = Carbon::now()->toTimeString();
        $attendance->save();

        return back()->with('success', 'Checked out at ' . $attendance->check_out);
    }
}