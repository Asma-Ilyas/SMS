{{-- resources/views/admin/exams/reports/subject-wise-form.blade.php --}}

@extends('layouts.app')

@section('title', 'Subject Wise Report - Filters')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">📊 Subject Wise Report</h1>
                    <p class="text-gray-500 mt-1">Select filters to generate subject-wise report</p>
                </div>
                <a href="{{ route('admin.exams.reports.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    ← Back
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300">
            <form method="GET" action="{{ route('admin.exams.reports.subject-wise') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Class Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class *</label>
                    <select name="class_id" id="class_id" 
                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500"
                            onchange="loadSections(this.value)">
                        <option value="">-- Select Class --</option>
                        @if(isset($classes) && $classes->count() > 0)
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    @php
                                        $gradeName = $class->grade->name ?? 'N/A';
                                        $streamName = $class->stream->name ?? '';
                                        $displayName = $gradeName;
                                        if ($streamName && !in_array($gradeName, ['KG', '1', '2', '3', '4', '5'])) {
                                            $displayName .= ' ' . $streamName;
                                        }
                                    @endphp
                                    {{ $displayName }}
                                </option>
                            @endforeach
                        @else
                            <option value="">No classes available</option>
                        @endif
                    </select>
                    @error('class_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Section Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select name="class_section_id" id="class_section_id" 
                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- All Sections --</option>
                        @if(isset($sections) && $sections->count() > 0)
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ request('class_section_id') == $section->id ? 'selected' : '' }}>
                                    {{ $section->section_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('class_section_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Subject Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                    <select name="subject_id" required 
                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Select Subject --</option>
                        @if(isset($subjects) && $subjects->count() > 0)
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                            @endforeach
                        @else
                            <option value="">No subjects available</option>
                        @endif
                    </select>
                    @error('subject_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-3 flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-300 shadow-md hover:shadow-lg">
                        🔍 Generate Report
                    </button>
                    <a href="{{ route('admin.exams.reports.subject-wise-form') }}" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-all duration-300">
                        Reset Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Debug Info - Remove after fixing -->
        @if(isset($classes) && $classes->count() == 0)
        <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
            <p class="text-yellow-700">
                <strong>⚠️ No classes found in database.</strong> 
                Please run <code>php artisan db:seed</code> to populate classes.
            </p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function loadSections(classId) {
    const sectionSelect = document.getElementById('class_section_id');
    
    if (!classId) {
        sectionSelect.innerHTML = '<option value="">-- All Sections --</option>';
        return;
    }
    
    // Show loading state
    sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
    
    // Try the correct route for fetching sections
    const url = `/admin/subject-assignments/get-sections/${classId}`;
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            sectionSelect.innerHTML = '<option value="">-- All Sections --</option>';
            
            if (data && data.length > 0) {
                data.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = section.section_name || section.name || 'Section';
                    sectionSelect.appendChild(option);
                });
            } else {
                sectionSelect.innerHTML = '<option value="">No sections available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading sections:', error);
            sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
        });
}

// Load sections on page load if class is selected
document.addEventListener('DOMContentLoaded', function() {
    const classId = document.getElementById('class_id').value;
    if (classId) {
        loadSections(classId);
    }
});
</script>
@endpush
@endsection