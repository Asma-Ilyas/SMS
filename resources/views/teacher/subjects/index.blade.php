@extends('layouts.app')
@section('title', 'My Subjects')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'My Subjects', 'subtitle' => 'Classes and subjects assigned to you'])

    @if($rows->isEmpty())
        @include('teacher.partials.empty', ['message' => 'No subjects have been assigned to you yet.'])
    @else
        @foreach($bySubject as $subject => $items)
            <div class="bg-white rounded-xl border shadow-sm">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800">{{ $subject }} <span class="text-xs text-gray-400">{{ $items->first()->subject_code }}</span></h2>
                    <span class="text-xs text-gray-500">{{ $items->count() }} class(es) · {{ $items->sum('students') }} students</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Class</th><th class="px-4 py-2">Session</th><th class="px-4 py-2">Students</th><th class="px-4 py-2">Weekly</th><th class="px-4 py-2">Scheduled</th><th></th></tr></thead>
                        <tbody class="divide-y">
                            @foreach($items as $r)
                                <tr>
                                    <td class="px-4 py-2 font-medium">{{ $r->section_label }} @if($r->elective)<span class="text-xs text-purple-600">(elective)</span>@endif</td>
                                    <td class="px-4 py-2 text-gray-500">{{ $r->session_name }}</td>
                                    <td class="px-4 py-2">{{ $r->students }}</td>
                                    <td class="px-4 py-2">{{ $r->weekly ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $r->scheduled }} period(s)</td>
                                    <td class="px-4 py-2 text-right space-x-3">
                                        <a class="text-indigo-600 hover:underline" href="{{ route('teacher.students.index', ['section_id' => $r->section_id]) }}">Students</a>
                                        <a class="text-indigo-600 hover:underline" href="{{ route('teacher.attendance.create', ['pair' => $r->key]) }}">Attendance</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
