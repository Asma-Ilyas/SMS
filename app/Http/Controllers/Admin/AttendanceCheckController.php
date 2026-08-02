<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;      // ✅ Changed from Attendance to StaffAttendance
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceCheckController extends Controller
{
    public function showCheckinForm()
    {
        $employees = Staff::with('category')->orderBy('first_name')->orderBy('last_name')->get();
        return view('admin.attendance.check', compact('employees'));
    }

    // ------------------------------------------------------------------
    // CHECK‑IN
    // ------------------------------------------------------------------
    public function checkin(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:staff,id',
        ]);

        $employee = Staff::with('category')->find($request->employee_id);
        $today = Carbon::today();
        $now = Carbon::now();

        // Check if already checked in today
        $attendance = StaffAttendance::where('staff_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance && $attendance->check_out) {
            return back()->with('error', 'Already completed attendance for today.');
        }

        if ($attendance && $attendance->check_in && is_null($attendance->check_out)) {
            return back()->with('error', 'Already checked in today. Please check out first.');
        }

        // Determine status based on arrival time vs category default arrival
        $categoryArrival = $employee->category?->arrival_time;
        $status = 'present';
        $remarks = 'Auto check‑in';

        if ($categoryArrival) {
            $arrivalTime = Carbon::parse($categoryArrival);
            if ($now->gt($arrivalTime)) {
                $lateMinutes = $now->diffInMinutes($arrivalTime);
                $status = 'late';
                $remarks = "Late by {$lateMinutes} minutes (arrived at {$now->format('H:i')})";
            }
        }

        // Create or update attendance record
        $attendance = StaffAttendance::updateOrCreate(
            [
                'staff_id' => $employee->id,
                'date'     => $today,
            ],
            [
                'check_in'   => $now->toTimeString(),
                'status'     => $status,
                'remarks'    => $remarks,
                'check_out'  => null,
            ]
        );

        return back()->with('success', "Checked in at {$attendance->check_in} – Status: " . ucfirst($status));
    }

    // ------------------------------------------------------------------
    // CHECK‑OUT
    // ------------------------------------------------------------------
    public function checkout(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:staff,id',
        ]);

        $employee = Staff::with('category')->find($request->employee_id);
        $today = Carbon::today();
        $now = Carbon::now();

        $attendance = StaffAttendance::where('staff_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'No check‑in found for today. Please check in first.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Already checked out today.');
        }

        // Get check‑in time
        $checkIn = Carbon::parse($attendance->check_in);
        $workMinutes = $checkIn->diffInMinutes($now);

        // Determine final status (if not already finalised)
        $finalStatus = $attendance->status; // 'late' or 'present' from check‑in
        $remarks = $attendance->remarks . '; ';

        // Half‑day rule: less than 4 hours (240 minutes) of work
        if ($workMinutes < 240) {
            $finalStatus = 'half_day';   // ✅ Changed to match migration enum ('half_day' not 'half‑day')
            $remarks .= "Worked only {$workMinutes} minutes (half day).";
        } elseif ($workMinutes >= 480) {
            $remarks .= "Full day work ({$workMinutes} minutes).";
        } else {
            $remarks .= "Worked {$workMinutes} minutes.";
        }

        // Check against category departure time (optional)
        $categoryDeparture = $employee->category?->departure_time;
        if ($categoryDeparture && $now->lt(Carbon::parse($categoryDeparture))) {
            $remarks .= " Left early (expected departure at {$categoryDeparture}).";
        } elseif ($categoryDeparture && $now->gt(Carbon::parse($categoryDeparture))) {
            $remarks .= " Overtime (stayed beyond {$categoryDeparture}).";
        }

        $attendance->update([
            'check_out' => $now->toTimeString(),
            'status'    => $finalStatus,
            'remarks'   => trim($remarks),
        ]);

        return back()->with('success', "Checked out at {$attendance->check_out} – Final status: " . ucfirst($finalStatus));
    }
}