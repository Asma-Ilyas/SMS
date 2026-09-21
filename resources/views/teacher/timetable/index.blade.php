@extends('layouts.app')
@section('title', 'My Timetable')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'My Timetable', 'subtitle' => $rows->count() . ' period(s) per week'])

    @if($slots->isEmpty() || $rows->isEmpty())
        @include('teacher.partials.empty', ['message' => 'No timetable has been generated for you yet.'])
    @else
        <div class="flex flex-wrap gap-3 text-xs">
            @foreach($days as $d)
                <span class="px-3 py-1 rounded-full {{ $d === $today ? 'bg-indigo-600 text-white' : 'bg-white border text-gray-600' }}">{{ ucfirst($d) }}: {{ $perDay[$d] }}</span>
            @endforeach
        </div>
        @include('teacher.partials.timetable-grid', ['slots' => $slots, 'grid' => $grid, 'days' => $days, 'todayName' => $today])
    @endif
</div>
@endsection
