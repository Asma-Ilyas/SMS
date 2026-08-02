@extends('layouts.app')
@section('title', 'Add Class Section')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Add Class Section</h1>
    <form method="POST" action="{{ route('admin.class-sections.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium">Class *</label>
            <select name="class_id" class="mt-1 block w-full border rounded-md px-3 py-2" required>
                <option value="">Select Class</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->full_name }}</option>
                @endforeach
            </select>
            @error('class_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Section Name *</label>
            <input type="text" name="section_name" class="mt-1 block w-full border rounded-md px-3 py-2" value="{{ old('section_name') }}" required>
            @error('section_name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Capacity (max students)</label>
            <input type="number" name="capacity" class="mt-1 block w-full border rounded-md px-3 py-2" value="{{ old('capacity') }}">
            @error('capacity') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Student Strength (manual override)</label>
            <input type="number" name="student_strength" class="mt-1 block w-full border rounded-md px-3 py-2" value="{{ old('student_strength') }}">
            @error('student_strength') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Save</button>
        <a href="{{ route('admin.class-sections.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg ml-2">Cancel</a>
    </form>
</div>
@endsection