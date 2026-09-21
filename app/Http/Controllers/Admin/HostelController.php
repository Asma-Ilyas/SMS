<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHostelRequest;
use App\Http\Requests\UpdateHostelRequest;
use App\Models\Hostel;
use App\Models\Staff;
use Illuminate\Http\Request;

class HostelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hostel::with('warden')->withCount('rooms');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $sortable = ['name', 'code', 'type', 'is_active', 'created_at'];
        if (in_array($sort, $sortable)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $hostels = $query->paginate(20)->withQueryString();
        return view('admin.hostel.hostels.index', compact('hostels'));
    }

    public function create()
    {
        $staff = Staff::where('is_active', true)->orderBy('first_name')->get();
        return view('admin.hostel.hostels.create', compact('staff'));
    }

    public function store(StoreHostelRequest $request)
    {
        Hostel::create($request->validated());

        return redirect()->route('admin.hostels.index')->with('success', 'Hostel created successfully.');
    }

    public function show(Hostel $hostel)
    {
        $hostel->load('warden', 'rooms.roomType', 'staffAssignments.staff', 'feeTypes');
        return view('admin.hostel.hostels.show', compact('hostel'));
    }

    public function edit(Hostel $hostel)
    {
        $staff = Staff::where('is_active', true)->orderBy('first_name')->get();
        return view('admin.hostel.hostels.edit', compact('hostel', 'staff'));
    }

    public function update(UpdateHostelRequest $request, Hostel $hostel)
    {
        $hostel->update($request->validated());

        return redirect()->route('admin.hostels.index')->with('success', 'Hostel updated successfully.');
    }

    public function destroy(Hostel $hostel)
    {
        if ($hostel->allocations()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete a hostel with currently allocated students. Vacate them first.');
        }

        $hostel->delete();

        return redirect()->route('admin.hostels.index')->with('success', 'Hostel deleted successfully.');
    }
}
