@extends('layouts.app')
@section('title', 'Subject Distribution (Section-wise)')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Subject Distribution (Grade + Stream + Section)</h1>
        <a href="{{ route('admin.class-subject.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Assign Subject</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Class (Grade / Stream / Section)</th>
                    <th class="px-6 py-3 text-left">Subject</th>
                    <th class="px-6 py-3 text-left">Teacher</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                <tr>
                    <td class="px-6 py-4">{{ $assignment->class_display }}</td>
                    <td class="px-6 py-4">{{ $assignment->subject->name ?? $assignment->subject_id }}</td>
                    <td class="px-6 py-4">{{ $assignment->teacher->name ?? $assignment->teacher_id }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.class-subject.edit', $assignment) }}" class="text-blue-600">Edit</a>
                        <form action="{{ route('admin.class-subject.destroy', $assignment) }}" method="POST" class="inline" onsubmit="return confirm('Remove this assignment?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No subject assignments found. <a href="{{ route('admin.class-subject.create') }}" class="text-blue-600">Assign now</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection