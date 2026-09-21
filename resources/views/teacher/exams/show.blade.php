@extends('layouts.app')
@section('title', $e->name)
@section('content')
@php $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—'; @endphp
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => $e->name, 'subtitle' => $section])

    <div class="bg-white rounded-xl border shadow-sm p-5 grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
        <div><div class="text-xs uppercase text-gray-500">Starts</div>{{ $fmt($e->start_date) }}</div>
        <div><div class="text-xs uppercase text-gray-500">Ends</div>{{ $fmt($e->end_date) }}</div>
        <div><div class="text-xs uppercase text-gray-500">Centre</div>{{ $e->exam_center ?: '—' }}</div>
        <div><div class="text-xs uppercase text-gray-500">Passing %</div>{{ $e->passing_percentage }}%</div>
        <div><div class="text-xs uppercase text-gray-500">Status</div>@include('teacher.partials.badge', ['status' => $state]) @if($e->is_published) @include('teacher.partials.badge', ['status' => 'published']) @endif</div>
        @if($e->description)<div class="col-span-full text-gray-600">{{ $e->description }}</div>@endif
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <div class="px-5 py-4 border-b font-semibold text-gray-800">Date Sheet</div>
        @if($schedule->isEmpty())
            <div class="p-6 text-sm text-gray-500">No subject schedule has been published.</div>
        @else
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Date</th><th class="px-4 py-2">Subject</th><th class="px-4 py-2">Time</th><th class="px-4 py-2">Room</th><th class="px-4 py-2">Max</th><th class="px-4 py-2">Pass</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($schedule as $r)
                        <tr class="{{ in_array($r->subject_id, $mySubjectIds) ? 'bg-indigo-50/50' : '' }}">
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($r->exam_date)->format('D, d M Y') }}</td>
                            <td class="px-4 py-2 font-medium">{{ $r->subject_name }} @if(in_array($r->subject_id, $mySubjectIds))<span class="text-xs text-indigo-600">(mine)</span>@endif</td>
                            <td class="px-4 py-2">{{ substr($r->start_time,0,5) }} – {{ substr($r->end_time,0,5) }}</td>
                            <td class="px-4 py-2">{{ $r->room_number ?? $r->room_name ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $r->max_marks }}</td><td class="px-4 py-2">{{ $r->passing_marks }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="bg-white rounded-xl border shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-3">My marks entry</h3>
        @foreach($tasks as $x)
            <div class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                <span>{{ $x->pair->subject_name }} — {{ $x->entered }}/{{ $x->total }} entered</span>
                <span class="space-x-3">
                    <a class="text-indigo-600 hover:underline" href="{{ route('teacher.exam-reports.index', ['task' => $x->key]) }}">Report</a>
                    @if(!$x->locked)<a class="text-indigo-600 hover:underline" href="{{ route('teacher.marks.create', ['task' => $x->key]) }}">Enter marks</a>@else<span class="text-gray-400">Locked</span>@endif
                </span>
            </div>
        @endforeach
    </div>
    <a href="{{ route('teacher.exams.index') }}" class="inline-block text-sm text-indigo-600 hover:underline">← Back to exams</a>
</div>
@endsection
