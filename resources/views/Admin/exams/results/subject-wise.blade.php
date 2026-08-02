@extends('layouts.app')

@section('title', 'Subject Wise Results' . (isset($selectedSubject) ? ' - ' . $selectedSubject->name : ''))

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-book text-emerald-600"></i>
                Subject Wise Results
            </h1>
            @if(isset($selectedSubject))
                <p class="text-sm text-slate-500">{{ $selectedSubject->name }} ({{ $selectedSubject->code }})</p>
            @else
                <p class="text-sm text-slate-500">Select a subject to view results</p>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.exam-results.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl px-5 py-2.5 hover:bg-slate-50 transition-all">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.exam-results.subject-wise') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Select Subject <span class="text-red-500">*</span></label>
                <select name="subject_id" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" required>
                    <option value="">Search subject...</option>
                    @foreach($subjects ?? [] as $subject)
                        <option value="{{ $subject->id }}" {{ isset($selectedSubject) && $selectedSubject->id == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }} ({{ $subject->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Exam</label>
                <select name="exam_id" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">All Exams</option>
                    @foreach($exams ?? [] as $exam)
                        <option value="{{ $exam->id }}" {{ isset($examId) && $examId == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }}
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
            <button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl px-6 py-2.5 shadow-md shadow-emerald-100 hover:bg-emerald-700 transition-all">
                <i class="fas fa-search"></i> View Results
            </button>
        </form>
    </div>

    @if(isset($selectedSubject))
        <!-- Grade Distribution -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-emerald-500"></i>
                <span>Grade Distribution</span>
            </h3>
            <div class="grid grid-cols-3 md:grid-cols-7 gap-3">
                @foreach($gradeDistribution as $grade => $count)
                    @php
                        $color = $grade == 'F' ? 'rose' : ($grade == 'A+' || $grade == 'A' ? 'emerald' : 'indigo');
                    @endphp
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-center">
                        <div class="text-2xl font-black text-{{ $color }}-600">{{ $grade }}</div>
                        <div class="text-lg font-bold text-slate-800">{{ $count }}</div>
                        <div class="text-xs text-slate-400">Students</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section-wise Performance -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-layer-group text-emerald-500"></i>
                <span>Section-wise Performance</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($sectionWise as $section)
                    @php
                        $color = $section->pass_percentage >= 80 ? 'emerald' : ($section->pass_percentage >= 60 ? 'amber' : 'rose');
                    @endphp
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-slate-800">{{ $section->grade_name }} - {{ $section->section_name }}</h4>
                            <span class="text-xs bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full">{{ $section->student_count }} students</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <p class="text-slate-400 text-xs">Avg Marks</p>
                                <p class="font-bold text-slate-800">{{ number_format($section->avg_marks, 1) }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs">Pass %</p>
                                <p class="font-bold text-{{ $color }}-600">{{ number_format($section->pass_percentage, 1) }}%</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs">Highest</p>
                                <p class="font-bold text-emerald-600">{{ $section->highest_marks }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs">Lowest</p>
                                <p class="font-bold text-rose-500">{{ $section->lowest_marks }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Student Performance -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-user-graduate text-emerald-500"></i>
                <span>Student Performance</span>
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-4">Student</th>
                            <th class="py-3 px-4">Section</th>
                            <th class="py-3 px-4 text-center">Roll No</th>
                            <th class="py-3 px-4 text-center">Tests</th>
                            <th class="py-3 px-4 text-center">Avg Marks</th>
                            <th class="py-3 px-4 text-center">Avg %</th>
                            <th class="py-3 px-4 text-center">Passed</th>
                            <th class="py-3 px-4 text-center">Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                        @foreach($studentPerformance as $student)
                            @php
                                $color = $student->avg_percentage >= 80 ? 'emerald' : ($student->avg_percentage >= 60 ? 'amber' : 'rose');
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td class="py-3 px-4">{{ $student->section_name }}</td>
                                <td class="py-3 px-4 text-center">{{ $student->roll_number ?? 'N/A' }}</td>
                                <td class="py-3 px-4 text-center">{{ $student->test_count }}</td>
                                <td class="py-3 px-4 text-center">{{ number_format($student->avg_marks, 1) }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $color }}-50 text-{{ $color }}-600 border border-{{ $color }}-100">
                                        {{ number_format($student->avg_percentage, 1) }}%
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center text-emerald-600">{{ $student->passed_count }}/{{ $student->test_count }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                        {{ $student->grade }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- No Subject Selected -->
        <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center shadow-sm">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-50 text-emerald-400 mb-4">
                <i class="fas fa-book text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700">Select a Subject</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto mt-2">Choose a subject from the dropdown above and click "View Results" to see the subject-wise analysis.</p>
        </div>
    @endif

</div>
@endsection