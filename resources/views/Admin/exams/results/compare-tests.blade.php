@extends('layouts.app')

@section('title', 'Compare Tests' . (isset($selectedStudent) ? ' - ' . $selectedStudent->first_name : ''))

@php
    // Helper function for grade calculation
    function calculateGrade($percentage) {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }
@endphp

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-chart-line text-purple-600"></i>
                Compare Tests
            </h1>
            @if(isset($selectedStudent))
                <p class="text-sm text-slate-500">{{ $selectedStudent->first_name }} {{ $selectedStudent->last_name }} - {{ $selectedStudent->admission_number }}</p>
            @else
                <p class="text-sm text-slate-500">Select a student to compare tests</p>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.exam-results.student-wise', ['student_id' => $selectedStudent->id ?? '']) }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl px-5 py-2.5 shadow-md shadow-indigo-100 hover:bg-indigo-700 transition-all">
                <i class="fas fa-user-graduate"></i> Student View
            </a>
            <a href="{{ route('admin.exam-results.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl px-5 py-2.5 hover:bg-slate-50 transition-all">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.exam-results.compare-tests') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Select Student <span class="text-red-500">*</span></label>
                <select name="student_id" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" required>
                    <option value="">Search student...</option>
                    @foreach($students ?? [] as $student)
                        <option value="{{ $student->id }}" {{ isset($selectedStudent) && $selectedStudent->id == $student->id ? 'selected' : '' }}>
                            {{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Academic Session</label>
                <select name="session_id" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">All Sessions</option>
                    @foreach($academicSessions ?? [] as $session)
                        <option value="{{ $session->id }}" {{ isset($sessionId) && $sessionId == $session->id ? 'selected' : '' }}>
                            {{ $session->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 bg-purple-600 text-white text-sm font-semibold rounded-xl px-6 py-2.5 shadow-md shadow-purple-100 hover:bg-purple-700 transition-all">
                <i class="fas fa-chart-line"></i> Compare
            </button>
        </form>
    </div>

    @if(isset($selectedStudent))
        <!-- Overall Test Comparison -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-chart-bar text-purple-500"></i>
                <span>Overall Test Performance</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($testResults as $test)
                    @php
                        $color = $test->percentage >= 80 ? 'emerald' : ($test->percentage >= 60 ? 'amber' : 'rose');
                        $examName = strlen($test->exam_name) > 15 ? substr($test->exam_name, 0, 15) . '...' : $test->exam_name;
                    @endphp
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-slate-800 text-sm" title="{{ $test->exam_name }}">{{ $examName }}</h4>
                            <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($test->start_date)->format('d M') }}</span>
                        </div>
                        <div class="text-center py-2">
                            <div class="text-3xl font-black text-{{ $color }}-600">{{ number_format($test->percentage, 1) }}%</div>
                            <div class="text-xs text-slate-400">{{ number_format($test->total_marks, 1) }}/{{ number_format($test->total_max_marks, 1) }}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-1 text-xs mt-2">
                            <div>
                                <p class="text-slate-400">Subjects</p>
                                <p class="font-bold text-slate-800">{{ $test->subject_count }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Grade</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600">
                                    {{ calculateGrade($test->percentage) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Subject-wise Comparison Across Tests -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-book text-purple-500"></i>
                <span>Subject-wise Comparison</span>
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4 text-center">Tests</th>
                            <th class="py-3 px-4 text-center">Avg Marks</th>
                            <th class="py-3 px-4 text-center">Avg %</th>
                            <th class="py-3 px-4 text-center">Highest</th>
                            <th class="py-3 px-4 text-center">Lowest</th>
                            <th class="py-3 px-4 text-center">Grade</th>
                            <th class="py-3 px-4 text-center">Trend</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                        @foreach($subjectComparison as $subject)
                            @php
                                $color = $subject->avg_percentage >= 80 ? 'emerald' : ($subject->avg_percentage >= 60 ? 'amber' : 'rose');
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-800">{{ $subject->subject_name }}</td>
                                <td class="py-3 px-4 text-center">{{ $subject->test_count }}</td>
                                <td class="py-3 px-4 text-center">{{ number_format($subject->avg_marks, 1) }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $color }}-50 text-{{ $color }}-600 border border-{{ $color }}-100">
                                        {{ number_format($subject->avg_percentage, 1) }}%
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-emerald-600">{{ $subject->highest_marks }}</td>
                                <td class="py-3 px-4 text-center font-bold text-rose-500">{{ $subject->lowest_marks }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                        {{ calculateGrade($subject->avg_percentage) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($subject->test_count > 1)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold">
                                            <i class="fas fa-arrow-up text-emerald-500"></i>
                                            <span class="text-slate-500">Improving</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- No Student Selected -->
        <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center shadow-sm">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-purple-50 text-purple-400 mb-4">
                <i class="fas fa-chart-line text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700">Select a Student</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto mt-2">Choose a student from the dropdown above and click "Compare" to see test comparisons.</p>
        </div>
    @endif

</div>
@endsection