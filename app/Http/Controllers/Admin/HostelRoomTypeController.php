<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostelRoomType;
use Illuminate\Http\Request;

class HostelRoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = HostelRoomType::withCount('rooms')->latest()->paginate(20);
        return view('admin.hostel.room-types.index', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'default_capacity' => 'required|integer|min:1|max:20',
            'description' => 'nullable|string',
        ]);

        HostelRoomType::create($data);

        return back()->with('success', 'Room type created successfully.');
    }

    public function update(Request $request, HostelRoomType $hostelRoomType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'default_capacity' => 'required|integer|min:1|max:20',
            'description' => 'nullable|string',
        ]);

        $hostelRoomType->update($data);

        return back()->with('success', 'Room type updated successfully.');
    }

    public function destroy(HostelRoomType $hostelRoomType)
    {
        if ($hostelRoomType->rooms()->exists()) {
            return back()->with('error', 'Cannot delete a room type that is in use by existing rooms.');
        }

        $hostelRoomType->delete();

        return back()->with('success', 'Room type deleted successfully.');
    }
}
