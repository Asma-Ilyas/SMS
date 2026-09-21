@extends('layouts.app')
@section('title', 'Teacher Timetable')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Teacher View', 'subtitle' => $selected ? trim($selected->first_name.' '.$selected->last_name) : ''])
    <form method="GET" class="bg-white rounded-xl border shadow-sm p-4 flex items-end gap-3">
        <div class="flex-1 min-w-[240px]"><label class="text-xs text-gray-500">Teacher</label>
            <select name="teacher_id" class="block w-full mt-1 rounded-lg border-gray-300 text-sm">
                @foreach($teachers as $t)<option value="{{ $t->id }}" @selected($selected && $selected->id == $t->id)>{{ $t->first_name }} {{ $t->last_name }} @if($t->id == $me->id) (me) @endif</option>@endforeach
            </select></div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Show</button>
    </form>
    @if(empty($grid)) @include('teacher.partials.empty', ['message' => 'No periods scheduled for this teacher.'])
    @else @include('teacher.partials.timetable-grid', ['slots' => $slots, 'grid' => $grid, 'days' => $days]) @endif
</div>
@endsection
