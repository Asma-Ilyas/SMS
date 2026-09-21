@extends('layouts.app')
@section('content')
<div class="p-6 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Room: {{ $room->room_number }}</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.hostel-rooms.update', $room) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hostel</label>
                    <select name="hostel_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
                        @foreach($hostels as $h)
                            <option value="{{ $h->id }}" @selected(old('hostel_id', $room->hostel_id)==$h->id)>{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room Type</label>
                    <select name="room_type_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option value="">-- None --</option>
                        @foreach($roomTypes as $rt)
                            <option value="{{ $rt->id }}" @selected(old('room_type_id', $room->room_type_id)==$rt->id)>{{ $rt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Room Number</label><input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Floor</label><input type="text" name="floor" value="{{ old('floor', $room->floor) }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $room->capacity) }}" min="{{ $room->current_occupancy }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                    <p class="text-xs text-gray-500 mt-1">Currently {{ $room->current_occupancy }} occupied — capacity can't go below this.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        @foreach(['available','full','maintenance'] as $s)
                            <option value="{{ $s }}" @selected(old('status', $room->status)==$s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">{{ old('notes', $room->notes) }}</textarea></div>
            </div>
            <div class="mt-6 flex gap-3">
                <button class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">Update Room</button>
                <a href="{{ route('admin.hostel-rooms.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
