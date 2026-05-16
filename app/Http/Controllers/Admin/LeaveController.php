<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Staff;          // 👈 changed
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('staff')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.leaves.index', compact('leaves'));
    }

    public function requestLeave(Request $request)
    {
        $staff = Auth::user()->staff;
        if (!$staff) {
            return back()->with('error', 'Staff record not found.');
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'type'       => 'required|in:sick,casual,annual',
            'reason'     => 'required|string',
        ]);

        $validated['employee_id'] = $staff->id;
        $validated['status'] = 'pending';

        Leave::create($validated);

        return redirect()->route('admin.leaves.my')->with('success', 'Leave request submitted successfully.');
    }

    public function approve(Leave $leave)
    {
        $leave->update(['status' => 'approved']);
        return back()->with('success', 'Leave approved.');
    }

    public function reject(Leave $leave)
    {
        $leave->update(['status' => 'rejected']);
        return back()->with('success', 'Leave rejected.');
    }

    public function myLeaves()
    {
        $staff = Auth::user()->staff;
        if (!$staff) {
            return redirect()->route('admin.dashboard')->with('error', 'No staff record found for your account.');
        }

        $leaves = Leave::where('employee_id', $staff->id)->orderBy('created_at', 'desc')->get();
        return view('admin.leaves.my-leaves', compact('leaves'));
    }
}