{{-- resources/views/admin/studentattendance/section-wise-report.blade.php --}}

@extends('layouts.app')

@section('title', 'Section Wise Report')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        📈 Section Wise Report
                    </h1>
                    <p class="text-gray-500 mt-1">Compare attendance across different sections</p>
                </div>
                <a href="{{ route('admin.studentattendance.report') }}" 
                   class="px-5 py-2.5 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-300">
                    ← Back to Reports
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 hover:shadow-2xl transition-all duration-300">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}" 
                           class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate }}" 
                           class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-all duration-300 shadow-md hover:shadow-lg">
                        🔍 Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Overall Statistics -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-xl p-6 mb-6 text-white">
            <h3 class="text-lg font-bold mb-4">📊 Overall Statistics</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                <div class="bg-white/20 rounded-xl p-3 text-center backdrop-blur-sm">
                    <div class="text-2xl font-bold">{{ $overallStats['total_students'] }}</div>
                    <div class="text-xs opacity-80">Total Students</div>
                </div>
                <div class="bg-white/20 rounded-xl p-3 text-center backdrop-blur-sm">
                    <div class="text-2xl font-bold">{{ $overallStats['total_present'] }}</div>
                    <div class="text-xs opacity-80">✅ Present</div>
                </div>
                <div class="bg-white/20 rounded-xl p-3 text-center backdrop-blur-sm">
                    <div class="text-2xl font-bold">{{ $overallStats['total_absent'] }}</div>
                    <div class="text-xs opacity-80">❌ Absent</div>
                </div>
                <div class="bg-white/20 rounded-xl p-3 text-center backdrop-blur-sm">
                    <div class="text-2xl font-bold">{{ $overallStats['total_late'] }}</div>
                    <div class="text-xs opacity-80">⏰ Late</div>
                </div>
                <div class="bg-white/20 rounded-xl p-3 text-center backdrop-blur-sm">
                    <div class="text-2xl font-bold">{{ $overallStats['total_half_day'] }}</div>
                    <div class="text-xs opacity-80">🌓 Half Day</div>
                </div>
                <div class="bg-white/20 rounded-xl p-3 text-center backdrop-blur-sm">
                    <div class="text-2xl font-bold">{{ $overallStats['present_percentage'] ?? 0 }}%</div>
                    <div class="text-xs opacity-80">Attendance %</div>
                </div>
                <div class="bg-white/20 rounded-xl p-3 text-center backdrop-blur-sm">
                    <div class="text-2xl font-bold">{{ $overallStats['total_days'] }}</div>
                    <div class="text-xs opacity-80">Total Days</div>
                </div>
            </div>
        </div>

        <!-- Section Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($sectionStats as $stat)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                    <h3 class="text-lg font-bold text-gray-800">{{ $stat['section']->full_name }}</h3>
                    <p class="text-xs text-gray-500">{{ $stat['total_students'] }} students • {{ $stat['total_days'] }} days</p>
                </div>
                <div class="p-6">
                    <!-- Attendance Percentage -->
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">Attendance</span>
                            <span class="font-bold {{ $stat['avg_attendance'] >= 90 ? 'text-green-600' : ($stat['avg_attendance'] >= 75 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $stat['avg_attendance'] }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 transition-all duration-1000" 
                                 style="width: {{ $stat['avg_attendance'] }}%"></div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-green-50 rounded-lg p-2 border border-green-200">
                            <div class="text-lg font-bold text-green-600">{{ $stat['present'] }}</div>
                            <div class="text-xs text-green-700">Present</div>
                            <div class="text-xs text-gray-400">{{ $stat['present_percentage'] }}%</div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-2 border border-red-200">
                            <div class="text-lg font-bold text-red-600">{{ $stat['absent'] }}</div>
                            <div class="text-xs text-red-700">Absent</div>
                            <div class="text-xs text-gray-400">{{ $stat['absent_percentage'] }}%</div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-2 border border-yellow-200">
                            <div class="text-lg font-bold text-yellow-600">{{ $stat['late'] }}</div>
                            <div class="text-xs text-yellow-700">Late</div>
                            <div class="text-xs text-gray-400">{{ $stat['late_percentage'] }}%</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-2 border border-blue-200">
                            <div class="text-lg font-bold text-blue-600">{{ $stat['half_day'] }}</div>
                            <div class="text-xs text-blue-700">Half Day</div>
                            <div class="text-xs text-gray-400">{{ $stat['half_day_percentage'] }}%</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-2 border border-purple-200">
                            <div class="text-lg font-bold text-purple-600">{{ $stat['leave'] }}</div>
                            <div class="text-xs text-purple-700">Leave</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                            <div class="text-lg font-bold text-gray-600">{{ $stat['holiday'] + $stat['on_duty'] }}</div>
                            <div class="text-xs text-gray-700">Other</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if(empty($sectionStats))
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-4 text-xl font-medium text-gray-900">No Data Available</h3>
            <p class="mt-2 text-gray-500">No attendance records found for the selected period.</p>
        </div>
        @endif
    </div>
</div>
@endsection