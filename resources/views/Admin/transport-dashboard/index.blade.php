@extends('layouts.app')
@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Transport Dashboard</h1>
    <p class="text-sm text-gray-500 mb-6">Fleet status and fee collection overview.</p>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Vehicles</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_vehicles'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Active</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active_vehicles'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">In Maintenance</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['maintenance_vehicles'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Inactive</p>
            <p class="text-2xl font-bold text-gray-500 mt-1">{{ $stats['inactive_vehicles'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Active Drivers</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_drivers'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase">Active Routes</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_routes'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 col-span-2">
            <p class="text-xs font-semibold text-gray-500 uppercase">Students Currently Assigned</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['active_assignments'] }}</p>
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
        <h2 class="text-base font-semibold text-gray-900 mb-4">Top Routes by Active Students</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Route</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Active Students</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($revenueByRoute as $route)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $route->name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $route->active_students }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="px-4 py-6 text-center text-sm text-gray-400">No active assignments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
