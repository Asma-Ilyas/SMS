@extends('layouts.app')
@section('title', 'Bulk Assign Subjects')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.subject-assignments.index') }}" class="hover:text-indigo-600 transition">← Back</a>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700 font-medium">Bulk Assign Subjects</span>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                <h1 class="text-3xl font-bold mb-2">📚 Bulk Assign Subjects</h1>
                <p class="text-indigo-100">Assign multiple subjects to a class section at once</p>
            </div>
        </div>

        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <form method="POST" action="{{ route('admin.subject-assignments.bulk.store') }}" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class Section *</label>
                        <select name="class_section_id" id="class_section_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Section --</option>
                            @foreach($classSections as $section)
                            <option value="{{ $section->id }}" {{ old('class_section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->full_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('class_section_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teacher (Optional - will apply to all)</label>
                        <select name="teacher_id" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Teacher --</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('teacher_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Weekly Frequency *</label>
                        <select name="weekly_frequency" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1">1 period per week</option>
                            <option value="2">2 periods per week</option>
                            <option value="3">3 periods per week</option>
                            <option value="4">4 periods per week</option>
                            <option value="5" selected>5 periods per week</option>
                            <option value="6">6 periods per week</option>
                            <option value="7">7 periods per week</option>
                            <option value="8">8 periods per week</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Subjects *</label>
                        <div class="border border-gray-300 rounded-lg p-3 max-h-48 overflow-y-auto" id="subjectList">
                            @foreach($subjects as $subject)
                            <div class="flex items-center py-1">
                                <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" 
                                       id="subject_{{ $subject->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="subject_{{ $subject->id }}" class="ml-2 text-sm text-gray-700 cursor-pointer">
                                    {{ $subject->name }} <span class="text-gray-400 text-xs">({{ $subject->code ?? 'N/A' }})</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-2 flex gap-2">
                            <button type="button" onclick="selectAll()" class="text-xs text-indigo-600 hover:text-indigo-800">Select All</button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="deselectAll()" class="text-xs text-gray-500 hover:text-gray-700">Deselect All</button>
                        </div>
                        @error('subject_ids')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-blue-800 text-sm flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span><strong>Note:</strong> Only subjects that are not already assigned to this section will be added. Existing assignments will be skipped.</span>
                    </p>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4" id="selectedSubjectsInfo">
                    <p class="text-yellow-800 text-sm">
                        <strong>Selected Subjects:</strong> <span id="selectedCount">0</span> subjects selected
                    </p>
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ route('admin.subject-assignments.index') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2.5 rounded-lg transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-lg transition shadow-md hover:shadow-lg">
                        📤 Bulk Assign Subjects
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('input[name="subject_ids[]"]:checked');
    document.getElementById('selectedCount').textContent = checkboxes.length;
}

function selectAll() {
    document.querySelectorAll('input[name="subject_ids[]"]').forEach(cb => cb.checked = true);
    updateSelectedCount();
}

function deselectAll() {
    document.querySelectorAll('input[name="subject_ids[]"]').forEach(cb => cb.checked = false);
    updateSelectedCount();
}

document.querySelectorAll('input[name="subject_ids[]"]').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

// Load assigned subjects when section changes
document.getElementById('class_section_id').addEventListener('change', function() {
    const sectionId = this.value;
    if (!sectionId) return;

    fetch(`/admin/subject-assignments/get-assigned/${sectionId}`)
        .then(response => response.json())
        .then(data => {
            const assignedIds = data.map(item => item.subject_id);
            document.querySelectorAll('input[name="subject_ids[]"]').forEach(cb => {
                if (assignedIds.includes(parseInt(cb.value))) {
                    cb.disabled = true;
                    cb.closest('.flex').style.opacity = '0.5';
                    cb.closest('.flex').title = 'Already assigned';
                } else {
                    cb.disabled = false;
                    cb.closest('.flex').style.opacity = '1';
                    cb.closest('.flex').title = '';
                }
            });
            updateSelectedCount();
        })
        .catch(error => console.error('Error loading assigned subjects:', error));
});

// Initialize on page load
updateSelectedCount();
</script>
@endsection