@extends('layouts.app')
@section('title', 'Edit Exam')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.exams.index') }}" class="hover:text-indigo-600 transition">← Back</a>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700 font-medium">Edit Exam</span>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                <h1 class="text-3xl font-bold mb-2">✏️ Edit Exam</h1>
                <p class="text-indigo-100">Update examination details</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <form method="POST" action="{{ route('admin.exams.update', $exam->id) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam Name *</label>
                        <input type="text" name="name" required value="{{ old('name', $exam->name) }}" 
                               placeholder="e.g., Mid-Term Examination 2025" 
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam Type *</label>
                        <select name="exam_type_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                            <option value="">Select Type</option>
                            @foreach($examTypes as $type)
                            <option value="{{ $type->id }}" {{ $exam->exam_type_id == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('exam_type_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam Group *</label>
                        <select name="exam_group_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                            <option value="">Select Group</option>
                            @foreach($examGroups as $group)
                            <option value="{{ $group->id }}" {{ $exam->exam_group_id == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('exam_group_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class Section *</label>
                        <select name="class_section_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                            <option value="">Select Section</option>
                            @foreach($classSections as $section)
                            <option value="{{ $section->id }}" {{ $exam->class_section_id == $section->id ? 'selected' : '' }}>
                                {{ $section->full_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('class_section_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                        <input type="date" name="start_date" required value="{{ old('start_date', $exam->start_date->format('Y-m-d')) }}" 
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                        @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                        <input type="date" name="end_date" required value="{{ old('end_date', $exam->end_date->format('Y-m-d')) }}" 
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                        @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam Center</label>
                        <input type="text" name="exam_center" placeholder="e.g., Main Hall, Room 101" 
                               value="{{ old('exam_center', $exam->exam_center) }}"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="is_published" class="w-full rounded-lg border-gray-300">
                            <option value="0" {{ !$exam->is_published ? 'selected' : '' }}>📌 Draft</option>
                            <option value="1" {{ $exam->is_published ? 'selected' : '' }}>✅ Published</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Additional information about this exam..." 
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">{{ old('description', $exam->description) }}</textarea>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800 text-sm flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Changing class section will affect student marks. Please ensure marks are recalculated after changes.</span>
                    </p>
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ route('admin.exams.show', $exam->id) }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2.5 rounded-lg transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition">
                        💾 Update Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection