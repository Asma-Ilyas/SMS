@extends('layouts.app')
@section('title', 'School Timings')
@section('content')
@php $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—'; $tm = fn($x) => $x ? substr($x,0,5) : '—'; @endphp
<div class="space-y-6 max-w-4xl">
    @include('teacher.partials.header', ['title' => 'School Timings', 'subtitle' => 'Active timing schedule'])

    @if(!$timing)
        @include('teacher.partials.empty', ['message' => 'No active school timing has been configured.'])
    @else
        <div class="bg-white rounded-xl border shadow-sm p-6">
            <h2 class="font-semibold text-gray-800 mb-4">{{ $timing->session_name }}</h2>
            <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div><dt class="text-xs uppercase text-gray-500">Session</dt><dd>{{ $fmt($timing->session_start) }} → {{ $fmt($timing->session_end) }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">School hours</dt><dd>{{ $tm($timing->school_start) }} – {{ $tm($timing->school_end) }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Period duration</dt><dd>{{ $timing->period_duration }} min</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Activity</dt><dd>{{ $timing->has_activity ? ($timing->activity_label.' '.$tm($timing->activity_start).'–'.$tm($timing->activity_end)) : 'None' }}</dd></div>
            </dl>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">Daily Schedule</div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-3">#</th><th class="px-4 py-3">Slot</th><th class="px-4 py-3">Start</th><th class="px-4 py-3">End</th><th class="px-4 py-3">Type</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($slots as $s)
                        <tr class="{{ $s->type !== 'period' ? 'bg-gray-50' : '' }}">
                            <td class="px-4 py-2 text-gray-500">{{ $s->sort_order }}</td>
                            <td class="px-4 py-2 font-medium text-gray-800">{{ $s->label }}</td>
                            <td class="px-4 py-2">{{ $tm($s->start_time) }}</td>
                            <td class="px-4 py-2">{{ $tm($s->end_time) }}</td>
                            <td class="px-4 py-2">{{ ucfirst($s->type) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
