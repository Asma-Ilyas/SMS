@extends('layouts.app')
@section('title', 'Assign Subject to Section')

@section('content')
<style>
    .step-indicator {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    .step {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #94a3b8;
    }
    .step.active {
        color: #4f46e5;
        font-weight: 600;
    }
    .step.completed {
        color: #22c55e;
    }
    .step-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e5e7eb;
        color: #6b7280;
        font-weight: 600;
        font-size: 12px;
    }
    .step.active .step-number {
        background: #4f46e5;
        color: white;
    }
    .step.completed .step-number {
        background: #22c55e;
        color: white;
    }
    .step-line {
        flex: 1;
        height: 2px;
        background: #e5e7eb;
        min-width: 30px;
    }
    .step-line.completed {
        background: #22c55e;
    }
</style>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.subject-assignments.index') }}" class="hover:text-indigo-600 transition">← Back</a>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700 font-medium">Assign Subject</span>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                <h1 class="text-3xl font-bold mb-2">📚 Assign Subject to Section</h1>
                <p class="text-indigo-100">Select a class, then a section, then assign subjects</p>
            </div>
        </div>

        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            {{ session('error') }}
        </div>
        @endif

        {{-- Step Indicator --}}
        <div class="step-indicator">
            <div class="step {{ $selectedClass ? 'completed' : 'active' }}">
                <span class="step-number">1</span>
                <span>Select Class</span>
            </div>
            <div class="step-line {{ $selectedClass ? 'completed' : '' }}"></div>
            <div class="step {{ $selectedClass && !$selectedSection ? 'active' : ($selectedSection ? 'completed' : '') }}">
                <span class="step-number">2</span>
                <span>Select Section</span>
            </div>
            <div class="step-line {{ $selectedSection ? 'completed' : '' }}"></div>
            <div class="step {{ $selectedSection ? 'active' : '' }}">
                <span class="step-number">3</span>
                <span>Assign Subject</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            {{-- Step 1: Select Class --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Step 1: Select Class</h3>
                <form method="GET" action="{{ route('admin.subject-assignments.create') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class *</label>
                        <select name="class_id" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500" onchange="this.form.submit()">
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                {{ $class->grade->name ?? 'Class' }} {{ $class->stream->name ?? '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        @if($selectedClass)
                        <a href="{{ route('admin.subject-assignments.create') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Clear Selection</a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Step 2: Select Section --}}
            @if($selectedClass)
            <div class="mb-6 border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Step 2: Select Section</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($sections as $section)
                    <a href="{{ route('admin.subject-assignments.create', ['class_id' => $selectedClass->id, 'class_section_id' => $section->id]) }}" 
                       class="block p-4 rounded-lg border-2 {{ $selectedSection && $selectedSection->id == $section->id ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50' }} transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-800">Section {{ $section->section_name }}</p>
                                <p class="text-sm text-gray-500">{{ $section->full_name }}</p>
                            </div>
                            @if($selectedSection && $selectedSection->id == $section->id)
                            <svg class="w-6 h-6 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Step 3: Assign Subject --}}
            @if($selectedSection)
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Step 3: Assign Subject to {{ $selectedSection->full_name }}</h3>
                
                <form method="POST" action="{{ route('admin.subject-assignments.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="class_section_id" value="{{ $selectedSection->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                            <select name="subject_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                            @error('weekly_frequency')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="is_active" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>✅ Active</option>
                                <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>❌ Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Is Elective?</label>
                            <select name="is_elective" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="0" {{ old('is_elective', 0) == 0 ? 'selected' : '' }}>❌ No (Compulsory)</option>
                                <option value="1" {{ old('is_elective') == 1 ? 'selected' : '' }}>✅ Yes (Elective)</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-yellow-800 text-sm flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span><strong>Note:</strong> A subject can only be assigned once per section.</span>
                        </p>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <a href="{{ route('admin.subject-assignments.index') }}" 
                           class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2.5 rounded-lg transition text-center">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition shadow-md hover:shadow-lg">
                            💾 Assign Subject
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection