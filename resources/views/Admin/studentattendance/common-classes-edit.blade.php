@extends('layouts.app')
@section('title', 'Edit Common Subject Class')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">✏️ Edit Common Subject Class</h1>
                    <p class="text-gray-500 mt-1">Update the combined class details</p>
                </div>
                <a href="{{ route('admin.studentattendance.common-classes') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">← Back</a>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <form method="POST" action="{{ route('admin.studentattendance.common-classes.update', $commonClass->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                    <select name="subject_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ $commonClass->subject_id == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class Name *</label>
                    <input type="text" name="name" required value="{{ old('name', $commonClass->name) }}" 
                           placeholder="e.g., Computer Lab Batch A" 
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room *</label>
                    <select name="room_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Room</option>
                        @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ $commonClass->room_id == $room->id ? 'selected' : '' }}>
                            {{ $room->name }} (Capacity: {{ $room->capacity }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="1" {{ $commonClass->is_active ? 'selected' : '' }}>✅ Active</option>
                        <option value="0" {{ !$commonClass->is_active ? 'selected' : '' }}>❌ Inactive</option>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sections Included *</label>
                    <select name="class_section_ids[]" required multiple class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" size="5">
                        @foreach($classSections as $section)
                        <option value="{{ $section->id }}" {{ in_array($section->id, $selectedSectionIds) ? 'selected' : '' }}>
                            {{ $section->full_name }} ({{ $section->section_name }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple sections</p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="3" 
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                              placeholder="Additional notes about this combined class">{{ old('description', $commonClass->description) }}</textarea>
                </div>
                
                <div class="md:col-span-2 flex gap-3">
                    <a href="{{ route('admin.studentattendance.common-classes') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 rounded-lg transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded-lg transition">
                        💾 Update Common Class
                    </button>
                </div>
            </form>
        </div>

        {{-- Current Sections Display --}}
        <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
            <h3 class="font-bold text-gray-800 mb-3">📋 Current Sections in this Common Class</h3>
            <div class="flex flex-wrap gap-2">
                @php
                    $currentSections = \App\Models\ClassSection::whereIn('id', $selectedSectionIds)->get();
                @endphp
                @forelse($currentSections as $section)
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">
                        {{ $section->full_name }}
                    </span>
                @empty
                    <span class="text-gray-500">No sections selected</span>
                @endforelse
            </div>
            <div class="mt-3 text-sm text-gray-500">
                <p>Subject: <strong>{{ $commonClass->subject->name ?? 'N/A' }}</strong></p>
                <p>Room: <strong>{{ $commonClass->room->name ?? 'N/A' }}</strong></p>
                <p>Status: <strong class="{{ $commonClass->is_active ? 'text-green-600' : 'text-red-600' }}">{{ $commonClass->is_active ? 'Active' : 'Inactive' }}</strong></p>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="mt-6 bg-red-50 border border-red-200 rounded-2xl p-6">
            <h3 class="font-bold text-red-800 mb-3">⚠️ Danger Zone</h3>
            <form method="POST" action="{{ route('admin.studentattendance.common-classes.destroy', $commonClass->id) }}" 
                  onsubmit="return confirm('Are you sure you want to delete this common class? This action cannot be undone.')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    🗑️ Delete This Common Class
                </button>
            </form>
            <p class="text-xs text-red-600 mt-2">Deleting this common class will remove it from the system. Students will no longer be combined for this subject.</p>
        </div>
    </div>
</div>
@endsection