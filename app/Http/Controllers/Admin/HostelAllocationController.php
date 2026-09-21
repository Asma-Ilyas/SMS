<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHostelAllocationRequest;
use App\Models\StudentHostelAllocation;
use App\Models\Student;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\HostelFeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelAllocationController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentHostelAllocation::with('student', 'hostel', 'room');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('student', fn ($sq) => $sq->where('first_name', 'like', "%{$s}%")->orWhere('last_name', 'like', "%{$s}%")->orWhere('admission_number', 'like', "%{$s}%"))
                  ->orWhereHas('hostel', fn ($hq) => $hq->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('room', fn ($rq) => $rq->where('room_number', 'like', "%{$s}%"));
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $sortable = ['status', 'allocation_date', 'created_at'];
        if (in_array($sort, $sortable)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $allocations = $query->paginate(20)->withQueryString();
        return view('admin.hostel.allocations.index', compact('allocations'));
    }

    public function create()
    {
        $students = Student::orderBy('first_name')->get();
        $hostels = Hostel::active()->orderBy('name')->get();
        return view('admin.hostel.allocations.create', compact('students', 'hostels'));
    }

    public function store(StoreHostelAllocationRequest $request)
    {
        $data = $request->validated();

        // A student already actively allocated somewhere must be vacated first.
        $alreadyAllocated = StudentHostelAllocation::where('student_id', $data['student_id'])
            ->where('status', 'active')
            ->exists();

        if ($alreadyAllocated) {
            return back()->withInput()->with('error', 'This student already has an active hostel allocation. Vacate it first before reassigning.');
        }

        try {
            $allocation = DB::transaction(function () use ($data) {
                // Lock the room row so concurrent requests can't both pass the vacancy check.
                $room = HostelRoom::where('id', $data['room_id'])->lockForUpdate()->firstOrFail();

                if ($room->current_occupancy >= $room->capacity) {
                    throw new \RuntimeException('ROOM_FULL');
                }

                $data['status'] = 'active';
                $allocation = StudentHostelAllocation::create($data);

                $room->increment('current_occupancy');
                if ($room->fresh()->current_occupancy >= $room->capacity) {
                    $room->update(['status' => 'full']);
                }

                return $allocation;
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'ROOM_FULL') {
                return back()->withInput()->with('error', 'This room reached full capacity just now. Please pick another room.');
            }
            throw $e;
        }

        return redirect()->route('admin.hostel-allocations.index')->with('success', 'Student allocated to room successfully.');
    }

    public function show(StudentHostelAllocation $hostelAllocation)
    {
        $hostelAllocation->load('student', 'hostel', 'room', 'feeType', 'feePayments');
        return view('admin.hostel.allocations.show', ['allocation' => $hostelAllocation]);
    }

    public function edit(StudentHostelAllocation $hostelAllocation)
    {
        $hostels = Hostel::active()->orderBy('name')->get();
        return view('admin.hostel.allocations.edit', ['allocation' => $hostelAllocation, 'hostels' => $hostels]);
    }

    public function update(Request $request, StudentHostelAllocation $hostelAllocation)
    {
        $data = $request->validate([
            'hostel_fee_type_id' => 'nullable|exists:hostel_fee_types,id',
            'bed_number' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $hostelAllocation->update($data);

        return redirect()->route('admin.hostel-allocations.index')->with('success', 'Allocation updated successfully.');
    }

    public function vacate(Request $request, StudentHostelAllocation $hostelAllocation)
    {
        $request->validate(['vacate_date' => 'required|date']);

        if ($hostelAllocation->status !== 'active') {
            return back()->with('error', 'This allocation is already vacated.');
        }

        DB::transaction(function () use ($hostelAllocation, $request) {
            $hostelAllocation->update([
                'status' => 'vacated',
                'vacate_date' => $request->vacate_date,
            ]);

            $room = HostelRoom::where('id', $hostelAllocation->room_id)->lockForUpdate()->first();
            if ($room && $room->current_occupancy > 0) {
                $room->decrement('current_occupancy');
                $room->update(['status' => $room->fresh()->current_occupancy > 0 ? 'available' : 'available']);
            }
        });

        return back()->with('success', 'Student vacated from hostel successfully.');
    }

    public function destroy(StudentHostelAllocation $hostelAllocation)
    {
        DB::transaction(function () use ($hostelAllocation) {
            if ($hostelAllocation->status === 'active') {
                $room = HostelRoom::where('id', $hostelAllocation->room_id)->lockForUpdate()->first();
                if ($room && $room->current_occupancy > 0) {
                    $room->decrement('current_occupancy');
                    $room->update(['status' => 'available']);
                }
            }
            $hostelAllocation->delete();
        });

        return redirect()->route('admin.hostel-allocations.index')->with('success', 'Allocation removed successfully.');
    }

    public function getRoomsByHostel($hostelId)
    {
        $rooms = HostelRoom::where('hostel_id', $hostelId)
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->where('status', '!=', 'maintenance')
            ->get();

        return response()->json($rooms);
    }
}
