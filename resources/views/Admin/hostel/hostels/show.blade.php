@extends('layouts.app')
@section('content')
<div class="p-6 max-w-4xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $hostel->name }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ $hostel->code }}</p>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Type</dt><dd class="text-sm text-gray-900 mt-1">{{ ucfirst($hostel->type) }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Warden</dt><dd class="text-sm text-gray-900 mt-1">{{ $hostel->warden ? $hostel->warden->first_name.' '.$hostel->warden->last_name : '—' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Address</dt><dd class="text-sm text-gray-900 mt-1">{{ $hostel->address ?? '—' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Capacity</dt><dd class="text-sm text-gray-900 mt-1">{{ $hostel->occupiedCount() }} / {{ $hostel->total_capacity ?? '—' }} occupied</dd></div>
        </dl>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Rooms ({{ $hostel->rooms->count() }})</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Room #</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Floor</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Occupancy</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($hostel->rooms as $room)
                    @php $badge = ['available'=>'bg-green-100 text-green-700','full'=>'bg-yellow-100 text-yellow-700','maintenance'=>'bg-gray-100 text-gray-600'][$room->status] ?? 'bg-gray-100 text-gray-600'; @endphp
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $room->room_number }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $room->roomType?->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $room->floor ?? '—' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $room->current_occupancy }}/{{ $room->capacity }}</td>
                        <td class="px-4 py-2"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ ucfirst($room->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-400">No rooms yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Staff Assigned</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Name</th><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Role</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($hostel->staffAssignments as $sa)
                    <tr><td class="px-4 py-2 text-sm text-gray-700">{{ $sa->staff->first_name }} {{ $sa->staff->last_name }}</td><td class="px-4 py-2 text-sm text-gray-700">{{ ucfirst(str_replace('_',' ',$sa->role)) }}</td></tr>
                    @empty
                    <tr><td colspan="2" class="px-4 py-6 text-center text-sm text-gray-400">No staff assigned.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex gap-3">
        <a href="{{ route('admin.hostels.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Back</a>
        <a href="{{ route('admin.hostels.edit', $hostel) }}" class="inline-flex items-center px-4 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-xl hover:bg-gray-900 transition">Edit</a>
    </div>
</div>
@endsection
