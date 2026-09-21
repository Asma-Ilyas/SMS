@extends('layouts.app')
@section('title', 'Teacher Dashboard')
@section('content')
@php $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—'; @endphp
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Welcome, ' . $t->first_name . ' ' . $t->last_name, 'subtitle' => trim(($t->designation ?: 'Teacher') . ($t->department ? ' · ' . $t->department : '')) . ' · ' . now()->format('l, d M Y')])

    {{-- Stats --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <a href="{{ route('teacher.subjects.index') }}" class="bg-white rounded-xl border shadow-sm p-5 hover:shadow-md transition">
            <div class="text-xs uppercase text-gray-500 font-semibold">Classes / Subjects</div>
            <div class="text-3xl font-bold text-indigo-600 mt-1">{{ $pairs->unique('section_id')->count() }} <span class="text-lg text-gray-400">/ {{ $pairs->unique('subject_id')->count() }}</span></div>
        </a>
        <a href="{{ route('teacher.students.index') }}" class="bg-white rounded-xl border shadow-sm p-5 hover:shadow-md transition">
            <div class="text-xs uppercase text-gray-500 font-semibold">My Students</div>
            <div class="text-3xl font-bold text-purple-600 mt-1">{{ $studentCount }}</div>
        </a>
        <a href="{{ route('teacher.timetable') }}" class="bg-white rounded-xl border shadow-sm p-5 hover:shadow-md transition">
            <div class="text-xs uppercase text-gray-500 font-semibold">Periods Today</div>
            <div class="text-3xl font-bold text-blue-600 mt-1">{{ $todayClasses->count() }}</div>
        </a>
        <a href="{{ route('teacher.leaves.index') }}" class="bg-white rounded-xl border shadow-sm p-5 hover:shadow-md transition">
            <div class="text-xs uppercase text-gray-500 font-semibold">Pending Leaves</div>
            <div class="text-3xl font-bold text-yellow-600 mt-1">{{ $pendingLeaves }}</div>
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Today's classes --}}
        <div class="xl:col-span-2 bg-white rounded-xl border shadow-sm">
            <div class="px-5 py-4 border-b flex justify-between items-center">
                <h2 class="font-semibold text-gray-800">Today's Classes</h2>
                <a href="{{ route('teacher.timetable') }}" class="text-sm text-indigo-600 hover:underline">Full timetable →</a>
            </div>
            @forelse($todayClasses as $c)
                <div class="px-5 py-3 border-b last:border-0 flex items-center justify-between">
                    <div>
                        <div class="font-medium text-gray-800">{{ $c->subject }}</div>
                        <div class="text-xs text-gray-500">Class {{ $c->grade_name }}-{{ $c->section_name }} · Room {{ $c->room_number ?? $c->room }}</div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">{{ substr($c->start_time,0,5) }} – {{ substr($c->end_time,0,5) }}</span>
                        <a href="{{ route('teacher.attendance.create', ['pair' => $c->class_section_id.'|'.$c->subject_id]) }}" class="text-xs px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">Attendance</a>
                    </div>
                </div>
            @empty
                <div class="p-6 text-sm text-gray-500">No classes scheduled for today.</div>
            @endforelse
        </div>

        {{-- My attendance --}}
        <div class="bg-white rounded-xl border shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">My Attendance Today</h3>
            @if($myAttendance && $myAttendance->check_in)
                <div class="text-sm text-gray-700">Checked in: <strong>{{ substr($myAttendance->check_in,0,5) }}</strong> @include('teacher.partials.badge', ['status' => $myAttendance->status])</div>
                <div class="text-sm text-gray-700 mt-1">Checked out: <strong>{{ $myAttendance->check_out ? substr($myAttendance->check_out,0,5) : '—' }}</strong></div>
            @else
                <p class="text-sm text-gray-500">You have not checked in yet.</p>
            @endif
            <a href="{{ route('teacher.attendance.check') }}" class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Check In / Out</a>
            <div class="mt-5 pt-4 border-t text-xs text-gray-500">
                This month: {{ $monthRows['present'] ?? 0 }} present · {{ $monthRows['late'] ?? 0 }} late · {{ $monthRows['absent'] ?? 0 }} absent
            </div>
            @if($lastSalary)
                <div class="mt-3 text-xs text-gray-500">Latest salary ({{ $lastSalary->month }}): <strong>Rs. {{ number_format($lastSalary->net_salary,0) }}</strong> @include('teacher.partials.badge', ['status' => $lastSalary->payment_status])</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border shadow-sm">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">⏳ Attendance Not Marked Today</div>
            @forelse($pendingAttendance as $c)
                <a href="{{ route('teacher.attendance.create', ['pair' => $c->class_section_id.'|'.$c->subject_id]) }}" class="block px-5 py-3 border-b last:border-0 hover:bg-gray-50 text-sm">
                    <div class="font-medium text-gray-800">{{ $c->subject }}</div>
                    <div class="text-xs text-gray-500">Class {{ $c->grade_name }}-{{ $c->section_name }}</div>
                </a>
            @empty
                <div class="p-5 text-sm text-gray-500">All done — nothing pending. ✅</div>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border shadow-sm">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">✏️ Marks Entry Pending</div>
            @forelse($marksPending as $x)
                <a href="{{ route('teacher.marks.create', ['task' => $x->key]) }}" class="block px-5 py-3 border-b last:border-0 hover:bg-gray-50 text-sm">
                    <div class="font-medium text-gray-800">{{ $x->exam->name }} · {{ $x->pair->subject_name }}</div>
                    <div class="text-xs text-gray-500">{{ $x->pair->section_label }} — {{ $x->entered }}/{{ $x->total }} entered</div>
                </a>
            @empty
                <div class="p-5 text-sm text-gray-500">No pending marks entry. ✅</div>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border shadow-sm">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">📅 Upcoming Exams</div>
            @forelse($upcomingExams as $e)
                <a href="{{ route('teacher.exams.show', $e->id) }}" class="block px-5 py-3 border-b last:border-0 hover:bg-gray-50 text-sm">
                    <div class="font-medium text-gray-800">{{ $e->name }}</div>
                    <div class="text-xs text-gray-500">Class {{ $e->grade_name }}-{{ $e->section_name }} · {{ $fmt($e->start_date) }} → {{ $fmt($e->end_date) }}</div>
                </a>
            @empty
                <div class="p-5 text-sm text-gray-500">No upcoming exams.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
