<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHostelStaffRequest;
use App\Models\Hostel;
use App\Models\HostelStaffAssignment;
use App\Models\Staff;

class HostelStaffController extends Controller
{
    public function index()
    {
        $assignments = HostelStaffAssignment::with('hostel', 'staff')->latest()->paginate(20);
        return view('admin.hostel.staff.index', compact('assignments'));
    }

    public function create()
    {
        $hostels = Hostel::active()->orderBy('name')->get();
        $staff = Staff::where('is_active', true)->orderBy('first_name')->get();
        return view('admin.hostel.staff.create', compact('hostels', 'staff'));
    }

    public function store(StoreHostelStaffRequest $request)
    {
        $data = $request->validated();

        HostelStaffAssignment::create($data);

        // Keep the hostel's primary warden field in sync when role is warden.
        if ($data['role'] === 'warden') {
            Hostel::where('id', $data['hostel_id'])->update(['warden_id' => $data['staff_id']]);
        }

        return redirect()->route('admin.hostel-staff.index')->with('success', 'Staff assigned to hostel successfully.');
    }

    public function destroy(HostelStaffAssignment $hostelStaff)
    {
        $hostelStaff->delete();
        return redirect()->route('admin.hostel-staff.index')->with('success', 'Assignment removed successfully.');
    }
}
