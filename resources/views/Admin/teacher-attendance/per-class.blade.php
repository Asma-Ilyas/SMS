@extends('layouts.app')

@section('title', 'Per Class Teacher Attendance')

@section('content')
<div class="container px-4 py-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span>📚</span> Per Class Teacher Attendance
            </h1>
            <p class="text-sm text-slate-500">{{ $teacher->full_name }} - {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.teacher-attendance.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">
                ← Back
            </a>
        </div>
    </div>

    <!-- Class Attendance Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($classAttendance as $item)
            @php
                $statusColor = $item['status'] == 'present' ? 'emerald' : 
                              ($item['status'] == 'absent' ? 'red' : 
                              ($item['status'] == 'late' ? 'amber' : 'gray'));
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800">{{ $item['timetable']->classSection->section_name ?? 'N/A' }}</h3>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">
                        {{ ucfirst($item['status']) }}
                    </span>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Subject</span>
                        <span class="font-medium text-slate-700">{{ $item['timetable']->subject->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Period</span>
                        <span class="font-medium text-slate-700">{{ $item['timetable']->timeSlot->label ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Class</span>
                        <span class="font-medium text-slate-700">{{ $item['timetable']->classSection->class->grade->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Arrival</span>
                        <span class="font-medium text-slate-700">{{ $item['arrival_time'] ? \Carbon\Carbon::parse($item['arrival_time'])->format('h:i A') : 'Not marked' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Departure</span>
                        <span class="font-medium text-slate-700">{{ $item['departure_time'] ? \Carbon\Carbon::parse($item['departure_time'])->format('h:i A') : 'Not marked' }}</span>
                    </div>
                </div>

                @if($item['status'] == 'not_marked')
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <a href="{{ route('admin.teacher-attendance.index') }}" 
                           class="block text-center px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition text-sm font-medium">
                            ✏️ Mark Attendance
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-slate-400">
                <span class="text-3xl block mb-2">📚</span>
                <p>No classes found for this teacher on {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection