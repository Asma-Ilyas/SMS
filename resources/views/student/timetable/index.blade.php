@extends('layouts.app')
@section('title', 'My Timetable')
@section('content')
<div class="space-y-6">
    @include('student.partials.header', ['title' => 'My Timetable', 'subtitle' => ($s->grade_name ? 'Class '.$s->grade_name.' – '.$s->section_name : 'No class assigned')])

    @if($slots->isEmpty())
        @include('student.partials.empty', ['message' => 'No timetable has been published for your section yet.'])
    @else
        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600 w-40">Time</th>
                        @foreach($days as $d)
                            <th class="px-4 py-3 text-left {{ $d === $todayName ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600' }}">{{ ucfirst($d) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($slots as $slot)
                        @if($slot->type !== 'period')
                            <tr class="bg-gray-50">
                                <td class="px-4 py-2 text-xs text-gray-500">{{ substr($slot->start_time,0,5) }} – {{ substr($slot->end_time,0,5) }}</td>
                                <td colspan="{{ count($days) }}" class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $slot->label }}</td>
                            </tr>
                        @else
                            <tr>
                                <td class="px-4 py-3 align-top">
                                    <div class="font-medium text-gray-800">{{ $slot->label }}</div>
                                    <div class="text-xs text-gray-500">{{ substr($slot->start_time,0,5) }} – {{ substr($slot->end_time,0,5) }}</div>
                                </td>
                                @foreach($days as $d)
                                    @php $cell = $grid[$d][$slot->id] ?? null; @endphp
                                    <td class="px-4 py-3 align-top {{ $d === $todayName ? 'bg-indigo-50/40' : '' }}">
                                        @if($cell)
                                            <div class="font-medium text-gray-800">{{ $cell->subject }}</div>
                                            <div class="text-xs text-gray-500">{{ $cell->teacher }}</div>
                                            <div class="text-xs text-gray-400">Room {{ $cell->room_number ?? $cell->room }}</div>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
