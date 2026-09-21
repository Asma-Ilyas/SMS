@extends('layouts.app')
@section('title', 'Section Timetable')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Section View', 'subtitle' => 'Weekly timetable of a section'])
    <form method="GET" class="bg-white rounded-xl border shadow-sm p-4 flex items-end gap-3">
        <div class="flex-1 min-w-[240px]"><label class="text-xs text-gray-500">Section</label>
            <select name="section_id" required class="block w-full mt-1 rounded-lg border-gray-300 text-sm">
                <option value="">— select —</option>
                @foreach($sections as $s)<option value="{{ $s->id }}" @selected($selected && $selected->id == $s->id)>Class {{ $s->grade_name }}-{{ $s->section_name }} ({{ $s->stream_name }}) · {{ $s->session_name }}</option>@endforeach
            </select></div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Show</button>
    </form>
    @if($selected)
        @if(empty($grid)) @include('teacher.partials.empty', ['message' => 'No timetable generated for this section.'])
        @else @include('teacher.partials.timetable-grid', ['slots' => $slots, 'grid' => $grid, 'days' => $days]) @endif
    @endif
</div>
@endsection
