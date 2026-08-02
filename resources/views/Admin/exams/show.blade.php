@extends('layouts.app')
@section('title', 'Exam Details')

@section('content')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
    .animate-fade-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }
    .status-badge {
        transition: all 0.3s ease;
    }
    .status-badge:hover {
        transform: scale(1.05);
    }
    .schedule-card {
        transition: all 0.3s ease;
        border-left: 4px solid #6366f1;
    }
    .schedule-card:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .subject-card {
        transition: all 0.3s ease;
    }
    .subject-card:hover {
        background: #f8fafc;
    }
    .grade-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    .grade-a-plus { background: #dcfce7; color: #16a34a; }
    .grade-a { background: #d1fae5; color: #059669; }
    .grade-b-plus { background: #dbeafe; color: #2563eb; }
    .grade-b { background: #bfdbfe; color: #1d4ed8; }
    .grade-c-plus { background: #fef3c7; color: #d97706; }
    .grade-c { background: #fde68a; color: #b45309; }
    .grade-d { background: #fed7aa; color: #ea580c; }
    .grade-f { background: #fee2e2; color: #dc2626; }
    .grade-na { background: #f3f4f6; color: #6b7280; }
    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-weight: 700;
        font-size: 14px;
    }
    .rank-1 { background: #fef3c7; color: #92400e; }
    .rank-2 { background: #e5e7eb; color: #4b5563; }
    .rank-3 { background: #fde68a; color: #78350f; }
    .rank-other { background: #f3f4f6; color: #6b7280; }
    .progress-bar {
        transition: width 1s ease-in-out;
    }
    .section-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    .table-row-hover {
        transition: all 0.2s ease;
    }
    .table-row-hover:hover {
        background: linear-gradient(90deg, #f8fafc 0%, #ffffff 100%);
        transform: scale(1.001);
    }
    .search-box {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }
    .search-box:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
</style>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        {{-- Header --}}
        <div class="mb-6 animate-fade-up">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                        <a href="{{ route('admin.exams.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Exams
                        </a>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-500">Exam Details</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold text-gray-800">{{ $exam->name }}</h1>
                        @if($exam->is_published)
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                Published
                            </span>
                        @else
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                Draft
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-4 mt-2">
                        <div class="flex items-center gap-1.5 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $exam->classSection->full_name ?? 'N/A' }}
                        </div>
                        <div class="flex items-center gap-1.5 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $exam->examType->name ?? 'N/A' }}
                        </div>
                        <div class="flex items-center gap-1.5 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Group: {{ $exam->examGroup->name ?? 'N/A' }}
                        </div>
                        <div class="flex items-center gap-1.5 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            {{ $exam->start_date->format('d M Y') }} - {{ $exam->end_date->format('d M Y') }}
                        </div>
                        <div class="flex items-center gap-1.5 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Passing: {{ $exam->passing_percentage ?? 40 }}%
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.exams.marks-entry', $exam->id) }}" 
                       class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md hover:shadow-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        Enter Marks
                    </a>
                    <a href="{{ route('admin.exams.edit', $exam->id) }}" 
                       class="inline-flex items-center px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl shadow-md hover:shadow-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    @if($exam->is_published)
                        <a href="{{ route('admin.exams.unpublish', $exam->id) }}" 
                           class="inline-flex items-center px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl shadow-md hover:shadow-lg transition"
                           onclick="return confirm('Unpublish this exam?')">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Unpublish
                        </a>
                    @else
                        <a href="{{ route('admin.exams.publish', $exam->id) }}" 
                           class="inline-flex items-center px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl shadow-md hover:shadow-lg transition"
                           onclick="return confirm('Publish this exam?')">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Publish
                        </a>
                    @endif
                    <a href="{{ route('admin.exams.reports.class-wise', ['exam_id' => $exam->id]) }}" 
                       class="inline-flex items-center px-4 py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded-xl shadow-md hover:shadow-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Report
                    </a>
                    <form method="POST" action="{{ route('admin.exams.destroy', $exam->id) }}" 
                          onsubmit="return confirm('Delete this exam? This will delete all associated data.')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl shadow-md hover:shadow-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 animate-fade-up" style="animation-delay: 0.1s">
            <div class="stat-card rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Students</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalStudents }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Average Score</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $averagePercentage }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $averagePercentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Marks</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($exam->marks->sum('marks_obtained')) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-purple-500 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Subjects</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $subjects->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-yellow-500 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Exam Schedule --}}
        @if($exam->subjectSchedules && $exam->subjectSchedules->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 animate-fade-up" style="animation-delay: 0.15s">
            <div class="px-6 py-4 section-header border-b flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">📅 Exam Schedule</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Subject-wise examination dates and timings</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">{{ $exam->subjectSchedules->count() }} subjects scheduled</span>
                </div>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($exam->subjectSchedules as $schedule)
                    <div class="schedule-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-800 text-lg">{{ $schedule->subject->name }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full">Day {{ $loop->iteration }}</span>
                                    @if($schedule->room)
                                    <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">🏠 {{ $schedule->room }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-xs font-medium text-gray-400">#{{ $loop->iteration }}</span>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <div class="bg-gray-50 rounded-lg p-2 text-center">
                                <p class="text-xs text-gray-400">Date</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $schedule->exam_date->format('d M Y') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2 text-center">
                                <p class="text-xs text-gray-400">Time</p>
                                <p class="text-sm font-semibold text-gray-700">
                                    @if($schedule->start_time && $schedule->end_time)
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - 
                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                    @else
                                        <span class="text-gray-400">TBD</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Subject-wise Statistics --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 animate-fade-up" style="animation-delay: 0.2s">
            <div class="px-6 py-4 section-header border-b flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">📊 Subject-wise Performance</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Detailed analysis per subject</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">{{ $subjects->count() }} subjects</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Score</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Highest</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lowest</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pass %</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subjectStats as $stat)
                        @php
                            $passCount = $stat['count'] > 0 ? round(($stat['average'] / ($stat['max_marks'] ?? 100)) * 100, 1) : 0;
                            $passPercentage = $stat['count'] > 0 ? round(($stat['average'] / ($stat['max_marks'] ?? 100)) * 100, 1) : 0;
                            $statusColor = $passPercentage >= 70 ? 'text-green-600' : ($passPercentage >= 50 ? 'text-yellow-600' : 'text-red-600');
                            $statusBadge = $passPercentage >= 70 ? 'bg-green-100 text-green-800' : ($passPercentage >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            $statusIcon = $passPercentage >= 70 ? '✅' : ($passPercentage >= 50 ? '📊' : '❌');
                            $statusText = $passPercentage >= 70 ? 'Excellent' : ($passPercentage >= 50 ? 'Average' : 'Needs Improvement');
                        @endphp
                        <tr class="table-row-hover">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $stat['subject']->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">Max: {{ $stat['max_marks'] ?? 100 }} | Passing: {{ $stat['passing_marks'] ?? 40 }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">{{ $stat['count'] }}</td>
                            <td class="px-6 py-4 text-center font-medium text-blue-600">{{ $stat['average'] }}</td>
                            <td class="px-6 py-4 text-center text-green-600 font-medium">{{ $stat['max'] }}</td>
                            <td class="px-6 py-4 text-center text-red-600">{{ $stat['min'] }}</td>
                            <td class="px-6 py-4 text-center">{{ $stat['total'] }}</td>
                            <td class="px-6 py-4 text-center font-medium">{{ $passPercentage }}%</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusBadge }}">
                                    {{ $statusIcon }} {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Student Marks Table --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-fade-up" style="animation-delay: 0.25s">
            <div class="px-6 py-4 section-header border-b flex flex-wrap justify-between items-center gap-3">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">👨‍🎓 Student Marks</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Individual student performance</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative">
                        <input type="text" id="searchStudent" placeholder="🔍 Search student..." 
                               class="search-box pl-10 pr-4 py-2 rounded-xl text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 w-48 md:w-64">
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">{{ $students->count() }} students</span>
                        <button onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm transition">
                            🖨️ Print
                        </button>
                        <a href="{{ route('admin.exams.marks-entry', $exam->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-sm transition">
                            ✏️ Edit Marks
                        </a>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="studentMarksTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            @foreach($subjects as $assignment)
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $assignment->subject->name }}
                                <span class="block text-[10px] font-normal text-gray-400">(Max: {{ $subjectMarksConfig[$assignment->subject_id]->max_marks ?? 100 }})</span>
                            </th>
                            @endforeach
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $index => $student)
                        @php
                            $studentMarks = isset($marks) ? $marks->get($student->id, collect()) : collect();
                            $result = $exam->results->where('student_id', $student->id)->first();
                            $totalMarks = $result->total_marks ?? 0;
                            $percentage = $result->percentage ?? 0;
                            $grade = $result->grade ?? 'N/A';
                            $rank = $result->rank_in_class ?? null;
                            $isPass = $percentage >= ($exam->passing_percentage ?? 40);
                            $studentName = $student->first_name . ' ' . $student->last_name;
                        @endphp
                        <tr class="table-row-hover student-row" data-student="{{ strtolower($studentName) }}">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold shadow-md flex-shrink-0">
                                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800 text-sm">{{ $studentName }}</div>
                                        <div class="text-xs text-gray-400">Roll: {{ $student->roll_number ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            @foreach($subjects as $assignment)
                            @php $mark = $studentMarks->where('subject_id', $assignment->subject_id)->first(); @endphp
                            <td class="px-4 py-3 text-center">
                                @if($mark)
                                    <span class="font-medium {{ $mark->marks_obtained >= $mark->passing_marks ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $mark->marks_obtained ?? '-' }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">/{{ $mark->max_marks }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="px-4 py-3 text-center font-medium text-gray-800">{{ $totalMarks }}</td>
                            <td class="px-4 py-3 text-center font-bold {{ $percentage >= ($exam->passing_percentage ?? 40) ? 'text-green-600' : 'text-red-600' }}">
                                {{ $percentage }}%
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $gradeClass = 'grade-na';
                                    if ($grade == 'A+') $gradeClass = 'grade-a-plus';
                                    elseif ($grade == 'A') $gradeClass = 'grade-a';
                                    elseif ($grade == 'B+') $gradeClass = 'grade-b-plus';
                                    elseif ($grade == 'B') $gradeClass = 'grade-b';
                                    elseif ($grade == 'C+') $gradeClass = 'grade-c-plus';
                                    elseif ($grade == 'C') $gradeClass = 'grade-c';
                                    elseif ($grade == 'D') $gradeClass = 'grade-d';
                                    elseif ($grade == 'F') $gradeClass = 'grade-f';
                                @endphp
                                <span class="grade-badge {{ $gradeClass }}">{{ $grade }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($rank)
                                    <span class="rank-badge {{ $rank <= 3 ? 'rank-'.$rank : 'rank-other' }}">
                                        #{{ $rank }}
                                    </span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($isPass)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Pass
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        Fail
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex flex-wrap justify-between items-center gap-3">
                    <div class="text-sm text-gray-500">
                        Showing <span id="visibleCount">{{ $students->count() }}</span> of {{ $students->count() }} students
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <span class="text-gray-600">Pass</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-500"></span>
                            <span class="text-gray-600">Fail</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                            <span class="text-gray-600">Top 3 Rank</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Exam Description --}}
        @if($exam->description)
        <div class="mt-8 bg-white rounded-2xl shadow-lg p-6 animate-fade-up" style="animation-delay: 0.3s">
            <h3 class="font-bold text-gray-800 mb-2">📝 Exam Description</h3>
            <p class="text-gray-600 text-sm leading-relaxed">{{ $exam->description }}</p>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchStudent');
    const rows = document.querySelectorAll('.student-row');
    const visibleCount = document.getElementById('visibleCount');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visible = 0;

            rows.forEach(row => {
                const studentData = row.getAttribute('data-student') || '';
                if (studentData.includes(searchTerm)) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            visibleCount.textContent = visible;
        });
    }

    // Animate progress bars on load
    setTimeout(() => {
        document.querySelectorAll('.progress-bar').forEach(bar => {
            bar.style.width = bar.style.width;
        });
    }, 300);

    // Print functionality
    window.printExam = function() {
        window.print();
    };
});
</script>
@endsection