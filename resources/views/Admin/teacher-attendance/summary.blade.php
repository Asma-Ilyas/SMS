@extends('layouts.app')

@section('title', 'Teacher Attendance Summary')

@section('content')
<div class="container px-4 py-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span>📈</span> Teacher Attendance Summary
            </h1>
            <p class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.teacher-attendance.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">
                ← Back
            </a>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('admin.teacher-attendance.summary') }}" class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-slate-100">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1">Month</label>
                <input type="month" name="month" value="{{ $month }}" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-medium">
                🔍 Filter
            </button>
            <a href="{{ route('admin.teacher-attendance.summary') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">
                🔄 Reset
            </a>
        </div>
    </form>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100">
            <p class="text-xs text-slate-400">Total Teachers</p>
            <p class="text-2xl font-bold text-slate-800">{{ count($summary) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100">
            <p class="text-xs text-slate-400">Avg Attendance</p>
            <p class="text-2xl font-bold text-emerald-600">{{ count($summary) > 0 ? round(collect($summary)->avg('percentage'), 1) : 0 }}%</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100">
            <p class="text-xs text-slate-400">Total Classes Taken</p>
            <p class="text-2xl font-bold text-indigo-600">{{ collect($summary)->sum('classes_taken') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100">
            <p class="text-xs text-slate-400">Present Days</p>
            <p class="text-2xl font-bold text-emerald-600">{{ collect($summary)->sum('present') }}</p>
        </div>
    </div>

    <!-- Summary Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Teacher</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Total Days</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Present</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Absent</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Late</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Leave</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Classes Taken</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Attendance %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($summary as $data)
                        @php
                            $percentageColor = $data['percentage'] >= 90 ? 'text-emerald-600' : ($data['percentage'] >= 75 ? 'text-amber-600' : 'text-red-600');
                            $percentageBg = $data['percentage'] >= 90 ? 'bg-emerald-50' : ($data['percentage'] >= 75 ? 'bg-amber-50' : 'bg-red-50');
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $data['teacher']->full_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $data['teacher']->employee_id }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">{{ $data['total_days'] }}</td>
                            <td class="px-4 py-3 text-center text-emerald-600 font-medium">{{ $data['present'] }}</td>
                            <td class="px-4 py-3 text-center text-red-600 font-medium">{{ $data['absent'] }}</td>
                            <td class="px-4 py-3 text-center text-amber-600 font-medium">{{ $data['late'] }}</td>
                            <td class="px-4 py-3 text-center text-purple-600 font-medium">{{ $data['leave'] }}</td>
                            <td class="px-4 py-3 text-center text-indigo-600 font-medium">{{ $data['classes_taken'] }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $percentageBg }} {{ $percentageColor }}">
                                    {{ number_format($data['percentage'], 1) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                <span class="text-2xl block mb-2">📈</span>
                                No attendance data found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection