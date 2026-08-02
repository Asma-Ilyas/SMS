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
    .pass-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .pass-badge-pass {
        background: #dcfce7;
        color: #16a34a;
    }
    .pass-badge-fail {
        background: #fee2e2;
        color: #dc2626;
    }
    .pass-badge-empty {
        background: #f3f4f6;
        color: #9ca3af;
    }
    .table-header {
        background: #f8fafc;
        position: sticky;
        top: 0;
        z-index: 20;
    }
    .student-row:hover {
        background: #f8fafc;
    }
    .student-row td {
        vertical-align: middle;
    }
</style>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                        <a href="{{ route('admin.exams.show', $exam->id) }}" class="hover:text-indigo-600 transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Exam
                        </a>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-500">Enter Marks</span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">✏️ Enter Marks</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-1">
                        <span class="text-sm text-gray-600">{{ $exam->name }}</span>
                        <span class="text-gray-300">|</span>
                        <span class="text-sm text-gray-600">{{ $exam->classSection->full_name ?? 'N/A' }}</span>
                        <span class="text-gray-300">|</span>
                        <span class="text-sm text-gray-600">{{ $exam->start_date->format('d M Y') }} - {{ $exam->end_date->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="bg-white rounded-xl shadow-sm px-4 py-2 text-center">
                        <span class="text-xs text-gray-500">Total Students</span>
                        <p class="text-2xl font-bold text-gray-800">{{ $students->count() }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm px-4 py-2 text-center">
                        <span class="text-xs text-gray-500">Subjects</span>
                        <p class="text-2xl font-bold text-indigo-600">{{ $subjects->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- No Subjects Warning --}}
        @if($subjects->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 text-center shadow-sm">
            <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-yellow-800">No subjects found</h3>
            <p class="mt-1 text-sm text-yellow-700">Please assign subjects to this section before entering marks.</p>
            <a href="{{ route('admin.subject-assignments.index') }}" class="mt-4 inline-block px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition">
                Go to Subject Assignments
            </a>
        </div>
        @else

        {{-- Marks Form --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('admin.exams.store-marks', $exam->id) }}">
                @csrf
                
                {{-- Toolbar --}}
                <div class="px-6 py-3 bg-gray-50 border-b flex flex-wrap justify-between items-center gap-3">
                    <div class="flex flex-wrap gap-2">
                        <button type="button" id="markAllPresent" 
                                class="inline-flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition shadow-sm">
                            ✅ Pass All
                        </button>
                        <button type="button" id="markAllAbsent" 
                                class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition shadow-sm">
                            ❌ Fail All
                        </button>
                        <button type="button" id="clearAll" 
                                class="inline-flex items-center px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-lg transition shadow-sm">
                            🗑️ Clear All
                        </button>
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <span class="text-gray-500">Max: <strong>100</strong> | Passing: <strong>40</strong></span>
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                <span class="text-gray-600">Pass</span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                <span class="text-gray-600">Fail</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto relative">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="table-header">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 sticky left-0 z-20 min-w-[180px]">
                                    <span class="flex items-center gap-2">
                                        👨‍🎓 Student
                                    </span>
                                </th>
                                @foreach($subjects as $assignment)
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px] bg-gray-50">
                                    {{ $assignment->subject->name ?? 'N/A' }}
                                    <div class="text-[10px] text-gray-400 font-normal">Max: 100</div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students as $student)
                            <tr class="student-row transition">
                                <td class="px-4 py-3 sticky left-0 bg-white z-10">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold shadow-md flex-shrink-0">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-800 text-sm">{{ $student->first_name }} {{ $student->last_name }}</div>
                                            <div class="text-xs text-gray-400">Roll: {{ $student->roll_number ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($subjects as $assignment)
                                @php
                                    // FIX: Check if marks exists and is not null
                                    $mark = null;
                                    if (isset($marks) && $marks) {
                                        $studentMarks = $marks->get($student->id);
                                        if ($studentMarks) {
                                            $mark = $studentMarks->where('subject_id', $assignment->subject_id)->first();
                                        }
                                    }
                                    $value = $mark ? $mark->marks_obtained : '';
                                    $isPass = $value !== '' && $value >= 40;
                                    $isFail = $value !== '' && $value < 40;
                                @endphp
                                <td class="px-2 py-2 text-center align-middle">
                                    <div class="flex items-center justify-center gap-1">
                                        <input type="number" 
                                               name="marks[{{ $student->id }}][{{ $assignment->subject_id }}]" 
                                               value="{{ $value }}"
                                               min="0" max="100"
                                               placeholder="-"
                                               class="marks-input w-20 text-center rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                                      {{ $isPass ? 'border-green-400 bg-green-50' : ($isFail ? 'border-red-400 bg-red-50' : '') }}">
                                        @if($isPass)
                                            <span class="pass-badge pass-badge-pass">✓</span>
                                        @elseif($isFail)
                                            <span class="pass-badge pass-badge-fail">✗</span>
                                        @else
                                            <span class="pass-badge pass-badge-empty">-</span>
                                        @endif
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t flex flex-wrap justify-between items-center gap-3">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">💡 Tip:</span> 
                        <span class="text-green-600">Green = Pass (&ge;40)</span>
                        <span class="mx-2">|</span>
                        <span class="text-red-600">Red = Fail (&lt;40)</span>
                        <span class="mx-2">|</span>
                        <span class="text-gray-400">Click on any cell to enter marks</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.exams.show', $exam->id) }}" 
                           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition shadow-md hover:shadow-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Save Marks
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Individual input change handler
    const inputs = document.querySelectorAll('input[type="number"]');
    inputs.forEach(input => {
        // Trigger on change
        input.addEventListener('change', function() {
            updateInputStyle(this);
        });

        // Trigger on input for real-time updates
        input.addEventListener('input', function() {
            updateInputStyle(this);
        });

        // Trigger on load to set initial color
        if (input.value !== '') {
            updateInputStyle(input);
        }
    });

    function updateInputStyle(input) {
        const val = parseInt(input.value);
        const td = input.closest('td');
        const badge = td ? td.querySelector('.pass-badge') : null;
        
        if (!isNaN(val) && val !== '') {
            if (val >= 40) {
                input.classList.remove('border-red-400', 'bg-red-50');
                input.classList.add('border-green-400', 'bg-green-50');
                if (badge) {
                    badge.className = 'pass-badge pass-badge-pass';
                    badge.textContent = '✓';
                }
            } else {
                input.classList.remove('border-green-400', 'bg-green-50');
                input.classList.add('border-red-400', 'bg-red-50');
                if (badge) {
                    badge.className = 'pass-badge pass-badge-fail';
                    badge.textContent = '✗';
                }
            }
        } else {
            input.classList.remove('border-green-400', 'bg-green-50', 'border-red-400', 'bg-red-50');
            if (badge) {
                badge.className = 'pass-badge pass-badge-empty';
                badge.textContent = '-';
            }
        }
    }

    // Mark All Pass
    document.getElementById('markAllPresent')?.addEventListener('click', function() {
        if (confirm('Mark all students as PASS (40 marks)?')) {
            inputs.forEach(input => {
                input.value = 40;
                updateInputStyle(input);
            });
        }
    });

    // Mark All Fail
    document.getElementById('markAllAbsent')?.addEventListener('click', function() {
        if (confirm('Mark all students as FAIL (20 marks)?')) {
            inputs.forEach(input => {
                input.value = 20;
                updateInputStyle(input);
            });
        }
    });

    // Clear All
    document.getElementById('clearAll')?.addEventListener('click', function() {
        if (confirm('Clear all marks?')) {
            inputs.forEach(input => {
                input.value = '';
                updateInputStyle(input);
            });
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+S to save
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            const form = document.querySelector('form');
            if (form) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.click();
                }
            }
        }
    });

    // Auto-focus first empty input
    const firstEmpty = document.querySelector('input[type="number"][value=""]');
    if (firstEmpty) {
        firstEmpty.focus();
    }
});
</script>
@endsection