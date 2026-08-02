@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #818cf8;
        --primary-dark: #3730a3;
        --success: #22c55e;
        --warning: #eab308;
        --danger: #ef4444;
        --info: #3b82f6;
    }

    .stat-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        background: white;
        border: 1px solid rgba(229, 231, 235, 0.6);
        padding: 1.25rem;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 1rem 1rem 0 0;
    }
    
    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .stat-card .stat-icon {
        transition: all 0.3s ease;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(-5deg);
    }

    .stat-card-primary::before { background: var(--primary); }
    .stat-card-success::before { background: var(--success); }
    .stat-card-warning::before { background: var(--warning); }
    .stat-card-danger::before { background: var(--danger); }
    .stat-card-info::before { background: var(--info); }
    .stat-card-purple::before { background: #8b5cf6; }

    .stat-number {
        font-size: 1.875rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        line-height: 1.2;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6d28d9 100%);
        border-radius: 1.5rem;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
    }
    
    .welcome-banner::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    
    .welcome-banner::before {
        content: '';
        position: absolute;
        bottom: -30%;
        left: 20%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
    }

    .welcome-banner .content {
        position: relative;
        z-index: 10;
    }

    .activity-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(229, 231, 235, 0.6);
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-item:hover {
        background: #f8fafc;
        padding-left: 0.5rem;
        border-radius: 0.5rem;
    }
    
    .activity-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
        border: 2px solid white;
        box-shadow: 0 0 0 3px var(--dot-color, #4f46e5);
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .pulse-dot {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .progress-bar {
        height: 6px;
        border-radius: 9999px;
        background: #e5e7eb;
        overflow: hidden;
    }
    
    .progress-bar .progress-fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 1s ease;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
    }

    @media (max-width: 768px) {
        .welcome-banner {
            padding: 1.5rem;
        }
        .stat-number {
            font-size: 1.5rem;
        }
    }
</style>

<div class="min-h-screen bg-gray-50/80 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- WELCOME BANNER --}}
        <div class="welcome-banner mb-8">
            <div class="content flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white">
                        👋 Good {{ \Carbon\Carbon::now()->format('A') == 'AM' ? 'Morning' : 'Evening' }}, {{ auth()->user()?->name ?? 'Admin' }}!
                    </h1>
                    <p class="mt-1 text-indigo-200 text-sm md:text-base">
                        Here's your school performance overview for today
                    </p>
                    <div class="mt-3 flex flex-wrap items-center gap-4">
                        <span class="inline-flex items-center gap-2 text-white/80 text-sm">
                            <span class="pulse-dot inline-block w-2 h-2 bg-green-400 rounded-full"></span>
                            All systems operational
                        </span>
                        <span class="text-white/30">|</span>
                        <span class="text-white/80 text-sm">
                            📅 {{ \Carbon\Carbon::now()->format('l, d M Y') }}
                        </span>
                        <span class="text-white/30">|</span>
                        <span class="text-white/80 text-sm">
                            🕐 {{ \Carbon\Carbon::now()->format('h:i A') }}
                        </span>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 flex flex-wrap gap-3">
                    <a href="{{ route('admin.timetable-reports.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-xl hover:bg-white/30 transition-all duration-200 text-sm font-medium">
                        📅 Timetable
                    </a>
                    <a href="{{ route('admin.students.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-xl hover:bg-white/30 transition-all duration-200 text-sm font-medium">
                        👨‍🎓 Students
                    </a>
                </div>
            </div>
        </div>

        {{-- STATISTICS CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">

            <div class="stat-card stat-card-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Students</p>
                        <p class="stat-number text-gray-900">{{ number_format($totalStudents ?? 0) }}</p>
                        <span class="text-xs text-green-600 flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            {{ $newStudents ?? 0 }} new
                        </span>
                    </div>
                    <div class="stat-icon bg-indigo-50 text-indigo-600 text-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-card-success">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Teachers</p>
                        <p class="stat-number text-gray-900">{{ number_format($totalTeachers ?? 0) }}</p>
                        <span class="text-xs text-green-600 flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            {{ $activeTeachers ?? 0 }} active
                        </span>
                    </div>
                    <div class="stat-icon bg-green-50 text-green-600 text-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-card-warning">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Classes</p>
                        <p class="stat-number text-gray-900">{{ number_format($totalClasses ?? 0) }}</p>
                        <span class="text-xs text-amber-600 flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            {{ $activeClasses ?? 0 }} active
                        </span>
                    </div>
                    <div class="stat-icon bg-amber-50 text-amber-600 text-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-card-danger">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Exams</p>
                        <p class="stat-number text-gray-900">{{ number_format($totalExams ?? 0) }}</p>
                        <span class="text-xs text-red-600 flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            {{ $upcomingExams ?? 0 }} upcoming
                        </span>
                    </div>
                    <div class="stat-icon bg-red-50 text-red-600 text-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-card-info">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Attendance</p>
                        <p class="stat-number text-gray-900">{{ $attendanceRate ?? 0 }}%</p>
                        <span class="text-xs text-blue-600 flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            Today's rate
                        </span>
                    </div>
                    <div class="stat-icon bg-blue-50 text-blue-600 text-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-card-purple">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Revenue</p>
                        <p class="stat-number text-gray-900">${{ number_format($totalRevenue ?? 0) }}</p>
                        <span class="text-xs text-purple-600 flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            ${{ number_format($revenueToday ?? 0) }} today
                        </span>
                    </div>
                    <div class="stat-icon bg-purple-50 text-purple-600 text-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT COLUMN (2/3) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Quick Actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100/80 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Quick Actions
                        </h3>
                        <span class="text-xs text-gray-400">Click to navigate</span>
                    </div>
                    <div class="p-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <a href="{{ route('admin.students.create') }}" class="group flex flex-col items-center justify-center p-4 bg-gray-50 rounded-xl hover:bg-indigo-50 transition-all duration-200">
                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-indigo-700">Add Student</span>
                        </a>
                        <a href="{{ route('admin.staff.create') }}" class="group flex flex-col items-center justify-center p-4 bg-gray-50 rounded-xl hover:bg-green-50 transition-all duration-200">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-600 group-hover:bg-green-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-green-700">Add Teacher</span>
                        </a>
                        <a href="{{ route('admin.exams.create') }}" class="group flex flex-col items-center justify-center p-4 bg-gray-50 rounded-xl hover:bg-purple-50 transition-all duration-200">
                            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 group-hover:bg-purple-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-purple-700">Create Exam</span>
                        </a>
                        <a href="{{ route('admin.class-sections.create') }}" class="group flex flex-col items-center justify-center p-4 bg-gray-50 rounded-xl hover:bg-amber-50 transition-all duration-200">
                            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 group-hover:bg-amber-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-amber-700">Add Class</span>
                        </a>
                    </div>
                </div>

                {{-- Recent Activities --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100/80 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Recent Activity
                        </h3>
                        <span class="text-xs text-gray-400">Last 24 hours</span>
                    </div>
                    <div class="p-6 space-y-1">
                        @forelse($recentActivities ?? [] as $activity)
                        <div class="activity-item flex items-center gap-4">
                            <div class="activity-dot" style="--dot-color: {{ $activity['color'] ?? '#4f46e5' }};"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ $activity['title'] ?? 'Activity' }}</p>
                                <p class="text-xs text-gray-400">{{ $activity['description'] ?? '' }}</p>
                            </div>
                            <span class="text-xs text-gray-400 whitespace-nowrap">{{ $activity['time'] ?? 'Just now' }}</span>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-400">
                            <p>No recent activities</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Attendance Chart --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100/80 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Attendance Overview
                        </h3>
                        <span class="text-xs text-gray-400">This week</span>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-6 mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Today's Attendance</p>
                                <p class="text-2xl font-bold text-indigo-600">{{ $attendanceRate ?? 0 }}%</p>
                            </div>
                            <div class="flex-1">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ min($attendanceRate ?? 0, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-5 gap-2 text-center text-xs">
                            @php
                                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
                                $values = [78, 82, 75, 88, $attendanceRate ?? 0];
                            @endphp
                            @foreach($days as $i => $day)
                            <div>
                                <div class="text-gray-400">{{ $day }}</div>
                                <div class="font-semibold text-gray-700">{{ $values[$i] ?? 0 }}%</div>
                                <div class="mt-1 h-1 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full bg-indigo-500" style="width: {{ $values[$i] ?? 0 }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN (1/3) --}}
            <div class="space-y-6">

                {{-- Today's Overview --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100/80">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Today's Overview
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-600">📚 Classes Today</span>
                            <span class="text-sm font-bold text-gray-800">{{ $classesToday ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-600">✅ Attendance Rate</span>
                            <span class="text-sm font-bold text-green-600">{{ $attendanceRate ?? 0 }}%</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-600">📝 Exams Today</span>
                            <span class="text-sm font-bold text-gray-800">{{ $examsToday ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-600">👨‍🎓 New Students</span>
                            <span class="text-sm font-bold text-indigo-600">{{ $newStudentsToday ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">💰 Today's Revenue</span>
                            <span class="text-sm font-bold text-green-600">${{ number_format($revenueToday ?? 0) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Upcoming Events --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100/80">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Upcoming Events
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @forelse($upcomingEvents ?? [] as $event)
                        <div class="flex items-start gap-3 p-2 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-12 h-12 bg-indigo-50 rounded-xl flex flex-col items-center justify-center text-indigo-600">
                                <span class="text-sm font-bold">{{ \Carbon\Carbon::parse($event['date'])->format('d') }}</span>
                                <span class="text-[10px] uppercase">{{ \Carbon\Carbon::parse($event['date'])->format('M') }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $event['title'] ?? 'Event' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">🕐 {{ $event['time'] ?? '' }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-400">
                            <p class="text-sm">No upcoming events</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl border border-indigo-100/50 p-5">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Quick Links
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.timetable-reports.index') }}" class="flex items-center gap-3 text-sm text-gray-700 hover:text-indigo-700 transition-colors p-2 rounded-lg hover:bg-white/60">
                            <span class="text-xl">📅</span>
                            <span>Timetable</span>
                        </a>
                        <a href="{{ route('admin.reports.student.dashboard') }}" class="flex items-center gap-3 text-sm text-gray-700 hover:text-indigo-700 transition-colors p-2 rounded-lg hover:bg-white/60">
                            <span class="text-xl">📊</span>
                            <span>Student Reports</span>
                        </a>
                        <a href="{{ route('admin.fee-reports.index') }}" class="flex items-center gap-3 text-sm text-gray-700 hover:text-indigo-700 transition-colors p-2 rounded-lg hover:bg-white/60">
                            <span class="text-xl">💰</span>
                            <span>Fee Reports</span>
                        </a>
                        <a href="{{ route('admin.attendance.index') }}" class="flex items-center gap-3 text-sm text-gray-700 hover:text-indigo-700 transition-colors p-2 rounded-lg hover:bg-white/60">
                            <span class="text-xl">📋</span>
                            <span>Attendance</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="text-center text-xs text-gray-400 border-t border-gray-200/60 pt-6 mt-8">
            <p>© {{ date('Y') }} {{ config('app.name', 'School Management System') }}. All rights reserved.</p>
            <p class="mt-1">Built with ❤️ using Laravel</p>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate stat cards on load
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (index * 80));
        });

        // Animate progress bars
        setTimeout(() => {
            document.querySelectorAll('.progress-fill').forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 200);
            });
        }, 300);
    });
</script>
@endpush