@extends('layouts.app')
@section('content')
<div class="p-6 max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Room {{ $room->room_number }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ $room->hostel->name }}</p>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Type</dt><dd class="text-sm text-gray-900 mt-1">{{ $room->roomType?->name ?? '—' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Floor</dt><dd class="text-sm text-gray-900 mt-1">{{ $room->floor ?? '—' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Occupancy</dt><dd class="text-sm text-gray-900 mt-1">{{ $room->current_occupancy }} / {{ $room->capacity }}</dd></div>
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase">Status</dt>
                @php $badge = ['available'=>'bg-green-100 text-green-700','full'=>'bg-yellow-100 text-yellow-700','maintenance'=>'bg-gray-100 text-gray-600'][$room->status] ?? 'bg-gray-100 text-gray-600'; @endphp
                <dd class="mt-1"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ ucfirst($room->status) }}</span></dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Currently Allocated Students</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Student</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Bed #</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Since</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($room->activeAllocations as $a)
                    <tr><td class="px-4 py-2 text-sm text-gray-700">{{ $a->student->first_name }} {{ $a->student->last_name }}</td><td class="px-4 py-2 text-sm text-gray-700">{{ $a->bed_number ?? '—' }}</td><td class="px-4 py-2 text-sm text-gray-700">{{ $a->allocation_date->format('d M Y') }}</td></tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-sm text-gray-400">No students currently in this room.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex gap-3">
        <a href="{{ route('admin.hostel-rooms.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Back</a>
        <a href="{{ route('admin.hostel-rooms.edit', $room) }}" class="inline-flex items-center px-4 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-xl hover:bg-gray-900 transition">Edit</a>
    </div>
</div>
@endsection
