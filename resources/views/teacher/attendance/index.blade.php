@extends('layouts.app')
@section('title', 'Attendance Dashboard')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Attendance Dashboard', 'subtitle' => 'Student attendance — ' . $today->format('l, d M Y')])

    <div class="flex gap-3">
        <a href="{{ route('teacher.attendance.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">+ Mark Attendance</a>
        <a href="{{ route('teacher.attendance.report') }}" class="px-4 py-2 bg-white border rounded-lg text-sm hover:bg-gray-50">📈 Reports</a>
    </div>

    @if($rows->isEmpty())
        @include('teacher.partials.empty', ['message' => 'You have no assigned classes.'])
    @else
        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">My Classes — Today</div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Class · Subject</th><th class="px-4 py-2">Students</th><th class="px-4 py-2">Marked today</th><th class="px-4 py-2">Status</th><th></th></tr></thead>
                <tbody class="divide-y">
                    @foreach($rows as $r)
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-800">{{ $r->label }}</td>
                            <td class="px-4 py-2">{{ $r->total }}</td>
                            <td class="px-4 py-2">{{ $r->marked }}</td>
                            <td class="px-4 py-2">@include('teacher.partials.badge', ['status' => $r->marked > 0 ? 'completed' : 'pending'])</td>
                            <td class="px-4 py-2 text-right"><a class="text-indigo-600 hover:underline" href="{{ route('teacher.attendance.create', ['pair' => $r->key]) }}">{{ $r->marked > 0 ? 'Edit' : 'Mark' }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <div class="px-5 py-4 border-b font-semibold text-gray-800">Recently marked</div>
        @if($recent->isEmpty())
            <div class="p-6 text-sm text-gray-500">Nothing marked yet.</div>
        @else
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Date</th><th class="px-4 py-2">Class</th><th class="px-4 py-2">Subject</th><th class="px-4 py-2">Present</th><th class="px-4 py-2">Absent</th><th class="px-4 py-2">Late</th><th></th></tr></thead>
                <tbody class="divide-y">
                    @foreach($recent as $r)
                        <tr>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($r->date)->format('D, d M') }}</td>
                            <td class="px-4 py-2">{{ $r->grade_name }}-{{ $r->section_name }}</td>
                            <td class="px-4 py-2">{{ $r->subject_name }}</td>
                            <td class="px-4 py-2 text-green-600">{{ (int) $r->present }}/{{ $r->total }}</td>
                            <td class="px-4 py-2 text-red-600">{{ (int) $r->absent }}</td>
                            <td class="px-4 py-2 text-yellow-600">{{ (int) $r->late }}</td>
                            <td class="px-4 py-2 text-right"><a class="text-indigo-600 hover:underline" href="{{ route('teacher.attendance.create', ['pair' => $r->class_section_id.'|'.$r->subject_id, 'date' => \Carbon\Carbon::parse($r->date)->toDateString()]) }}">Edit</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
