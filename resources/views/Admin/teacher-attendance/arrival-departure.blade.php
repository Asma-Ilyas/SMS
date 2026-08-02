@extends('layouts.app')

@section('title', 'Arrival & Departure Report')

@section('content')
<div class="container px-4 py-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span>🕐</span> Arrival & Departure Report
            </h1>
            <p class="text-sm text-slate-500">Track teacher arrival and departure times</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.teacher-attendance.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">
                ← Back
            </a>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('admin.teacher-attendance.arrival-departure') }}" class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-slate-100">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1">Teacher</label>
                <select name="teacher_id" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    <option value="">All Teachers</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-medium">
                🔍 Filter
            </button>
            <a href="{{ route('admin.teacher-attendance.arrival-departure') }}" 
               class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">
                🔄 Reset
            </a>
        </div>
    </form>

    <!-- Report Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Teacher</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Date</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Arrival</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Departure</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Late (min)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $staffId => $records)
                        @php
                            $staff = $records->first()->staff;
                            $rowspan = $records->count();
                        @endphp
                        @foreach($records as $index => $record)
                            <tr class="hover:bg-slate-50 transition-colors">
                                @if($index == 0)
                                    <td class="px-4 py-3" rowspan="{{ $rowspan }}">
                                        <div>
                                            <p class="font-medium text-slate-800">{{ $staff->full_name }}</p>
                                            <p class="text-xs text-slate-400">{{ $staff->employee_id }}</p>
                                        </div>
                                    </td>
                                @endif
                                <td class="px-4 py-3 text-center">{{ $record->date->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-center font-medium text-slate-700">
                                    {{ $record->arrival_time ? \Carbon\Carbon::parse($record->arrival_time)->format('h:i A') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center font-medium text-slate-700">
                                    {{ $record->departure_time ? \Carbon\Carbon::parse($record->departure_time)->format('h:i A') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $record->status_badge }}-100 text-{{ $record->status_badge }}-700">
                                        {{ $record->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ $record->late_minutes ?? 0 }}
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                <span class="text-2xl block mb-2">🕐</span>
                                No attendance records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection