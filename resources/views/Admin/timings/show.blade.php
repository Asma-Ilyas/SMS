@extends('layouts.app')

@section('title', $schoolTiming->session_name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $schoolTiming->session_name }}</h1>
                <p class="text-gray-500">{{ $schoolTiming->session_start->format('d M Y') }} – {{ $schoolTiming->session_end->format('d M Y') }}</p>
            </div>
            <div class="space-x-2">
                <a href="{{ route('admin.school-timings.edit', $schoolTiming) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md">Edit Session</a>
                <a href="{{ route('admin.slots.index', $schoolTiming) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Manage Slots</a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded shadow-sm">
                <h3 class="text-sm font-medium text-gray-500">School Timing</h3>
                <p class="text-lg font-semibold">{{ $schoolTiming->school_start }} – {{ $schoolTiming->school_end }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow-sm">
                <h3 class="text-sm font-medium text-gray-500">Period Duration</h3>
                <p class="text-lg font-semibold">{{ $schoolTiming->period_duration }} min</p>
            </div>
            <div class="bg-white p-4 rounded shadow-sm">
                <h3 class="text-sm font-medium text-gray-500">Total Slots</h3>
                <p class="text-lg font-semibold">{{ $slots->count() }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow-sm">
                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $schoolTiming->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $schoolTiming->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
        </div>

        <!-- Breaks & Activity Badges -->
        @if($schoolTiming->breaks)
        <div class="bg-white p-4 rounded shadow-sm mb-6">
            <h3 class="text-md font-medium text-gray-900 mb-2">Breaks</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($schoolTiming->breaks as $break)
                <span class="bg-gray-100 px-3 py-1 rounded-full text-sm">{{ $break['label'] }} ({{ $break['start'] }}–{{ $break['end'] }})</span>
                @endforeach
            </div>
        </div>
        @endif

        @if($schoolTiming->has_activity)
        <div class="bg-white p-4 rounded shadow-sm mb-6">
            <h3 class="text-md font-medium text-gray-900">Activity Slot</h3>
            <p>{{ $schoolTiming->activity_label }}: {{ $schoolTiming->activity_start }} – {{ $schoolTiming->activity_end }}</p>
        </div>
        @endif

        <!-- Slots Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Generated Time Slots</h2>
                <a href="{{ route('admin.slots.create', $schoolTiming) }}" class="text-sm bg-indigo-600 text-white px-3 py-1 rounded">+ Manual Slot</a>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Label</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Start</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">End</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Period #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($slots as $slot)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $slot->sort_order }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $slot->label }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $slot->start_time }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $slot->end_time }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full
                                @if($slot->type === 'period') bg-blue-100 text-blue-800
                                @elseif($slot->type === 'break') bg-yellow-100 text-yellow-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ ucfirst($slot->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $slot->period_number ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($slot->is_active) Active @else Inactive @endif
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.slots.edit', [$schoolTiming, $slot]) }}" class="text-indigo-600">Edit</a>
                            <form action="{{ route('admin.slots.toggle', [$schoolTiming, $slot]) }}" method="POST" class="inline-block">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-yellow-600">{{ $slot->is_active ? 'Disable' : 'Enable' }}</button>
                            </form>
                            <form action="{{ route('admin.slots.destroy', [$schoolTiming, $slot]) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this slot?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">No slots generated yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection