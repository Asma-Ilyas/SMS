@extends('layouts.app')
@section('title', 'Enter Marks')

@section('content')
<style>
    .marks-input {
        transition: all 0.3s ease;
    }
    .marks-input:focus {
        transform: scale(1.05);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }
</style>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.exam-marks.create') }}" class="hover:text-indigo-600 transition">← Change Selection</a>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700 font-medium">Enter Marks</span>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex flex-wrap justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">✏️ Enter Marks</h1>
                        <p class="text-indigo-100">{{ $exam->name }}</p>
                        <p class="text-indigo-200 text-sm">{{ $subject->name }} - {{ $classSection->full_name }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-indigo-200">Total Students</div>
                        <div class="text-2xl font-bold">{{ $students->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('admin.exam-marks.save') }}">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                <input type="hidden" name="subject_id" value="{{ $subject->id }}">

                <div class="px-6 py-4 bg-gray-50 border-b flex flex-wrap justify-between items-center gap-3">
                    <div class="flex gap-2">
                        <button type="button" id="markAllPresent" 
                                class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition">
                            ✅ Pass All
                        </button>
                        <button type="button" id="markAllAbsent" 
                                class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition">
                            ❌ Fail All
                        </button>
                        <button type="button" id="clearAll" 
                                class="px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-lg transition">
                            🗑️ Clear All
                        </button>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span>Max Marks: 100 | Passing: 40</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Marks (Max: 100)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($students as $index => $student)
                            @php $mark = $existingMarks->get($student->id); @endphp
                            <tr class="hover:bg-gray-50 transition student-row">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-800">{{ $student->first_name }} {{ $student->last_name }}</div>
                                            <div class="text-xs text-gray-400">Roll: {{ $student->roll_number ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" 
                                           name="marks[{{ $student->id }}]" 
                                           id="marks_{{ $student->id }}"
                                           value="{{ $mark->marks_obtained ?? '' }}"
                                           min="0" max="100"
                                           placeholder="Enter marks"
                                           class="marks-input w-32 text-center rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500
                                                  {{ $mark && $mark->marks_obtained >= 40 ? 'border-green-400 bg-green-50' : ($mark && $mark->marks_obtained !== '' ? 'border-red-400 bg-red-50' : '') }}">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" 
                                           name="remarks[{{ $student->id }}]" 
                                           value="{{ $mark->remarks ?? '' }}"
                                           placeholder="Optional"
                                           class="w-full rounded-lg border-gray-300 focus:ring-gray-500 focus:border-gray-500">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t flex flex-wrap justify-between items-center gap-3">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">💡 Tip:</span> 
                        <span class="text-green-600">Green = Pass (&ge;40)</span>
                        <span class="mx-2">|</span>
                        <span class="text-red-600">Red = Fail (&lt;40)</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.exam-marks.create') }}" 
                           class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition shadow-md hover:shadow-lg">
                            💾 Save Marks
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Individual input change handler
    const inputs = document.querySelectorAll('input[type="number"]');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            const val = parseInt(this.value);
            if (!isNaN(val) && val !== '') {
                if (val >= 40) {
                    this.classList.remove('border-red-400', 'bg-red-50');
                    this.classList.add('border-green-400', 'bg-green-50');
                } else {
                    this.classList.remove('border-green-400', 'bg-green-50');
                    this.classList.add('border-red-400', 'bg-red-50');
                }
            } else {
                this.classList.remove('border-green-400', 'bg-green-50', 'border-red-400', 'bg-red-50');
            }
        });

        // Trigger change on load to set initial color
        if (input.value !== '') {
            input.dispatchEvent(new Event('change'));
        }
    });

    // Mark All Pass
    document.getElementById('markAllPresent')?.addEventListener('click', function() {
        inputs.forEach(input => {
            input.value = 40;
            input.classList.remove('border-red-400', 'bg-red-50');
            input.classList.add('border-green-400', 'bg-green-50');
        });
    });

    // Mark All Fail
    document.getElementById('markAllAbsent')?.addEventListener('click', function() {
        inputs.forEach(input => {
            input.value = 20;
            input.classList.remove('border-green-400', 'bg-green-50');
            input.classList.add('border-red-400', 'bg-red-50');
        });
    });

    // Clear All
    document.getElementById('clearAll')?.addEventListener('click', function() {
        if (confirm('Clear all marks?')) {
            inputs.forEach(input => {
                input.value = '';
                input.classList.remove('border-green-400', 'bg-green-50', 'border-red-400', 'bg-red-50');
            });
        }
    });
});
</script>
@endsection