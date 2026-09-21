@extends('layouts.app')

@section('title', 'Student Attendance Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50/30 py-6">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section with Stats -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg shadow-indigo-200">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">
                                Attendance Overview
                            </h1>
                            <p class="text-slate-500 text-sm flex items-center gap-2 mt-0.5">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ now()->format('l, F d, Y') }}
                                </span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span class="inline-flex items-center gap-1 text-emerald-600 font-medium">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                    Live
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.studentattendance.create') }}" 
                       class="group px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:shadow-xl hover:shadow-indigo-200 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-2 font-medium">
                        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Mark Attendance
                    </a>
                    
                    <a href="{{ route('admin.studentattendance.pending-leaves') }}" 
                       class="group relative px-5 py-2.5 bg-white border-2 border-amber-400 text-amber-700 rounded-xl hover:shadow-xl hover:shadow-amber-100 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-2 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pending Requests
                        @if(($pendingLeaves ?? 0) + ($pendingHalfDays ?? 0) > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-lg shadow-red-200 animate-bounce">
                                {{ ($pendingLeaves ?? 0) + ($pendingHalfDays ?? 0) }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8">
            <!-- Total Card -->
            <div class="group bg-white rounded-2xl p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">📌</span>
                    <span class="text-xs font-semibold text-slate-400 bg-slate-100 px-2 py-1 rounded-full">Total</span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-slate-800">{{ $todayStats['total'] ?? 0 }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">Students today</div>
                </div>
                <div class="mt-2 h-1 w-full bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full w-full bg-slate-400 rounded-full"></div>
                </div>
            </div>

            <!-- Present Card -->
            <div class="group bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-2xl p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-2 border-emerald-200">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">✅</span>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-200 px-2 py-1 rounded-full">
                        {{ $todayStats['total'] > 0 ? round(($todayStats['present'] / $todayStats['total']) * 100) : 0 }}%
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-emerald-600">{{ $todayStats['present'] ?? 0 }}</div>
                    <div class="text-xs text-emerald-600/70 mt-0.5">Present</div>
                </div>
                <div class="mt-2 h-1 w-full bg-emerald-200 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" 
                         style="width: {{ $todayStats['total'] > 0 ? ($todayStats['present'] / $todayStats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- Absent Card -->
            <div class="group bg-gradient-to-br from-rose-50 to-rose-100/50 rounded-2xl p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-2 border-rose-200">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">❌</span>
                    <span class="text-xs font-semibold text-rose-600 bg-rose-200 px-2 py-1 rounded-full">
                        {{ $todayStats['total'] > 0 ? round(($todayStats['absent'] / $todayStats['total']) * 100) : 0 }}%
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-rose-600">{{ $todayStats['absent'] ?? 0 }}</div>
                    <div class="text-xs text-rose-600/70 mt-0.5">Absent</div>
                </div>
                <div class="mt-2 h-1 w-full bg-rose-200 rounded-full overflow-hidden">
                    <div class="h-full bg-rose-500 rounded-full transition-all duration-1000" 
                         style="width: {{ $todayStats['total'] > 0 ? ($todayStats['absent'] / $todayStats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- Late Card -->
            <div class="group bg-gradient-to-br from-amber-50 to-amber-100/50 rounded-2xl p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-2 border-amber-200">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">⏰</span>
                    <span class="text-xs font-semibold text-amber-600 bg-amber-200 px-2 py-1 rounded-full">
                        {{ $todayStats['total'] > 0 ? round(($todayStats['late'] / $todayStats['total']) * 100) : 0 }}%
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-amber-600">{{ $todayStats['late'] ?? 0 }}</div>
                    <div class="text-xs text-amber-600/70 mt-0.5">Late</div>
                </div>
                <div class="mt-2 h-1 w-full bg-amber-200 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 rounded-full transition-all duration-1000" 
                         style="width: {{ $todayStats['total'] > 0 ? ($todayStats['late'] / $todayStats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- Half Day Card -->
            <div class="group bg-gradient-to-br from-sky-50 to-sky-100/50 rounded-2xl p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-2 border-sky-200">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">🌓</span>
                    <span class="text-xs font-semibold text-sky-600 bg-sky-200 px-2 py-1 rounded-full">
                        {{ $todayStats['total'] > 0 ? round(($todayStats['half_day'] / $todayStats['total']) * 100) : 0 }}%
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-sky-600">{{ $todayStats['half_day'] ?? 0 }}</div>
                    <div class="text-xs text-sky-600/70 mt-0.5">Half Day</div>
                </div>
                <div class="mt-2 h-1 w-full bg-sky-200 rounded-full overflow-hidden">
                    <div class="h-full bg-sky-500 rounded-full transition-all duration-1000" 
                         style="width: {{ $todayStats['total'] > 0 ? ($todayStats['half_day'] / $todayStats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- Leave Card -->
            <div class="group bg-gradient-to-br from-violet-50 to-violet-100/50 rounded-2xl p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-2 border-violet-200">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">📝</span>
                    <span class="text-xs font-semibold text-violet-600 bg-violet-200 px-2 py-1 rounded-full">
                        {{ $todayStats['total'] > 0 ? round(($todayStats['leave'] / $todayStats['total']) * 100) : 0 }}%
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-violet-600">{{ $todayStats['leave'] ?? 0 }}</div>
                    <div class="text-xs text-violet-600/70 mt-0.5">On Leave</div>
                </div>
                <div class="mt-2 h-1 w-full bg-violet-200 rounded-full overflow-hidden">
                    <div class="h-full bg-violet-500 rounded-full transition-all duration-1000" 
                         style="width: {{ $todayStats['total'] > 0 ? ($todayStats['leave'] / $todayStats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- Other Card -->
            <div class="group bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-2xl p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-2 border-slate-200">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">🎉</span>
                    <span class="text-xs font-semibold text-slate-600 bg-slate-200 px-2 py-1 rounded-full">
                        {{ $todayStats['total'] > 0 ? round((($todayStats['holiday'] ?? 0) + ($todayStats['on_duty'] ?? 0)) / $todayStats['total'] * 100) : 0 }}%
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-slate-600">{{ ($todayStats['holiday'] ?? 0) + ($todayStats['on_duty'] ?? 0) }}</div>
                    <div class="text-xs text-slate-600/70 mt-0.5">Other</div>
                </div>
                <div class="mt-2 h-1 w-full bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-slate-500 rounded-full transition-all duration-1000" 
                         style="width: {{ $todayStats['total'] > 0 ? ((($todayStats['holiday'] ?? 0) + ($todayStats['on_duty'] ?? 0)) / $todayStats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance Table - Full Width -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-indigo-100 rounded-xl">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800">Recent Activity</h2>
                    <span class="px-2.5 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">
                        {{ count($recentAttendance ?? []) }} records
                    </span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.studentattendance.report') }}" 
                       class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold hover:underline flex items-center gap-1 transition-all">
                        View Full Report
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('admin.studentattendance.create') }}" 
                       class="text-sm bg-indigo-600 text-white px-3 py-1 rounded-lg hover:bg-indigo-700 transition">
                        + Mark
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    Student
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Class</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentAttendance ?? [] as $att)
                            <tr class="group hover:bg-gradient-to-r hover:from-indigo-50/50 hover:to-transparent transition-all duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm">
                                            {{ strtoupper(substr($att->student->first_name ?? 'N', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-800 text-sm">
                                                {{ $att->student->first_name ?? 'N/A' }} {{ $att->student->last_name ?? '' }}
                                            </div>
                                            <div class="text-xs text-slate-400">
                                                Roll #{{ $att->student->roll_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded-full">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ $att->classSection->section_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @switch($att->status)
                                        @case('present')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                                ✅ Present
                                            </span>
                                            @break
                                        @case('absent')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-100 text-rose-700 text-xs font-semibold rounded-full">
                                                <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
                                                ❌ Absent
                                            </span>
                                            @break
                                        @case('late')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                                ⏰ Late
                                            </span>
                                            @break
                                        @case('half_day')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-100 text-sky-700 text-xs font-semibold rounded-full">
                                                <span class="w-1.5 h-1.5 bg-sky-500 rounded-full"></span>
                                                🌓 Half Day
                                            </span>
                                            @break
                                        @case('leave')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-100 text-violet-700 text-xs font-semibold rounded-full">
                                                <span class="w-1.5 h-1.5 bg-violet-500 rounded-full"></span>
                                                📝 Leave
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full">
                                                {{ $att->status ?? 'N/A' }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-600">
                                        {{ $att->date->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        {{ $att->created_at->format('h:i A') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-slate-600 font-medium">No recent attendance records</p>
                                            <p class="text-slate-400 text-sm mt-1">Start marking attendance to see data here</p>
                                        </div>
                                        <a href="{{ route('admin.studentattendance.create') }}" 
                                           class="mt-2 px-4 py-2 bg-indigo-600 text-white text-sm rounded-xl hover:bg-indigo-700 transition-colors">
                                            Mark First Attendance
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Simple auto-navigate when class is selected from dropdown
document.addEventListener('DOMContentLoaded', function() {
    // If there's any dropdown for class selection, auto-navigate
    const select = document.getElementById('quickClassSelect');
    if (select) {
        select.addEventListener('change', function() {
            if (this.value) {
                window.location.href = "{{ route('admin.studentattendance.create') }}?class_section_id=" + this.value;
            }
        });
    }
});
</script>
@endpush
@endsection