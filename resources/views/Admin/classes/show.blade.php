{{-- resources/views/admin/classes/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Class Details')

@section('content')
<div class="max-w-5xl mx-auto py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">
            🏫 {{ $class->grade->name ?? 'Class' }}
            @if($class->stream && $class->stream->name !== 'General')
                <span class="text-gray-500 font-normal">— {{ $class->stream->name }}</span>
            @endif
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.classes.edit', $class->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">✏️ Edit</a>
            <a href="{{ route('admin.classes.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">← Back</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- CLASS OVERVIEW --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="font-bold text-lg mb-4">📋 Class Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Grade</p>
                <p class="font-medium">{{ $class->grade->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Stream</p>
                <p class="font-medium">{{ $class->stream->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Elective Track</p>
                <p class="font-medium">{{ $class->electiveTrack->name ?? '— None —' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Academic Session</p>
                <p class="font-medium">{{ $class->academicSession->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Capacity (per section)</p>
                <p class="font-medium">{{ $class->capacity ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Total Sections</p>
                <p class="font-medium">{{ $class->classSections->count() }}</p>
            </div>
        </div>
    </div>

    {{-- CLASS SECTIONS --}}
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">📚 Sections</h3>
            <a href="{{ route('admin.class-sections.create') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">+ Add Section</a>
        </div>

        @if($class->classSections->isEmpty())
            <p class="text-gray-400 text-sm py-6 text-center">No sections created yet for this class.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-2 font-medium text-gray-500 uppercase text-xs">Section</th>
                            <th class="px-4 py-2 font-medium text-gray-500 uppercase text-xs">Capacity</th>
                            <th class="px-4 py-2 font-medium text-gray-500 uppercase text-xs">Students</th>
                            <th class="px-4 py-2 font-medium text-gray-500 uppercase text-xs text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($class->classSections as $section)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $section->section_name }}</td>
                                <td class="px-4 py-3">{{ $section->capacity ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ $section->student_count ?? 0 }} / {{ $section->capacity ?? '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.class-sections.edit', $section->id) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection