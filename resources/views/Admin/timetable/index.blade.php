@extends('layouts.app')
@section('title', 'Time Table')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Time Table</h1>
        <a href="{{ route('admin.timetable.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Entry</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade / Stream / Section</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timetables as $tt)
                @php
                    $gradeName = $tt->class->grade->name ?? '?';
                    $streamName = $tt->class->stream->name ?? '';
                    $sectionName = $tt->class->section ?? '?';
                    $combined = trim($gradeName . ' ' . $streamName . ' - ' . $sectionName);
                @endphp
                <tr>
                    <td class="px-6 py-4">{{ $combined }}</td>
                    <td class="px-6 py-4">{{ $tt->day_of_week }}</td>
                    <td class="px-6 py-4">{{ $tt->period_number }}</td>
                    <td class="px-6 py-4">{{ $tt->start_time }}</td>
                    <td class="px-6 py-4">{{ $tt->end_time }}</td>
                    <td class="px-6 py-4">{{ $tt->subject->name ?? $tt->subject_id }}</td>
                    <td class="px-6 py-4">{{ $tt->teacher->name ?? $tt->teacher_id }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.timetable.edit', $tt) }}" class="text-blue-600">Edit</a>
                        <form action="{{ route('admin.timetable.destroy', $tt) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4">No entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection