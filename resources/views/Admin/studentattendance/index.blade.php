{{-- resources/views/admin/studentattendance/index.blade.php --}}

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

        <!-- Statistics Cards with Better Visual Design -->
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

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <!-- Quick Actions Sidebar -->
            <div class="xl:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl transition-all duration-300 sticky top-6">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="p-2 bg-indigo-100 rounded-xl">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800">Quick Actions</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Class Selector with Better UI -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Select Class
                            </label>
                            <div class="relative">
                                <select id="quickClassSelect" 
                                        class="w-full appearance-none bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all hover:border-indigo-300 cursor-pointer">
                                    <option value="">📚 Choose a class...</option>
                                    @foreach($classSections as $section)
                                        <option value="{{ $section->id }}">{{ $section->full_name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <button onclick="goToMark()" 
                                class="group w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white py-3.5 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg hover:shadow-indigo-200 font-medium">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                            Mark Attendance
                        </button>

                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('admin.studentattendance.report') }}" 
                               class="group flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 rounded-xl transition-all duration-300 font-medium text-sm hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Reports
                            </a>
                            <a href="{{ route('admin.studentattendance.section-wise-report') }}" 
                               class="group flex items-center justify-center gap-2 bg-teal-50 hover:bg-teal-100 text-teal-700 py-3 rounded-xl transition-all duration-300 font-medium text-sm hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Section
                            </a>
                        </div>

                        <div class="border-t border-slate-200 pt-4 space-y-2">
                            <a href="{{ route('admin.studentattendance.permissions') }}" 
                               class="group flex items-center gap-3 w-full bg-purple-50 hover:bg-purple-100 text-purple-700 px-4 py-3 rounded-xl transition-all duration-300 font-medium text-sm hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                🔐 Permissions
                            </a>
                            <a href="{{ route('admin.studentattendance.common-classes') }}" 
                               class="group flex items-center gap-3 w-full bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-4 py-3 rounded-xl transition-all duration-300 font-medium text-sm hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                📚 Common Classes
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Attendance Table -->
            <div class="xl:col-span-3">
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
                        <a href="{{ route('admin.studentattendance.report') }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold hover:underline flex items-center gap-1 transition-all">
                            View Full Report
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
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
                                                {{ $att->classSection->full_name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            {!! $att->status_label !!}
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
    </div>
</div>

@push('scripts')
<script>
function goToMark() {
    const classId = document.getElementById('quickClassSelect').value;
    if (classId) {
        window.location.href = "{{ route('admin.studentattendance.create') }}?class_section_id=" + classId;
    } else {
        // Show a beautiful toast notification instead of alert
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-amber-50 border-2 border-amber-400 text-amber-800 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-slide-up z-50';
        toast.innerHTML = `
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <p class="font-semibold">Please select a class first</p>
                <p class="text-sm opacity-80">Choose a class from the dropdown above</p>
            </div>
            <button onclick="this.parentElement.remove()" class="ml-4 text-amber-600 hover:text-amber-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }
}

// Add CSS for toast animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-slide-up {
        animation: slide-up 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection