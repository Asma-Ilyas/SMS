@extends('layouts.app')

@section('title', 'No Active Session')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto text-center">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-yellow-800">No Active School Session</h3>
            <p class="mt-2 text-yellow-700">Please activate a session from the <a href="{{ route('admin.school-timings.index') }}" class="underline">School Sessions</a> page to view the timetable.</p>
            <div class="mt-4">
                <a href="{{ route('admin.school-timings.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    + Create Session
                </a>
            </div>
        </div>
    </div>
</div>
@endsection