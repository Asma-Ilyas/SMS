@extends('layouts.app')
@section('title', 'Timetable Dashboard')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Timetable Dashboard', 'subtitle' => $timing ? 'Active timing: '.$timing->session_name : 'No active school timing'])

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach([['Total periods', $stats['entries']], ['Sections scheduled', $stats['sections']], ['Teachers scheduled', $stats['teachers']], ['My periods', $stats['mine']]] as [$l, $v])
            <div class="bg-white rounded-xl border shadow-sm p-5"><div class="text-xs uppercase text-gray-500">{{ $l }}</div><div class="text-3xl font-bold text-indigo-600">{{ $v }}</div></div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('teacher.timetable-reports.class') }}" class="bg-white rounded-xl border shadow-sm p-6 hover:shadow-md transition"><div class="text-2xl">📚</div><div class="font-semibold mt-2">Class View</div><p class="text-xs text-gray-500">All sections of a class</p></a>
        <a href="{{ route('teacher.timetable-reports.section') }}" class="bg-white rounded-xl border shadow-sm p-6 hover:shadow-md transition"><div class="text-2xl">📋</div><div class="font-semibold mt-2">Section View</div><p class="text-xs text-gray-500">One section's weekly timetable</p></a>
        <a href="{{ route('teacher.timetable-reports.teacher') }}" class="bg-white rounded-xl border shadow-sm p-6 hover:shadow-md transition"><div class="text-2xl">👨‍🏫</div><div class="font-semibold mt-2">Teacher View</div><p class="text-xs text-gray-500">Any teacher's timetable</p></a>
    </div>

    @if($lastLog)<p class="text-xs text-gray-400">Timetable last generated: {{ \Carbon\Carbon::parse($lastLog->generated_at)->format('d M Y H:i') }} ({{ $lastLog->total_entries }} entries)</p>@endif
</div>
@endsection
