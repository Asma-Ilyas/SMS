@extends('layouts.app')
@section('title', 'Student Dashboard')
@section('content')
@php $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—'; @endphp
<div class="space-y-6">

    {{-- Welcome --}}
    <div class="rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 shadow-lg">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($s->profile_photo)
                    <img src="{{ asset('storage/'.$s->profile_photo) }}" class="w-16 h-16 rounded-full object-cover border-2 border-white/60" alt="">
                @else
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold">{{ strtoupper(substr($s->first_name,0,1)) }}</div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold">Welcome, {{ $s->first_name }} {{ $s->last_name }}</h1>
                    <p class="text-indigo-100 text-sm">
                        Admission # {{ $s->admission_number }}
                        @if($s->grade_name) · Class {{ $s->grade_name }} @endif
                        @if($s->section_name) – {{ $s->section_name }} @endif
                        @if($s->stream_name) ({{ $s->stream_name }}) @endif
                        @if($s->session_name) · {{ $s->session_name }} @endif
                    </p>
                </div>
            </div>
            <div class="text-sm text-indigo-100">{{ now()->format('l, d M Y') }}</div>
        </div>
    </div>

    @if($s->status === 'Inactive')
        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            Your account is currently <strong>inactive</strong>.
            @if($s->suspension_message) {{ $s->suspension_message }} @endif
            @if($s->suspension_end_date) (until {{ $fmt($s->suspension_end_date) }}) @endif
        </div>
    @endif

    @if(!$s->class_section_id)
        <div class="p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
            You have not been assigned to a class section yet. Timetable, subjects and exams will appear once you are assigned.
        </div>
    @endif

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <a href="{{ route('student.attendance.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div class="text-xs uppercase text-gray-500 font-semibold">Attendance (this month)</div>
            <div class="text-3xl font-bold mt-1 {{ $attendanceMonth['percentage'] >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $attendanceMonth['percentage'] }}%</div>
            <div class="text-xs text-gray-500 mt-1">Overall: {{ $attendanceOverall['percentage'] }}% · {{ $attendanceOverall['absent'] }} absent</div>
        </a>
        <a href="{{ route('student.results.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div class="text-xs uppercase text-gray-500 font-semibold">Latest Result</div>
            @php $lr = $latestResults->first(); @endphp
            <div class="text-3xl font-bold mt-1 text-indigo-600">{{ $lr ? number_format($lr->percentage, 1).'%' : '—' }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $lr ? $lr->exam_name.($lr->grade ? ' · Grade '.$lr->grade : '') : 'No published results' }}</div>
        </a>
        <a href="{{ route('student.fees.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div class="text-xs uppercase text-gray-500 font-semibold">Fees Due</div>
            <div class="text-3xl font-bold mt-1 {{ $feeSummary->total > 0 ? 'text-red-600' : 'text-green-600' }}">Rs. {{ number_format($feeSummary->total, 0) }}</div>
            <div class="text-xs text-gray-500 mt-1">
                {{ $feeSummary->cnt }} unpaid invoice(s)
                @if($overdueCount) · <span class="text-red-600">{{ $overdueCount }} overdue</span> @endif
            </div>
        </a>
        <a href="{{ route('student.exams.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div class="text-xs uppercase text-gray-500 font-semibold">Upcoming Exams</div>
            <div class="text-3xl font-bold mt-1 text-purple-600">{{ $upcomingExams->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $upcomingExams->first() ? 'Next: '.$upcomingExams->first()->name.' ('.$fmt($upcomingExams->first()->start_date).')' : 'None scheduled' }}</div>
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Today's classes --}}
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border">
            <div class="px-5 py-4 border-b flex justify-between items-center">
                <h2 class="font-semibold text-gray-800">Today's Classes</h2>
                <a href="{{ route('student.timetable.index') }}" class="text-sm text-indigo-600 hover:underline">Full timetable →</a>
            </div>
            @if($todayClasses->isEmpty())
                <div class="p-6 text-sm text-gray-500">No classes scheduled for today.</div>
            @else
                <ul class="divide-y">
                    @foreach($todayClasses as $c)
                        <li class="px-5 py-3 flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-800">{{ $c->subject }}</div>
                                <div class="text-xs text-gray-500">{{ $c->teacher }} · Room {{ $c->room_number ?? $c->room }}</div>
                            </div>
                            <div class="text-sm text-gray-600">{{ substr($c->start_time,0,5) }} – {{ substr($c->end_time,0,5) }}</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Facilities --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="font-semibold text-gray-800 mb-2">🚌 Transport</h3>
                @if($transport)
                    <p class="text-sm text-gray-700">{{ $transport->route_name }}</p>
                    <p class="text-xs text-gray-500">Stop: {{ $transport->stop_name ?? '—' }} @if($transport->pickup_time) · Pickup {{ substr($transport->pickup_time,0,5) }} @endif</p>
                @else
                    <p class="text-sm text-gray-500">Not using school transport.</p>
                @endif
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="font-semibold text-gray-800 mb-2">🏠 Hostel</h3>
                @if($hostel)
                    <p class="text-sm text-gray-700">{{ $hostel->hostel_name }}</p>
                    <p class="text-xs text-gray-500">Room {{ $hostel->room_number }} @if($hostel->bed_number) · Bed {{ $hostel->bed_number }} @endif</p>
                @else
                    <p class="text-sm text-gray-500">Not a hostel resident.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Upcoming exams --}}
        <div class="bg-white rounded-xl shadow-sm border">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">Upcoming Exams</div>
            @forelse($upcomingExams as $e)
                <a href="{{ route('student.exams.show', $e->id) }}" class="block px-5 py-3 border-b last:border-0 hover:bg-gray-50">
                    <div class="font-medium text-gray-800">{{ $e->name }} <span class="text-xs text-gray-400">{{ $e->type_name }}</span></div>
                    <div class="text-xs text-gray-500">{{ $fmt($e->start_date) }} → {{ $fmt($e->end_date) }}</div>
                </a>
            @empty
                <div class="p-6 text-sm text-gray-500">No upcoming exams.</div>
            @endforelse
        </div>

        {{-- Latest results --}}
        <div class="bg-white rounded-xl shadow-sm border">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">Recent Results</div>
            @forelse($latestResults as $r)
                <a href="{{ route('student.results.show', $r->exam_id) }}" class="flex items-center justify-between px-5 py-3 border-b last:border-0 hover:bg-gray-50">
                    <div>
                        <div class="font-medium text-gray-800">{{ $r->exam_name }}</div>
                        <div class="text-xs text-gray-500">{{ $fmt($r->end_date) }} @if($r->rank_in_class) · Rank {{ $r->rank_in_class }} @endif</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold {{ $r->percentage >= $r->passing_percentage ? 'text-green-600' : 'text-red-600' }}">{{ number_format($r->percentage,1) }}%</div>
                        <div class="text-xs text-gray-500">{{ $r->grade }}</div>
                    </div>
                </a>
            @empty
                <div class="p-6 text-sm text-gray-500">No published results yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
