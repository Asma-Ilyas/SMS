@extends('layouts.app')

@section('title', 'Edit Subject')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-teal-700 px-6 py-5">
            <h2 class="text-2xl font-bold text-white">Edit Subject</h2>
            <p class="text-green-100 text-sm">Update subject details</p>
        </div>
        <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="px-6 py-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-semibold">Subject Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $subject->name) }}" class="w-full rounded-lg border-gray-300" required>
            </div>
            <div>
                <label class="block font-semibold">Subject Code <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code', $subject->code) }}" class="w-full rounded-lg border-gray-300" required>
            </div>
            <div>
                <label class="block font-semibold">Description</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300">{{ old('description', $subject->description) }}</textarea>
            </div>
            <div>
                <label class="block font-semibold">Type <span class="text-red-500">*</span></label>
                <select name="type" class="w-full rounded-lg border-gray-300">
                    <option value="theory" {{ old('type', $subject->type) == 'theory' ? 'selected' : '' }}>Theory</option>
                    <option value="practical" {{ old('type', $subject->type) == 'practical' ? 'selected' : '' }}>Practical</option>
                    <option value="elective" {{ old('type', $subject->type) == 'elective' ? 'selected' : '' }}>Elective</option>
                </select>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
                <span class="ml-2">Active</span>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.subjects.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Update Subject</button>
            </div>
        </form>
    </div>
</div>
@endsection