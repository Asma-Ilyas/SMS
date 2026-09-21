@extends('layouts.app')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hostel Rooms</h1>
            <p class="text-sm text-gray-500 mt-1">Manage rooms across all hostels.</p>
        </div>
        <a href="{{ route('admin.hostel-rooms.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">+ Add Room</a>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('error') }}</div>@endif

    @include('admin.partials.search-bar', ['placeholder' => 'Search room #, hostel...'])

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hostel</th>
                        @include('admin.partials.sortable-th', ['field' => 'room_number', 'label' => 'Room #'])
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Floor</th>
                        @include('admin.partials.sortable-th', ['field' => 'current_occupancy', 'label' => 'Occupancy'])
                        @include('admin.partials.sortable-th', ['field' => 'status', 'label' => 'Status'])
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rooms as $room)
                    @php $badge = ['available'=>'bg-green-100 text-green-700','full'=>'bg-yellow-100 text-yellow-700','maintenance'=>'bg-gray-100 text-gray-600'][$room->status] ?? 'bg-gray-100 text-gray-600'; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $room->hostel->name }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $room->room_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $room->roomType?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $room->floor ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $room->current_occupancy }}/{{ $room->capacity }}</td>
                        <td class="px-6 py-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ ucfirst($room->status) }}</span></td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.hostel-rooms.show', $room) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                            <a href="{{ route('admin.hostel-rooms.edit', $room) }}" class="text-gray-600 hover:text-gray-900 font-medium">Edit</a>
                            <form action="{{ route('admin.hostel-rooms.destroy', $room) }}" method="POST" class="inline" onsubmit="return confirm('Delete this room?')">
                                @csrf @method('DELETE')<button class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                        @if(request('search')) No rooms match "{{ request('search') }}". @else No rooms found. @endif
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $rooms->links() }}</div>
</div>
@endsection
