@extends('layouts.app')
@section('content')
<div class="p-6 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Allocate Student to Hostel</h1>
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('error') }}</div>@endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.hostel-allocations.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                    <select name="student_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
                        <option value="">-- Select Student --</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" @selected(old('student_id')==$s->id)>{{ $s->first_name }} {{ $s->last_name }} ({{ $s->admission_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hostel</label>
                    <select name="hostel_id" id="hostelSelect" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
                        <option value="">-- Select Hostel --</option>
                        @foreach($hostels as $h)
                            <option value="{{ $h->id }}" @selected(old('hostel_id')==$h->id)>{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room (only rooms with vacancy shown)</label>
                    <select name="room_id" id="roomSelect" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
                        <option value="">-- Select Hostel First --</option>
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Bed Number (optional)</label><input type="text" name="bed_number" value="{{ old('bed_number') }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Allocation Date</label><input type="date" name="allocation_date" value="{{ old('allocation_date') }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label><textarea name="remarks" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">{{ old('remarks') }}</textarea></div>
            </div>
            <div class="mt-6 flex gap-3">
                <button class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">Save Allocation</button>
                <a href="{{ route('admin.hostel-allocations.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    const hostelSelect = document.getElementById('hostelSelect');
    const roomSelect = document.getElementById('roomSelect');

    function loadRooms() {
        const hostelId = hostelSelect.value;
        roomSelect.innerHTML = '<option value="">Loading...</option>';
        if (!hostelId) {
            roomSelect.innerHTML = '<option value="">-- Select Hostel First --</option>';
            return;
        }
        fetch('/admin/hostels/' + hostelId + '/rooms')
            .then(r => r.json())
            .then(rooms => {
                roomSelect.innerHTML = '';
                if (rooms.length === 0) {
                    roomSelect.innerHTML = '<option value="">No rooms with vacancy</option>';
                    return;
                }
                roomSelect.innerHTML = '<option value="">-- Select Room --</option>';
                rooms.forEach(function (room) {
                    const opt = document.createElement('option');
                    opt.value = room.id;
                    opt.textContent = room.room_number + ' (' + room.current_occupancy + '/' + room.capacity + ')';
                    roomSelect.appendChild(opt);
                });
            });
    }

    hostelSelect.addEventListener('change', loadRooms);
    if (hostelSelect.value) loadRooms();
</script>
@endsection
