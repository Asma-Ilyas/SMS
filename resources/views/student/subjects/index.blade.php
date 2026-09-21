@extends('layouts.app')
@section('title', 'My Subjects')
@section('content')
<div class="space-y-6">
    @include('student.partials.header', ['title' => 'My Subjects', 'subtitle' => ($s->grade_name ? 'Class '.$s->grade_name.' – '.$s->section_name : '')])

    @if($subjects->isEmpty())
        @include('student.partials.empty', ['message' => 'No subjects have been assigned to your section yet.'])
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($subjects as $sub)
                <div class="bg-white rounded-xl border shadow-sm p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $sub->name }}</h3>
                            <p class="text-xs text-gray-400">{{ $sub->code }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">{{ ucfirst($sub->type) }}@if($sub->is_elective) · Elective @endif</span>
                    </div>
                    @if($sub->description)<p class="text-sm text-gray-500 mt-2">{{ $sub->description }}</p>@endif
                    <div class="mt-3 text-sm text-gray-700">👨‍🏫 {{ trim($sub->teacher_name) ?: 'Teacher not assigned' }}</div>
                    @if($sub->teacher_email)<div class="text-xs text-gray-400">{{ $sub->teacher_email }}</div>@endif
                    <div class="mt-2 text-xs text-gray-500">{{ $sub->weekly_frequency }} period(s) / week</div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
