@extends('layouts.app')
@section('title', 'Create Exam')
@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-700">
            <h2 class="text-2xl font-bold text-white">Create New Exam</h2>
        </div>
        <form method="POST" action="{{ route('admin.exams.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Exam Type *</label>
                    <select name="exam_type_id" required class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select Type</option>
                        @foreach($examTypes as $type)
                            <option value="{{ $type->id }}" {{ old('exam_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('exam_type_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block font-medium">Exam Group</label>
                    <select name="exam_group_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">-- None --</option>
                        @foreach($examGroups as $group)
                            <option value="{{ $group->id }}" {{ old('exam_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Class *</label>
                    <select name="class_id" required class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->grade->name ?? '' }} {{ $class->stream->name ?? '' }} - {{ $class->section }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block font-medium">Exam Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-3 py-2">
                    @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block font-medium">Exam Center</label>
                    <input type="text" name="exam_center" value="{{ old('exam_center') }}" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">End Date *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Time Table (PDF/Image)</label>
                    <input type="file" name="time_table" accept=".pdf,.doc,.docx,.jpg,.png" class="w-full border rounded-lg px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Max 5MB</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-medium">Description</label>
                    <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Exam</button>
            </div>
        </form>
    </div>
</div>
@endsection