<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHostelRoomRequest;
use App\Http\Requests\UpdateHostelRoomRequest;
use App\Models\HostelRoom;
use App\Models\Hostel;
use App\Models\HostelRoomType;
use Illuminate\Http\Request;

class HostelRoomController extends Controller
{
    public function index(Request $request)
    {
        $query = HostelRoom::with('hostel', 'roomType');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('room_number', 'like', "%{$s}%")
                  ->orWhereHas('hostel', fn ($hq) => $hq->where('name', 'like', "%{$s}%"));
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $sortable = ['room_number', 'capacity', 'current_occupancy', 'status', 'created_at'];
        if (in_array($sort, $sortable)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $rooms = $query->paginate(20)->withQueryString();
        return view('admin.hostel.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $hostels = Hostel::active()->orderBy('name')->get();
        $roomTypes = HostelRoomType::orderBy('name')->get();
        return view('admin.hostel.rooms.create', compact('hostels', 'roomTypes'));
    }

    public function store(StoreHostelRoomRequest $request)
    {
        $data = $request->validated();
        $data['status'] = 'available';
        $data['current_occupancy'] = 0;

        HostelRoom::create($data);

        return redirect()->route('admin.hostel-rooms.index')->with('success', 'Room added successfully.');
    }

    public function show(HostelRoom $hostelRoom)
    {
        $hostelRoom->load('hostel', 'roomType', 'activeAllocations.student');
        return view('admin.hostel.rooms.show', ['room' => $hostelRoom]);
    }

    public function edit(HostelRoom $hostelRoom)
    {
        $hostels = Hostel::active()->orderBy('name')->get();
        $roomTypes = HostelRoomType::orderBy('name')->get();
        return view('admin.hostel.rooms.edit', ['room' => $hostelRoom, 'hostels' => $hostels, 'roomTypes' => $roomTypes]);
    }

    public function update(UpdateHostelRoomRequest $request, HostelRoom $hostelRoom)
    {
        $data = $request->validated();

        // Guard against setting capacity below current occupancy.
        if ($data['capacity'] < $hostelRoom->current_occupancy) {
            return back()->withInput()->with('error', "Capacity can't be lower than the current occupancy ({$hostelRoom->current_occupancy}).");
        }

        $hostelRoom->update($data);

        return redirect()->route('admin.hostel-rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(HostelRoom $hostelRoom)
    {
        if ($hostelRoom->current_occupancy > 0) {
            return back()->with('error', 'Cannot delete a room with students currently allocated.');
        }

        $hostelRoom->delete();

        return redirect()->route('admin.hostel-rooms.index')->with('success', 'Room deleted successfully.');
    }
}
