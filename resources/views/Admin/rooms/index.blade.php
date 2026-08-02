@extends('layouts.app')

@section('title', 'Rooms Management')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pb-5 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">🏫 Rooms Management</h1>
                <p class="text-sm text-gray-500 mt-1">Manage all rooms in the school</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.rooms.create') }}" 
                   class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    ➕ Add Room
                </a>
                <a href="{{ route('admin.rooms.assignments') }}" 
                   class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    📋 Assignments
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        {{-- Stats Cards --}}
        @if(isset($stats) && $stats)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-2xl shadow-sm p-5 card-hover">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 rounded-xl">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['totalRooms'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Total Rooms</div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-5 card-hover">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-50 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['availableRooms'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Available Rooms</div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-5 card-hover">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-red-50 rounded-xl">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['occupiedRooms'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Occupied Rooms</div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-5 card-hover">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-amber-50 rounded-xl">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['assignedCount'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Assigned to Sections</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 mb-6">
            <form method="GET" action="{{ route('admin.rooms.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name or number..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                @if($blocks->count() > 0)
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Block</label>
                    <select name="block_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Blocks</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                                {{ $block->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                @if($floors->count() > 0)
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
                    <select name="floor_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Floors</option>
                        @foreach($floors as $floor)
                            <option value="{{ $floor->id }}" {{ request('floor_id') == $floor->id ? 'selected' : '' }}>
                                {{ $floor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="flex items-center gap-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="available" value="1" {{ request('available') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Available Only</span>
                    </label>
                </div>
                
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    🔍 Filter
                </button>
                
                @if(request()->hasAny(['search', 'block_id', 'floor_id', 'available']))
                    <a href="{{ route('admin.rooms.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        ✕ Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Rooms Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="font-bold text-gray-800">📋 All Rooms</span>
                <span class="text-xs text-gray-500">{{ $rooms->total() }} rooms</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100/70">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Room</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Number</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Capacity</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($rooms as $room)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $room->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $room->room_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">
                                        {{ ucfirst($room->type ?? 'classroom') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $room->capacity }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($room->is_available)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Available</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">Occupied</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        <a href="{{ route('admin.rooms.show', $room) }}" 
                                           class="text-blue-600 hover:text-blue-900 px-2 py-1" title="View">👁️</a>
                                        <a href="{{ route('admin.rooms.edit', $room) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 px-2 py-1" title="Edit">✏️</a>
                                       
                                        <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this room?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 text-xs" title="Delete">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <div class="text-4xl mb-2">🏫</div>
                                    <p>No rooms found.</p>
                                    <p class="text-sm text-gray-400 mt-1">Create your first room.</p>
                                    <a href="{{ route('admin.rooms.create') }}" class="mt-3 inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                                        ➕ Add Room
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $rooms->links() }}
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    .card-hover {
        transition: all 0.2s ease-in-out;
    }
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush