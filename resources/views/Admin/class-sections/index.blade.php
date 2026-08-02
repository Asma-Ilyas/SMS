@extends('layouts.app')
@section('title', 'Class Sections')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">📚 Class Sections</h1>
        <a href="{{ route('admin.class-sections.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">+ Add Section</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Strength</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrolled</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sections as $section)
                <tr>
                    <td class="px-6 py-4">{{ $section->class->full_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $section->section_name }}</td>
                    <td class="px-6 py-4">{{ $section->capacity ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $section->student_strength ?? $section->student_count ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $section->student_count ?? 0 }}</td>
                    <td class="px-6 py-4 space-x-2">
                        <a href="{{ route('admin.class-sections.edit', $section) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.class-sections.destroy', $section) }}" method="POST" class="inline" onsubmit="return confirm('Delete this section?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">No sections found. <a href="{{ route('admin.class-sections.create') }}" class="text-blue-600">Create one</a>.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $sections->links() }}
    </div>
</div>
@endsection