{{-- resources/views/admin/exams/reports/student-wise-form.blade.php --}}

@extends('layouts.app')

@section('title', 'Student-wise Report')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">👨‍🎓 Student-wise Report</h1>
                    <p class="text-gray-500 mt-1">Select a student to view their academic history</p>
                </div>
                <a href="{{ route('admin.exams.reports.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    ← Back to Reports
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300">
            <form method="GET" action="{{ route('admin.exams.reports.student-wise') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Class Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class *</label>
                    <select name="class_id" id="class_id" 
                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500"
                            onchange="loadSectionsAndStudents(this.value)">
                        <option value="">-- Select Class --</option>
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
                    </select>
                    @error('class_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Section Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select name="class_section_id" id="class_section_id" 
                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500"
                            onchange="loadStudents(this.value)">
                        <option value="">-- All Sections --</option>
                        @foreach($sections ?? [] as $section)
                            <option value="{{ $section->id }}" {{ request('class_section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->section_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_section_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Student Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student *</label>
                    <select name="student_id" id="student_id" required 
                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Select Student --</option>
                        @if(isset($students) && $students->count() > 0)
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->first_name ?? 'N/A' }} {{ $student->last_name ?? '' }} 
                                    @if(isset($student->roll_number))
                                        (Roll: {{ $student->roll_number }})
                                    @endif
                                </option>
                            @endforeach
                        @else
                            <option value="">No students available</option>
                        @endif
                    </select>
                    @error('student_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-3 flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-300 shadow-md hover:shadow-lg">
                        🔍 Generate Report
                    </button>
                    <a href="{{ route('admin.exams.reports.student-wise-form') }}" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-all duration-300">
                        Reset Filters
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadSectionsAndStudents(classId) {
    if (!classId) {
        document.getElementById('class_section_id').innerHTML = '<option value="">-- All Sections --</option>';
        document.getElementById('student_id').innerHTML = '<option value="">-- Select Student --</option>';
        return;
    }
    
    // Load sections
    fetch(`/admin/subject-assignments/get-sections/${classId}`)
        .then(response => response.json())
        .then(data => {
            const sectionSelect = document.getElementById('class_section_id');
            sectionSelect.innerHTML = '<option value="">-- All Sections --</option>';
            
            if (data && data.length > 0) {
                data.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = section.section_name || section.name || 'Section';
                    sectionSelect.appendChild(option);
                });
            }
            
            // Load students for this class
            loadStudents(classId);
        })
        .catch(error => {
            console.error('Error loading sections:', error);
        });
}

function loadStudents(classIdOrSectionId) {
    const classId = document.getElementById('class_id').value;
    const sectionId = document.getElementById('class_section_id').value;
    const studentSelect = document.getElementById('student_id');
    
    const id = sectionId || classId;
    
    if (!id) {
        studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
        return;
    }
    
    studentSelect.innerHTML = '<option value="">Loading students...</option>';
    
    fetch(`/admin/get-students-by-class-section/${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
            
            if (data && data.length > 0) {
                data.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    const firstName = student.first_name || 'N/A';
                    const lastName = student.last_name || '';
                    const roll = student.roll_number ? ' (Roll: ' + student.roll_number + ')' : '';
                    option.textContent = firstName + ' ' + lastName + roll;
                    studentSelect.appendChild(option);
                });
            } else {
                studentSelect.innerHTML = '<option value="">No students available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            studentSelect.innerHTML = '<option value="">Error loading students</option>';
        });
}

// Load on page load if class is selected
document.addEventListener('DOMContentLoaded', function() {
    const classId = document.getElementById('class_id').value;
    if (classId) {
        loadSectionsAndStudents(classId);
    }
});
</script>
@endpush
@endsection