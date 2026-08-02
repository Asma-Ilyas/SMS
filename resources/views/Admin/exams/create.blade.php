@extends('layouts.app')
@section('title', 'Create Exam')

@section('content')
<style>
    .subject-card {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }
    .subject-card:hover {
        border-color: #6366f1;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .subject-card.selected {
        border-color: #6366f1;
        background: #eef2ff;
    }
    .subject-card .subject-checkbox {
        width: 20px;
        height: 20px;
        accent-color: #6366f1;
    }
    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #6366f1;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .schedule-row {
        background: #f8fafc;
        border-radius: 8px;
        padding: 10px;
        margin-top: 8px;
    }
    .schedule-row input, .schedule-row select {
        background: white;
    }
    .date-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .date-badge-success {
        background: #dcfce7;
        color: #16a34a;
    }
    .date-badge-warning {
        background: #fef3c7;
        color: #d97706;
    }
</style>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.exams.index') }}" class="hover:text-indigo-600 transition">← Back</a>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700 font-medium">Create Exam</span>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                <h1 class="text-3xl font-bold mb-2">✏️ Create New Exam</h1>
                <p class="text-indigo-100">Set up a new examination with subject-wise schedule and marks</p>
            </div>
        </div>

        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <form method="POST" action="{{ route('admin.exams.store') }}" class="space-y-6" id="examForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam Name *</label>
                        <input type="text" name="name" required placeholder="e.g., Mid-Term Examination 2025" 
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('name') }}">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam Type *</label>
                        <select name="exam_type_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                            <option value="">Select Type</option>
                            @foreach($examTypes as $type)
                            <option value="{{ $type->id }}" {{ old('exam_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('exam_type_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam Group *</label>
                        <select name="exam_group_id" required class="w-full rounded-lg border-gray-300">
                            <option value="">Select Group</option>
                            @foreach($examGroups as $group)
                            <option value="{{ $group->id }}" {{ old('exam_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                        @error('exam_group_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class Section *</label>
                        <select name="class_section_id" id="class_section_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                            <option value="">Select Section</option>
                            @foreach($classSections as $section)
                            <option value="{{ $section->id }}" {{ old('class_section_id') == $section->id ? 'selected' : '' }}>{{ $section->full_name }}</option>
                            @endforeach
                        </select>
                        @error('class_section_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                        <input type="date" name="start_date" id="exam_start_date" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500" value="{{ old('start_date') }}">
                        @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                        <input type="date" name="end_date" id="exam_end_date" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500" value="{{ old('end_date') }}">
                        @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Passing Percentage *</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="passing_percentage" id="passing_percentage" value="{{ old('passing_percentage', 40) }}" min="0" max="100" 
                                   class="w-32 rounded-lg border-gray-300 focus:ring-indigo-500">
                            <span class="text-gray-500">%</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Default: 40%</p>
                        @error('passing_percentage')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Publish Immediately</label>
                        <select name="is_published" class="w-full rounded-lg border-gray-300">
                            <option value="0" {{ old('is_published', 0) == 0 ? 'selected' : '' }}>No (Draft)</option>
                            <option value="1" {{ old('is_published') == 1 ? 'selected' : '' }}>Yes (Published)</option>
                        </select>
                    </div>
                </div>

                {{-- Subject Selection with Schedule --}}
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">📚 Select Subjects & Schedule</h3>
                            <p class="text-sm text-gray-500 mt-1" id="selectedSectionInfo">Select a class section above to load subjects</p>
                        </div>
                        <div class="flex gap-2" id="subjectActions">
                            <button type="button" id="selectAllSubjects" class="px-3 py-1 text-sm bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition hidden">
                                ✅ Select All
                            </button>
                            <button type="button" id="deselectAllSubjects" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition hidden">
                                ❌ Deselect All
                            </button>
                        </div>
                    </div>
                    
                    <div id="sectionInfoBanner" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 hidden">
                        <p class="text-blue-800 text-sm flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Showing subjects for: <strong id="selectedSectionName">-</strong></span>
                        </p>
                    </div>

                    <div id="loadingSubjects" class="text-center py-8 hidden">
                        <div class="loading-spinner"></div>
                        <p class="text-gray-500 mt-4">Loading subjects for this section...</p>
                    </div>

                    <div id="noSubjectsMessage" class="text-center py-8 hidden">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No subjects assigned</h3>
                        <p class="mt-1 text-sm text-gray-500">Please assign subjects to this class section first.</p>
                        <a href="{{ route('admin.subject-assignments.index') }}" class="mt-4 inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                            Go to Subject Assignments
                        </a>
                    </div>

                    <div id="subjectMarksContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <p class="text-gray-500 text-sm col-span-2 text-center py-4">
                            Please select a class section to configure subject-wise marks
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Additional information about this exam..." 
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ route('admin.exams.index') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2.5 rounded-lg transition text-center">
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition shadow-md hover:shadow-lg">
                        Create Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSectionSelect = document.getElementById('class_section_id');
    const subjectContainer = document.getElementById('subjectMarksContainer');
    const loadingDiv = document.getElementById('loadingSubjects');
    const noSubjectsDiv = document.getElementById('noSubjectsMessage');
    const sectionInfoBanner = document.getElementById('sectionInfoBanner');
    const selectedSectionName = document.getElementById('selectedSectionName');
    const selectAllBtn = document.getElementById('selectAllSubjects');
    const deselectAllBtn = document.getElementById('deselectAllSubjects');
    const startDateInput = document.getElementById('exam_start_date');
    const endDateInput = document.getElementById('exam_end_date');

    // Function to get date range
    function getDateRange() {
        const start = startDateInput.value;
        const end = endDateInput.value;
        if (!start || !end) return [];
        
        const dates = [];
        let current = new Date(start);
        const endDate = new Date(end);
        
        while (current <= endDate) {
            // Skip weekends (Saturday=6, Sunday=0)
            const day = current.getDay();
            if (day !== 0 && day !== 6) {
                dates.push(new Date(current));
            }
            current.setDate(current.getDate() + 1);
        }
        return dates;
    }

    // Load subjects when class section changes
    classSectionSelect.addEventListener('change', function() {
        const sectionId = this.value;
        const sectionName = this.options[this.selectedIndex]?.text || '';
        
        subjectContainer.innerHTML = '';
        noSubjectsDiv.classList.add('hidden');
        selectAllBtn.classList.add('hidden');
        deselectAllBtn.classList.add('hidden');

        if (!sectionId) {
            sectionInfoBanner.classList.add('hidden');
            selectedSectionName.textContent = '';
            return;
        }

        loadingDiv.classList.remove('hidden');
        sectionInfoBanner.classList.remove('hidden');
        selectedSectionName.textContent = sectionName;

        fetch(`/get-subjects-by-section/${sectionId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                loadingDiv.classList.add('hidden');
                
                let subjects = [];
                if (data.subjects) {
                    subjects = data.subjects;
                } else if (Array.isArray(data)) {
                    subjects = data;
                }
                
                if (!subjects || subjects.length === 0) {
                    noSubjectsDiv.classList.remove('hidden');
                    return;
                }

                selectAllBtn.classList.remove('hidden');
                deselectAllBtn.classList.remove('hidden');

                // Get available dates
                const availableDates = getDateRange();
                
                let html = '';
                subjects.forEach((subject, index) => {
                    const defaultPassing = Math.round(100 * 40 / 100);
                    // Assign date based on index (spread across available dates)
                    const dateIndex = index % Math.max(availableDates.length, 1);
                    const examDate = availableDates[dateIndex] || new Date();
                    
                    html += `
                        <div class="subject-card rounded-lg p-4 bg-white border-2" data-subject-id="${subject.id}">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" 
                                       name="subjects[]" 
                                       value="${subject.id}" 
                                       id="subject_${subject.id}"
                                       class="subject-checkbox mt-1"
                                       checked>
                                <div class="flex-1">
                                    <label for="subject_${subject.id}" class="font-medium text-gray-800 cursor-pointer">
                                        ${subject.name}
                                    </label>
                                    <div class="text-xs text-gray-400">Code: ${subject.code || 'N/A'}</div>
                                </div>
                            </div>
                            
                            {{-- SCHEDULE SECTION --}}
                            <div class="schedule-row grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">📅 Exam Date</label>
                                    <input type="date" 
                                           name="subject_schedules[${subject.id}][exam_date]" 
                                           value="${examDate.toISOString().split('T')[0]}"
                                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">⏰ Start Time</label>
                                    <input type="time" 
                                           name="subject_schedules[${subject.id}][start_time]" 
                                           value="09:00"
                                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">⏰ End Time</label>
                                    <input type="time" 
                                           name="subject_schedules[${subject.id}][end_time]" 
                                           value="11:00"
                                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500">
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">🏠 Room</label>
                                <input type="text" 
                                       name="subject_schedules[${subject.id}][room]" 
                                       placeholder="e.g., Hall A, Room 101"
                                       class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500">
                            </div>
                            
                            {{-- MARKS SECTION --}}
                            <div class="grid grid-cols-2 gap-3 mt-3 border-t border-gray-200 pt-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">📊 Max Marks</label>
                                    <input type="number" 
                                           name="subject_marks[${subject.id}][max_marks]" 
                                           value="100" 
                                           min="1" max="1000"
                                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 max-marks-input">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">✅ Passing Marks</label>
                                    <input type="number" 
                                           name="subject_marks[${subject.id}][passing_marks]" 
                                           value="${defaultPassing}" 
                                           min="0" 
                                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 passing-marks-input">
                                </div>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-xs text-gray-400">
                                    Passing %: <span class="font-medium pass-percent-display">${40}%</span>
                                </span>
                                <span class="text-xs text-gray-400">
                                    <span class="status-dot inline-block w-2 h-2 rounded-full bg-green-500"></span>
                                    Included
                                </span>
                            </div>
                        </div>
                    `;
                });
                subjectContainer.innerHTML = html;

                // Add event listeners for real-time calculation
                document.querySelectorAll('.max-marks-input, .passing-marks-input').forEach(input => {
                    input.addEventListener('input', function() {
                        const card = this.closest('.subject-card');
                        const max = parseFloat(card.querySelector('.max-marks-input').value) || 1;
                        const pass = parseFloat(card.querySelector('.passing-marks-input').value) || 0;
                        const percent = Math.round((pass / max) * 100);
                        card.querySelector('.pass-percent-display').textContent = percent + '%';
                    });
                });

                // Checkbox change handler
                document.querySelectorAll('.subject-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const card = this.closest('.subject-card');
                        const config = card.querySelector('.subject-config');
                        const statusDot = card.querySelector('.status-dot');
                        
                        if (this.checked) {
                            card.classList.add('selected');
                            if (statusDot) {
                                statusDot.className = 'status-dot inline-block w-2 h-2 rounded-full bg-green-500';
                            }
                            const textNode = card.querySelector('.status-dot')?.parentElement;
                            if (textNode) {
                                textNode.innerHTML = ' <span class="status-dot inline-block w-2 h-2 rounded-full bg-green-500"></span> Included';
                            }
                        } else {
                            card.classList.remove('selected');
                            if (statusDot) {
                                statusDot.className = 'status-dot inline-block w-2 h-2 rounded-full bg-gray-300';
                            }
                            const textNode = card.querySelector('.status-dot')?.parentElement;
                            if (textNode) {
                                textNode.innerHTML = ' <span class="status-dot inline-block w-2 h-2 rounded-full bg-gray-300"></span> Excluded';
                            }
                        }
                    });
                });

                // Ensure all subjects are selected by default
                document.querySelectorAll('.subject-checkbox').forEach(cb => {
                    cb.checked = true;
                    cb.dispatchEvent(new Event('change'));
                });
            })
            .catch(error => {
                console.error('Error loading subjects:', error);
                loadingDiv.classList.add('hidden');
                subjectContainer.innerHTML = `
                    <div class="col-span-2 text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Error loading subjects</h3>
                        <p class="mt-1 text-sm text-gray-500">Please ensure subjects are assigned to this section.</p>
                        <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                            🔄 Retry
                        </button>
                    </div>
                `;
            });
    });

    // Update subject dates when exam dates change
    function updateSubjectDates() {
        const availableDates = getDateRange();
        const dateInputs = document.querySelectorAll('[name$="[exam_date]"]');
        dateInputs.forEach((input, index) => {
            const dateIndex = index % Math.max(availableDates.length, 1);
            if (availableDates[dateIndex]) {
                input.value = availableDates[dateIndex].toISOString().split('T')[0];
            }
        });
    }

    startDateInput.addEventListener('change', updateSubjectDates);
    endDateInput.addEventListener('change', updateSubjectDates);

    // Select All Subjects
    selectAllBtn?.addEventListener('click', function() {
        document.querySelectorAll('.subject-checkbox:not(:checked)').forEach(cb => {
            cb.checked = true;
            cb.dispatchEvent(new Event('change'));
        });
    });

    // Deselect All Subjects
    deselectAllBtn?.addEventListener('click', function() {
        document.querySelectorAll('.subject-checkbox:checked').forEach(cb => {
            cb.checked = false;
            cb.dispatchEvent(new Event('change'));
        });
    });

    // Form validation
    document.getElementById('examForm').addEventListener('submit', function(e) {
        const checkedSubjects = document.querySelectorAll('.subject-checkbox:checked');
        if (checkedSubjects.length === 0) {
            e.preventDefault();
            alert('⚠️ Please select at least one subject for this exam.');
            return false;
        }
        return true;
    });

    // Trigger on page load if section is selected
    if (classSectionSelect.value) {
        classSectionSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection