@extends('layouts.app')

@section('title', 'Class Wise Teacher Attendance')

@section('content')
<div class="container px-4 py-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span>📊</span> Class Wise Teacher Attendance
            </h1>
            <p class="text-sm text-slate-500">{{ $classSection->full_name ?? $classSection->section_name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.teacher-attendance.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">
                ← Back
            </a>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('admin.teacher-attendance.class-wise') }}" class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-slate-100">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1">Class Section</label>
                <select name="class_section_id" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    @foreach(\App\Models\ClassSection::with('class.grade')->get() as $section)
                        <option value="{{ $section->id }}" {{ $classSection->id == $section->id ? 'selected' : '' }}>
                            {{ $section->full_name ?? $section->section_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1">From Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1">To Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-medium">
                🔍 Filter
            </button>
            <a href="{{ route('admin.teacher-attendance.class-wise', ['class_section_id' => $classSection->id]) }}" 
               class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">
                🔄 Reset
            </a>
        </div>
    </form>

    <!-- Weekly Report -->
    @foreach($weeklyReport as $day)
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-6">
            <div class="px-6 py-3 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-700">{{ $day['day'] }}</h3>
                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($day['date'])->format('d M Y') }}</p>
                </div>
                <span class="text-xs bg-white px-3 py-1 rounded-full border border-slate-200">
                    {{ count($day['periods']) }} periods
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Period</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Subject</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Teacher</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-slate-600">Status</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-slate-600">Arrival</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-slate-600">Departure</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($day['periods'] as $period)
                            @php
                                $statusColor = $period['status'] == 'present' ? 'text-emerald-600' : 
                                              ($period['status'] == 'absent' ? 'text-red-600' : 
                                              ($period['status'] == 'late' ? 'text-amber-600' : 'text-slate-400'));
                                $statusBg = $period['status'] == 'present' ? 'bg-emerald-50' : 
                                           ($period['status'] == 'absent' ? 'bg-red-50' : 
                                           ($period['status'] == 'late' ? 'bg-amber-50' : 'bg-slate-50'));
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-2">{{ $period['time'] }}</td>
                                <td class="px-4 py-2 font-medium text-slate-800">{{ $period['subject'] }}</td>
                                <td class="px-4 py-2">{{ $period['teacher'] }}</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBg }} {{ $statusColor }}">
                                        {{ ucfirst($period['status']) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    {{ $period['arrival_time'] ? \Carbon\Carbon::parse($period['arrival_time'])->format('h:i A') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-center">
                                    {{ $period['departure_time'] ? \Carbon\Carbon::parse($period['departure_time'])->format('h:i A') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400">No periods found for this day</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection