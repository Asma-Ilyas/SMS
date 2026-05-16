@extends('layouts.app')
@section('title', 'Student Attendance')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Mark Student Attendance</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($assignments as $assignment)
            <div class="border rounded-lg p-4 shadow hover:shadow-md">
                <h3 class="text-lg font-semibold">{{ $assignment->subject->name ?? 'Subject' }}</h3>
                <p>Class: {{ $assignment->class_display ?? 'N/A' }}</p>
                <a href="{{ route('admin.studentattendance.create', ['class_id' => $assignment->class_id]) }}" 
                   class="inline-block mt-3 bg-blue-600 text-white px-3 py-1 rounded text-sm">
                    Mark Attendance
                </a>
            </div>
        @empty
            <p class="text-gray-500">You are not assigned to any subject yet.</p>
        @endforelse
    </div>
</div>
@endsection