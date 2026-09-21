@extends('layouts.app')
@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Hostel Dashboard</h1>
    <p class="text-sm text-gray-500 mb-6">Occupancy and fee collection overview.</p>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Active Hostels</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_hostels'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Rooms</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_rooms'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Occupied / Capacity</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_occupied'] }} / {{ $stats['total_capacity'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Occupancy Rate</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['occupancy_percent'] }}%</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-900">Fee Collection — {{ \Carbon\Carbon::parse($currentMonth)->format('F Y') }}</h2>
            @if($revenue['overdue_count'] > 0)
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">{{ $revenue['overdue_count'] }} overdue</span>
            @endif
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500">Expected</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($revenue['expected'], 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Collected</p>
                <p class="text-xl font-bold text-green-600">{{ number_format($revenue['collected'], 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Pending</p>
                <p class="text-xl font-bold text-yellow-600">{{ number_format($revenue['pending'], 2) }}</p>
            </div>
        </div>
        @if($revenue['expected'] > 0)
        <div class="mt-4">
            <div class="w-full bg-gray-100 rounded-full h-2.5">
                <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ min(100, round(($revenue['collected'] / $revenue['expected']) * 100)) }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ round(($revenue['collected'] / $revenue['expected']) * 100) }}% collected this month</p>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Occupancy by Hostel</h2>
        <div class="space-y-4">
            @forelse($occupancyByHostel as $h)
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-medium text-gray-900">{{ $h->name }}</span>
                    <span class="text-xs text-gray-500">{{ $h->room_occupied ?? 0 }} / {{ $h->room_capacity ?? 0 }} ({{ $h->occupancy_percent }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    @php
                        $pct = $h->occupancy_percent;
                        $color = $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-green-500');
                    @endphp
                    <div class="{{ $color }} h-2.5 rounded-full" style="width: {{ min(100, $pct) }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-6">No hostels with rooms yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
