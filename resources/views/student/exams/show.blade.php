@extends('layouts.app')
@section('title', $exam->name)
@section('content')
@php $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—'; @endphp
<div class="space-y-6">
    @include('student.partials.header', ['title' => $exam->name, 'subtitle' => trim(($exam->type_name ?? '').' '.($exam->group_name ? '· '.$exam->group_name : ''))])

    <div class="bg-white rounded-xl border shadow-sm p-5 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><div class="text-xs uppercase text-gray-500">Starts</div><div class="font-medium">{{ $fmt($exam->start_date) }}</div></div>
        <div><div class="text-xs uppercase text-gray-500">Ends</div><div class="font-medium">{{ $fmt($exam->end_date) }}</div></div>
        <div><div class="text-xs uppercase text-gray-500">Centre</div><div class="font-medium">{{ $exam->exam_center ?: '—' }}</div></div>
        <div><div class="text-xs uppercase text-gray-500">Passing %</div><div class="font-medium">{{ $exam->passing_percentage }}%</div></div>
        @if($exam->description)<div class="col-span-full text-gray-600">{{ $exam->description }}</div>@endif
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <div class="px-5 py-4 border-b font-semibold text-gray-800">Date Sheet</div>
        @if($schedule->isEmpty())
            <div class="p-6 text-sm text-gray-500">Subject schedule has not been published yet.</div>
        @else
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left">
                    <tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Subject</th><th class="px-4 py-3">Time</th><th class="px-4 py-3">Room</th><th class="px-4 py-3">Max</th><th class="px-4 py-3">Pass</th></tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($schedule as $r)
                        <tr>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($r->exam_date)->format('D, d M Y') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $r->subject_name }} <span class="text-xs text-gray-400">{{ $r->subject_code }}</span></td>
                            <td class="px-4 py-3">{{ substr($r->start_time,0,5) }} – {{ substr($r->end_time,0,5) }}</td>
                            <td class="px-4 py-3">{{ $r->room_number ?? $r->room_name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $r->max_marks }}</td>
                            <td class="px-4 py-3">{{ $r->passing_marks }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <a href="{{ route('student.exams.index') }}" class="inline-block text-sm text-indigo-600 hover:underline">← Back to exams</a>
</div>
@endsection
