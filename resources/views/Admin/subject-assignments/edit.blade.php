@extends('layouts.app')
@section('title', 'Edit Subject Assignment')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.subject-assignments.index') }}" class="hover:text-indigo-600 transition">← Back</a>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700 font-medium">Edit Subject Assignment</span>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                <h1 class="text-3xl font-bold mb-2">✏️ Edit Subject Assignment</h1>
                <p class="text-indigo-100">{{ $assignment->subject->name ?? 'N/A' }} → {{ $assignment->classSection->full_name ?? 'N/A' }}</p>
            </div>
        </div>

        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <form method="POST" action="{{ route('admin.subject-assignments.update', $assignment->id) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class Section *</label>
                        <select name="class_section_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Section --</option>
                            @foreach($classSections as $section)
                            <option value="{{ $section->id }}" {{ old('class_section_id', $assignment->class_section_id) == $section->id ? 'selected' : '' }}>
                                {{ $section->full_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('class_section_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                        <select name="subject_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Subject --</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $assignment->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }} ({{ $subject->code ?? 'N/A' }})
                            </option>
                            @endforeach
                        </select>
                        @error('subject_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teacher (Optional)</label>
                        <select name="teacher_id" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Teacher --</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $assignment->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('teacher_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Weekly Frequency *</label>
                        <select name="weekly_frequency" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1" {{ old('weekly_frequency', $assignment->weekly_frequency) == 1 ? 'selected' : '' }}>1 period per week</option>
                            <option value="2" {{ old('weekly_frequency', $assignment->weekly_frequency) == 2 ? 'selected' : '' }}>2 periods per week</option>
                            <option value="3" {{ old('weekly_frequency', $assignment->weekly_frequency) == 3 ? 'selected' : '' }}>3 periods per week</option>
                            <option value="4" {{ old('weekly_frequency', $assignment->weekly_frequency) == 4 ? 'selected' : '' }}>4 periods per week</option>
                            <option value="5" {{ old('weekly_frequency', $assignment->weekly_frequency) == 5 ? 'selected' : '' }}>5 periods per week</option>
                            <option value="6" {{ old('weekly_frequency', $assignment->weekly_frequency) == 6 ? 'selected' : '' }}>6 periods per week</option>
                            <option value="7" {{ old('weekly_frequency', $assignment->weekly_frequency) == 7 ? 'selected' : '' }}>7 periods per week</option>
                            <option value="8" {{ old('weekly_frequency', $assignment->weekly_frequency) == 8 ? 'selected' : '' }}>8 periods per week</option>
                        </select>
                        @error('weekly_frequency')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="is_active" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1" {{ old('is_active', $assignment->is_active) == 1 ? 'selected' : '' }}>✅ Active</option>
                            <option value="0" {{ old('is_active', $assignment->is_active) == 0 ? 'selected' : '' }}>❌ Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Is Elective?</label>
                        <select name="is_elective" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="0" {{ old('is_elective', $assignment->is_elective) == 0 ? 'selected' : '' }}>❌ No (Compulsory)</option>
                            <option value="1" {{ old('is_elective', $assignment->is_elective) == 1 ? 'selected' : '' }}>✅ Yes (Elective)</option>
                        </select>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800 text-sm flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span><strong>Note:</strong> Changing the section or subject will create a new assignment. The old assignment will be updated.</span>
                    </p>
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ route('admin.subject-assignments.index') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2.5 rounded-lg transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition shadow-md hover:shadow-lg">
                        💾 Update Assignment
                    </button>
                </div>
            </form>
        </div>

        {{-- Current Assignment Info --}}
        <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
            <h3 class="font-bold text-gray-800 mb-3">📋 Current Assignment Details</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Section</p>
                    <p class="font-semibold">{{ $assignment->classSection->full_name ?? 'N/A' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Subject</p>
                    <p class="font-semibold">{{ $assignment->subject->name ?? 'N/A' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Teacher</p>
                    <p class="font-semibold">{{ $assignment->teacher->name ?? 'Not Assigned' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Frequency</p>
                    <p class="font-semibold">{{ $assignment->weekly_frequency ?? 'N/A' }}x/week</p>
                </div>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="mt-6 bg-red-50 border border-red-200 rounded-2xl p-6">
            <h3 class="font-bold text-red-800 mb-3">⚠️ Danger Zone</h3>
            <form method="POST" action="{{ route('admin.subject-assignments.destroy', $assignment->id) }}" 
                  onsubmit="return confirm('Are you sure you want to remove this subject assignment? This action cannot be undone.')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    🗑️ Remove This Assignment
                </button>
            </form>
            <p class="text-xs text-red-600 mt-2">Deleting this assignment will remove the subject from this section.</p>
        </div>
    </div>
</div>
@endsection