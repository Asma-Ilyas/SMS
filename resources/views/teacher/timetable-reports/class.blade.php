@extends('layouts.app')
@section('title', 'Class Timetable')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Class View', 'subtitle' => 'Timetable of every section in a class'])
    <form method="GET" class="bg-white rounded-xl border shadow-sm p-4 flex items-end gap-3">
        <div class="flex-1 min-w-[240px]"><label class="text-xs text-gray-500">Class</label>
            <select name="class_id" required class="block w-full mt-1 rounded-lg border-gray-300 text-sm">
                <option value="">— select —</option>
                @foreach($classes as $c)<option value="{{ $c->id }}" @selected($selected && $selected->id == $c->id)>Class {{ $c->grade_name }} ({{ $c->stream_name }}) · {{ $c->session_name }}</option>@endforeach
            </select></div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Show</button>
    </form>
    @if($selected)
        @forelse($blocks as $b)
            <h3 class="font-semibold text-gray-800">{{ $b['title'] }}</h3>
            @include('teacher.partials.timetable-grid', ['slots' => $slots, 'grid' => $b['grid'], 'days' => $days])
        @empty
            @include('teacher.partials.empty', ['message' => 'This class has no sections.'])
        @endforelse
    @endif
</div>
@endsection
