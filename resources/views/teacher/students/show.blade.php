@extends('layouts.app')
@section('title', $student->first_name.' '.$student->last_name)
@section('content')
@php $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—'; @endphp
<div class="space-y-6 max-w-5xl">
    @include('teacher.partials.header', ['title' => $student->first_name.' '.$student->last_name, 'subtitle' => 'Admission # '.$student->admission_number.' · Class '.$student->grade_name.'-'.$student->section_name])

    <div class="bg-white rounded-xl border shadow-sm p-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><div class="text-xs uppercase text-gray-500">Roll #</div>{{ $student->roll_number ?: '—' }}</div>
        <div><div class="text-xs uppercase text-gray-500">Gender</div>{{ $student->gender }}</div>
        <div><div class="text-xs uppercase text-gray-500">Date of birth</div>{{ $fmt($student->date_of_birth) }}</div>
        <div><div class="text-xs uppercase text-gray-500">Blood group</div>{{ $student->blood_group ?: '—' }}</div>
        <div><div class="text-xs uppercase text-gray-500">Father</div>{{ $student->father_name }}</div>
        <div><div class="text-xs uppercase text-gray-500">Father phone</div>{{ $student->father_phone ?: '—' }}</div>
        <div><div class="text-xs uppercase text-gray-500">Mother</div>{{ $student->mother_name ?: '—' }}</div>
        <div><div class="text-xs uppercase text-gray-500">Mother phone</div>{{ $student->mother_phone ?: '—' }}</div>
        <div class="col-span-2"><div class="text-xs uppercase text-gray-500">Address</div>{{ $student->address ?: '—' }}</div>
        <div><div class="text-xs uppercase text-gray-500">Status</div>@include('teacher.partials.badge', ['status' => $student->status])</div>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <div class="px-5 py-4 border-b font-semibold text-gray-800">Attendance in my subjects</div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Subject</th><th class="px-4 py-2">Present</th><th class="px-4 py-2">Absent</th><th class="px-4 py-2">Late</th><th class="px-4 py-2">Half</th><th class="px-4 py-2">Leave</th><th class="px-4 py-2">%</th></tr></thead>
            <tbody class="divide-y">
                @foreach($myPairs as $p)
                    @php $a = $attendance[$p->subject_id]; @endphp
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $p->subject_name }}</td>
                        <td class="px-4 py-2">{{ $a['present'] }}</td><td class="px-4 py-2">{{ $a['absent'] }}</td><td class="px-4 py-2">{{ $a['late'] }}</td>
                        <td class="px-4 py-2">{{ $a['half_day'] }}</td><td class="px-4 py-2">{{ $a['leave'] }}</td>
                        <td class="px-4 py-2 font-semibold {{ $a['pct'] >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $a['total'] ? $a['pct'].'%' : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <div class="px-5 py-4 border-b font-semibold text-gray-800">Marks in my subjects</div>
        @if($marks->isEmpty())
            <div class="p-6 text-sm text-gray-500">No marks entered yet.</div>
        @else
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Exam</th><th class="px-4 py-2">Subject</th><th class="px-4 py-2">Marks</th><th class="px-4 py-2">%</th><th class="px-4 py-2">Result</th><th class="px-4 py-2">Published</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($marks as $m)
                        @php $pct = ($m->marks_obtained !== null && $m->max_marks > 0) ? round($m->marks_obtained / $m->max_marks * 100, 1) : null; @endphp
                        <tr>
                            <td class="px-4 py-2 font-medium">{{ $m->exam_name }}</td>
                            <td class="px-4 py-2">{{ $m->subject_name }}</td>
                            <td class="px-4 py-2">{{ $m->marks_obtained ?? 'Absent' }} @if($m->max_marks) / {{ $m->max_marks }} @endif</td>
                            <td class="px-4 py-2">{{ $pct !== null ? $pct.'%' : '—' }}</td>
                            <td class="px-4 py-2">@if($m->marks_obtained !== null && $m->passing_marks !== null) @include('teacher.partials.badge', ['status' => $m->marks_obtained >= $m->passing_marks ? 'pass' : 'fail']) @else — @endif</td>
                            <td class="px-4 py-2">{{ $m->is_published ? 'Yes' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <a href="{{ route('teacher.students.index') }}" class="inline-block text-sm text-indigo-600 hover:underline">← Back to students</a>
</div>
@endsection
