@extends('layouts.app')

@section('title', 'Complete Student Report - ' . $student->full_name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- ===== ACTIONS BAR ===== -->
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6 no-print">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span class="text-2xl">📊</span> Complete Student Report
            </h1>
            <p class="text-sm text-slate-500">{{ $student->full_name }} - {{ $student->admission_number }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-medium flex items-center gap-2 shadow-sm">
                <span>🖨️</span> Print
            </button>
            <a href="{{ route('admin.reports.student.export-pdf', ['student_id' => $student->id, 'session_id' => $sessionId ?? '']) }}" 
               class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition text-sm font-medium flex items-center gap-2 shadow-sm">
                <span>📄</span> PDF
            </a>
            <a href="{{ route('admin.reports.student.dashboard') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium flex items-center gap-2">
                <span>←</span> Back
            </a>
        </div>
    </div>

    <!-- ===== REPORT CARD ===== -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden">

        <!-- ===== HEADER ===== -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white p-8 text-center relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <div class="text-4xl mb-2">🏫</div>
                <h1 class="text-3xl font-extrabold">School Management System</h1>
                <p class="text-indigo-200 mt-1 text-lg">Complete Student Report</p>
                <div class="mt-4 flex flex-wrap justify-center gap-3 text-sm">
                    <span class="bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/10">
                        📅 {{ $academicSession->name ?? 'Current Session' }}
                    </span>
                    <span class="bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/10">
                        🆔 {{ $student->admission_number }}
                    </span>
                    @if($dateRangeText && $dateRangeText != 'All Time')
                        <span class="bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/10">
                            📆 {{ $dateRangeText }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- ===== STUDENT INFO ===== -->
        <div class="p-6 border-b border-slate-100">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-4">
                <div class="flex-shrink-0">
                    @if($student->profile_photo)
                        <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->full_name }}" class="w-20 h-20 rounded-full border-2 border-indigo-200 object-cover">
                    @else
                        <div class="w-20 h-20 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-3xl font-bold text-white shadow-lg">
                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-2xl font-extrabold text-slate-800">{{ $student->full_name }}</h2>
                    <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-1">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold {{ $student->status == 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $student->status == 'Active' ? '✅ Active' : '❌ Inactive' }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold {{ $student->gender == 'Male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                            {{ $student->gender }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                            🎂 {{ $student->age }} years
                        </span>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700">
                            📋 {{ $student->roll_number ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="mt-2 text-sm text-slate-500">
                        <span class="inline-flex items-center gap-1 mr-4">📚 {{ $student->current_class }}</span>
                        <span class="inline-flex items-center gap-1">📅 {{ $student->admission_date->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== QUICK STATS ===== -->
        <div class="p-6 border-b border-slate-100">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Attendance</p>
                    <p class="text-2xl font-extrabold {{ $stats['attendance_percentage'] >= 80 ? 'text-emerald-600' : ($stats['attendance_percentage'] >= 60 ? 'text-amber-500' : 'text-red-500') }}">
                        {{ number_format($stats['attendance_percentage'], 1) }}%
                    </p>
                    <div class="w-full h-2 bg-slate-200 rounded-full mt-2 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-1000 {{ $stats['attendance_percentage'] >= 80 ? 'bg-emerald-500' : ($stats['attendance_percentage'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }}" 
                             style="width: {{ $stats['attendance_percentage'] }}%"></div>
                    </div>
                </div>
                <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Average Marks</p>
                    <p class="text-2xl font-extrabold text-indigo-600">{{ number_format($stats['avg_percentage'], 1) }}%</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Overall Grade</p>
                    <div class="w-20 h-20 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center text-3xl font-extrabold text-white mx-auto border-4 border-white/20 shadow-lg hover:scale-105 transition-transform duration-300">
                        {{ $overallGrade }}
                    </div>
                </div>
                <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Exams Taken</p>
                    <p class="text-2xl font-extrabold text-slate-800">{{ $stats['total_exams'] }}</p>
                    <p class="text-xs text-slate-400 mt-1">Total Exams</p>
                </div>
            </div>
        </div>

        <!-- ===== PERSONAL INFORMATION ===== -->
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3">
                <span class="text-xl">👤</span> Personal Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">First Name</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->first_name }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Last Name</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->last_name }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Date of Birth</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->date_of_birth->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Gender</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->gender }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Religion</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->religion ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Blood Group</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->blood_group ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Mother Tongue</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->mother_tongue ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Birth Place</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->birth_place ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Previous School</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->previous_school_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Previous Class</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->previous_class ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== CONTACT & ACADEMIC ===== -->
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3">
                <span class="text-xl">📋</span> Contact & Academic
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Email</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Phone</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Address</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->address ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">City</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->city ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">State</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->state ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Country</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->country ?? 'Pakistan' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Admission Date</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->admission_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-sm text-slate-400 font-medium">Student Type</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $student->student_type }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== PARENTS INFORMATION ===== -->
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3">
                <span class="text-xl">👨‍👩‍👧</span> Parents Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 hover:border-indigo-200 transition-all duration-300">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl">👨</span>
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Father</p>
                    </div>
                    <p class="text-lg font-semibold text-slate-800">{{ $student->father_name }}</p>
                    <div class="flex flex-wrap gap-3 mt-1 text-sm text-slate-600">
                        <span class="inline-flex items-center gap-1">💼 {{ $student->father_occupation ?? 'N/A' }}</span>
                        <span class="inline-flex items-center gap-1">📞 {{ $student->father_phone ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 hover:border-indigo-200 transition-all duration-300">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl">👩</span>
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Mother</p>
                    </div>
                    <p class="text-lg font-semibold text-slate-800">{{ $student->mother_name ?? 'N/A' }}</p>
                    <div class="flex flex-wrap gap-3 mt-1 text-sm text-slate-600">
                        <span class="inline-flex items-center gap-1">💼 {{ $student->mother_occupation ?? 'N/A' }}</span>
                        <span class="inline-flex items-center gap-1">📞 {{ $student->mother_phone ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== ATTENDANCE SUMMARY ===== -->
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3">
                <span class="text-xl">📅</span> Attendance Summary
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                    <p class="text-xs text-slate-400 font-medium">Total Days</p>
                    <p class="text-xl font-extrabold text-slate-800">{{ $attendanceSummary['total'] }}</p>
                </div>
                <div class="bg-emerald-50 rounded-xl p-3 text-center border border-emerald-200">
                    <p class="text-xs text-emerald-400 font-medium">Present</p>
                    <p class="text-xl font-extrabold text-emerald-600">{{ $attendanceSummary['present'] }}</p>
                </div>
                <div class="bg-red-50 rounded-xl p-3 text-center border border-red-200">
                    <p class="text-xs text-red-400 font-medium">Absent</p>
                    <p class="text-xl font-extrabold text-red-500">{{ $attendanceSummary['absent'] }}</p>
                </div>
                <div class="bg-amber-50 rounded-xl p-3 text-center border border-amber-200">
                    <p class="text-xs text-amber-400 font-medium">Leave</p>
                    <p class="text-xl font-extrabold text-amber-500">{{ $attendanceSummary['leave'] }}</p>
                </div>
                <div class="bg-indigo-50 rounded-xl p-3 text-center border border-indigo-200">
                    <p class="text-xs text-indigo-400 font-medium">Percentage</p>
                    <p class="text-xl font-extrabold text-indigo-600">{{ number_format($attendanceSummary['percentage'], 1) }}%</p>
                </div>
            </div>

            @if($attendanceMonthly->isNotEmpty())
                <div class="mt-4">
                    <p class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                        <span>📊</span> Monthly Breakdown
                        <span class="text-xs font-normal text-slate-400">({{ $attendanceMonthly->count() }} months)</span>
                    </p>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        @foreach($attendanceMonthly as $month => $data)
                            @php
                                $color = $data['percentage'] >= 80 ? 'emerald' : ($data['percentage'] >= 60 ? 'amber' : 'red');
                            @endphp
                            <div class="bg-slate-50 rounded-xl p-3 text-center border border-{{ $color }}-200 hover:shadow-md transition-all duration-300">
                                <div class="text-xs font-semibold text-slate-500">{{ $month }}</div>
                                <div class="text-lg font-extrabold text-{{ $color }}-600">{{ number_format($data['percentage'], 1) }}%</div>
                                <div class="text-xs text-slate-400 mt-1">
                                    <span class="text-emerald-600">{{ $data['present'] }}</span>P /
                                    <span class="text-red-500">{{ $data['absent'] }}</span>A /
                                    <span class="text-amber-500">{{ $data['leave'] }}</span>L
                                </div>
                                <div class="w-full h-1.5 bg-slate-200 rounded-full mt-2 overflow-hidden">
                                    <div class="h-full rounded-full bg-{{ $color }}-500 transition-all duration-1000" style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- ===== SUBJECT PERFORMANCE ===== -->
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3">
                <span class="text-xl">📚</span> Subject-wise Performance
            </h3>
            @if($subjectPerformance->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-600 text-xs uppercase tracking-wider">Subject</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Tests</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Avg Marks</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Percentage</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Passed</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Grade</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($subjectPerformance as $subject)
                                @php
                                    $grade = $subject['grade'];
                                    $color = $subject['percentage'] >= 80 ? 'emerald' : ($subject['percentage'] >= 60 ? 'amber' : 'red');
                                    $badgeBg = $grade == 'A+' ? 'bg-emerald-100 text-emerald-700' : 
                                               ($grade == 'A' ? 'bg-emerald-50 text-emerald-600' : 
                                               ($grade == 'B+' ? 'bg-blue-100 text-blue-700' : 
                                               ($grade == 'B' ? 'bg-blue-50 text-blue-600' : 
                                               ($grade == 'C' ? 'bg-amber-100 text-amber-700' : 
                                               ($grade == 'D' ? 'bg-amber-50 text-amber-600' : 
                                               'bg-red-100 text-red-700')))));
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-2.5 font-medium text-slate-800">{{ $subject['subject']->name }}</td>
                                    <td class="px-4 py-2.5 text-center">{{ $subject['total'] }}</td>
                                    <td class="px-4 py-2.5 text-center">{{ number_format($subject['avg_marks'], 1) }}</td>
                                    <td class="px-4 py-2.5 text-center font-bold text-{{ $color }}-600">{{ number_format($subject['percentage'], 1) }}%</td>
                                    <td class="px-4 py-2.5 text-center">
                                        {{ $subject['passed'] }}/{{ $subject['total'] }}
                                        <span class="text-xs text-slate-400">({{ number_format($subject['pass_percentage'], 1) }}%)</span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeBg }}">
                                            {{ $grade }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-slate-400">
                    <span class="text-3xl block mb-2">📭</span>
                    <p>No subject performance data available</p>
                </div>
            @endif
        </div>

        <!-- ===== EXAM RESULTS ===== -->
        @if($examPerformance->isNotEmpty())
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3">
                <span class="text-xl">📊</span> Recent Exam Results
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 py-2.5 text-left font-semibold text-slate-600 text-xs uppercase tracking-wider">Exam</th>
                            <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Marks</th>
                            <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Percentage</th>
                            <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Grade</th>
                            <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Rank</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($examPerformance as $result)
                            @php
                                $color = $result->percentage >= 80 ? 'emerald' : ($result->percentage >= 60 ? 'amber' : 'red');
                                $grade = $result->grade;
                                $badgeBg = $grade == 'A+' ? 'bg-emerald-100 text-emerald-700' : 
                                           ($grade == 'A' ? 'bg-emerald-50 text-emerald-600' : 
                                           ($grade == 'B+' ? 'bg-blue-100 text-blue-700' : 
                                           ($grade == 'B' ? 'bg-blue-50 text-blue-600' : 
                                           ($grade == 'C' ? 'bg-amber-100 text-amber-700' : 
                                           ($grade == 'D' ? 'bg-amber-50 text-amber-600' : 
                                           'bg-red-100 text-red-700')))));
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $result->exam->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-center">{{ number_format($result->total_marks, 1) }}/{{ number_format($result->total_max_marks, 1) }}</td>
                                <td class="px-4 py-2.5 text-center font-bold text-{{ $color }}-600">{{ number_format($result->percentage, 1) }}%</td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeBg }}">
                                        {{ $grade }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-center">{{ $result->rank_in_class ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- ===== FEE SUMMARY ===== -->
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3">
                <span class="text-xl">💰</span> Fee Summary
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                    <p class="text-xs text-slate-400 font-medium">Total Fee</p>
                    <p class="text-xl font-extrabold text-slate-800">${{ number_format($feeSummary['total'], 2) }}</p>
                </div>
                <div class="bg-emerald-50 rounded-xl p-3 text-center border border-emerald-200">
                    <p class="text-xs text-emerald-400 font-medium">Paid</p>
                    <p class="text-xl font-extrabold text-emerald-600">${{ number_format($feeSummary['paid'], 2) }}</p>
                </div>
                <div class="bg-red-50 rounded-xl p-3 text-center border border-red-200">
                    <p class="text-xs text-red-400 font-medium">Pending</p>
                    <p class="text-xl font-extrabold text-red-500">${{ number_format($feeSummary['pending'], 2) }}</p>
                </div>
                <div class="bg-amber-50 rounded-xl p-3 text-center border border-amber-200">
                    <p class="text-xs text-amber-400 font-medium">Overdue</p>
                    <p class="text-xl font-extrabold text-amber-500">{{ $feeSummary['overdue'] }}</p>
                </div>
            </div>

            @if($feeInstallments->isNotEmpty())
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-600 text-xs uppercase tracking-wider">Fee Type</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Due Date</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($feeInstallments as $installment)
                                @php
                                    $statusBg = $installment->status == 'paid' ? 'bg-emerald-100 text-emerald-700' : 
                                                ($installment->status == 'partial' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                                    $statusLabel = $installment->status == 'paid' ? '✅ Paid' : 
                                                  ($installment->status == 'partial' ? '🔄 Partial' : '⏳ Pending');
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-2.5">{{ $installment->feeSubmissionType->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2.5 text-center">${{ number_format($installment->amount, 2) }}</td>
                                    <td class="px-4 py-2.5 text-center">{{ $installment->due_date->format('d M Y') }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBg }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- ===== FEE PAYMENT HISTORY ===== -->
        @if(isset($paymentHistory) && $paymentHistory->isNotEmpty())
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3">
                <span class="text-xl">📋</span> Payment History
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 py-2.5 text-left font-semibold text-slate-600 text-xs uppercase tracking-wider">Date</th>
                            <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Type</th>
                            <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Receipt #</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($paymentHistory as $payment)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-2.5">{{ $payment['date']->format('d M Y') }}</td>
                                <td class="px-4 py-2.5 text-center font-semibold text-emerald-600">${{ number_format($payment['amount'], 2) }}</td>
                                <td class="px-4 py-2.5 text-center">{{ $payment['type'] }}</td>
                                <td class="px-4 py-2.5 text-center">{{ $payment['receipt'] ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- ===== PREVIOUS FEE INSTALLMENTS ===== -->
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3">
                <span class="text-xl">📋</span> Previous Fee Installments
                <span class="text-xs font-normal text-slate-400 ml-2">(All time)</span>
            </h3>
            
            @if(isset($allFeeInstallments) && $allFeeInstallments->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-600 text-xs uppercase tracking-wider">Fee Type</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Installment</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Due Date</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Paid</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($allFeeInstallments as $installment)
                                @php
                                    $statusBg = $installment->status == 'paid' ? 'bg-emerald-100 text-emerald-700' : 
                                                ($installment->status == 'partial' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                                    $statusLabel = $installment->status == 'paid' ? '✅ Paid' : 
                                                  ($installment->status == 'partial' ? '🔄 Partial' : '⏳ Pending');
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-2.5">{{ $installment->feeSubmissionType->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2.5 text-center">#{{ $installment->installment_number }}</td>
                                    <td class="px-4 py-2.5 text-center font-semibold text-slate-800">${{ number_format($installment->amount, 2) }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="{{ $installment->due_date < now() && $installment->status != 'paid' ? 'text-red-500 font-semibold' : 'text-slate-600' }}">
                                            {{ $installment->due_date->format('d M Y') }}
                                            @if($installment->due_date < now() && $installment->status != 'paid')
                                                <span class="text-xs text-red-400 ml-1">(Overdue)</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center font-semibold text-emerald-600">${{ number_format($installment->paid_amount, 2) }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBg }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 text-slate-400">
                    <span class="text-2xl block mb-2">📋</span>
                    <p>No previous fee installments found</p>
                </div>
            @endif
        </div>

        <!-- ===== CERTIFICATES ===== -->
        @if(isset($certificates) && $certificates->isNotEmpty())
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3">
                <span class="text-xl">📜</span> Certificates
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($certificates as $cert)
                    <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-4 text-center border border-amber-200 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                        <div class="text-3xl mb-2">🏅</div>
                        <p class="font-semibold text-slate-800">{{ $cert->certificateType->title ?? 'Certificate' }}</p>
                        <p class="text-xs text-slate-400">Issued: {{ $cert->issue_date->format('d M Y') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- ============================================= -->
        <!-- ===== TIMETABLE SECTION ===== -->
        <!-- ============================================= -->
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3">
                <span class="text-xl">⏰</span> Weekly Timetable
                <span class="text-xs font-normal text-slate-400 ml-2">({{ $student->current_class }})</span>
            </h3>
            
            @if(isset($timetable) && $timetable->isNotEmpty() && isset($timeSlots) && $timeSlots->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider border border-slate-200">Time</th>
                                <th class="px-3 py-2 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider border border-slate-200">Monday</th>
                                <th class="px-3 py-2 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider border border-slate-200">Tuesday</th>
                                <th class="px-3 py-2 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider border border-slate-200">Wednesday</th>
                                <th class="px-3 py-2 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider border border-slate-200">Thursday</th>
                                <th class="px-3 py-2 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider border border-slate-200">Friday</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                            @endphp
                            @foreach($timeSlots as $slot)
                                <tr>
                                    <td class="px-3 py-2 text-xs font-medium text-slate-600 border border-slate-200 bg-slate-50/50 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - 
                                        {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                    </td>
                                    @foreach($days as $day)
                                        @php
                                            $entry = $timetable[$day] ?? collect();
                                            $entry = $entry->firstWhere('time_slot_id', $slot->id);
                                        @endphp
                                        <td class="px-3 py-2 text-center border border-slate-200 min-w-[100px]">
                                            @if($entry)
                                                <div class="bg-white rounded-lg p-2 border border-indigo-100 hover:shadow-md transition-all duration-200">
                                                    <div class="font-semibold text-slate-800 text-xs">{{ $entry->subject->name ?? 'N/A' }}</div>
                                                    <div class="text-xs text-slate-500 mt-0.5">👨‍🏫 {{ $entry->teacher->first_name ?? '' }} {{ $entry->teacher->last_name ?? '' }}</div>
                                                    <div class="text-xs text-slate-400">🚪 {{ $entry->room->name ?? 'N/A' }}</div>
                                                    <div class="mt-1">
                                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-medium bg-indigo-50 text-indigo-600 border border-indigo-100">
                                                            {{ $entry->classSection->section_name ?? '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-slate-300 text-xs py-2">−</div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Timetable Summary -->
                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-indigo-50 rounded-xl p-3 text-center border border-indigo-100">
                        <p class="text-xs text-slate-500 font-medium">Total Subjects</p>
                        <p class="text-lg font-extrabold text-indigo-600">
                            {{ $timetable->flatMap(function($items) { return $items; })->unique('subject_id')->count() }}
                        </p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-3 text-center border border-emerald-100">
                        <p class="text-xs text-slate-500 font-medium">Periods/Week</p>
                        <p class="text-lg font-extrabold text-emerald-600">
                            {{ $timetable->flatMap(function($items) { return $items; })->count() }}
                        </p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-3 text-center border border-amber-100">
                        <p class="text-xs text-slate-500 font-medium">Teachers</p>
                        <p class="text-lg font-extrabold text-amber-600">
                            {{ $timetable->flatMap(function($items) { return $items; })->unique('teacher_id')->count() }}
                        </p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-3 text-center border border-purple-100">
                        <p class="text-xs text-slate-500 font-medium">Section</p>
                        <p class="text-lg font-extrabold text-purple-600">
                            {{ $student->classSection->section_name ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <!-- Timetable Legend -->
                <div class="mt-3 flex flex-wrap items-center gap-4 text-xs text-slate-500">
                    <span class="font-medium">Legend:</span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 bg-white border border-slate-300 rounded"></span>
                        <span>Class</span>
                    </span>
                    <span class="flex items-center gap-1 text-indigo-600">
                        <span class="text-sm">ℹ️</span>
                        <span>Hover for details</span>
                    </span>
                </div>
            @else
                <div class="text-center py-8 text-slate-400">
                    <span class="text-3xl block mb-2">⏰</span>
                    <p>No timetable assigned for this student's class yet.</p>
                    <p class="text-xs text-slate-400 mt-1">Please generate timetable or assign classes.</p>
                    <a href="{{ route('admin.timetable-reports.generate') }}" class="mt-3 inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition no-print">
                        ⚙️ Generate Timetable
                    </a>
                </div>
            @endif
        </div>

        <!-- ===== FOOTER ===== -->
        <div class="p-6 bg-slate-50">
            <div class="rounded-xl p-4 text-center text-xs text-slate-400 border border-slate-200 bg-white">
                <p class="flex flex-wrap items-center justify-center gap-2">
                    <span>🖨️</span> Generated on {{ now()->format('d M Y, h:i A') }}
                    <span class="text-slate-300 hidden sm:inline">|</span>
                    <span>📋 Report ID: #{{ $student->id }}-{{ now()->format('Ymd') }}</span>
                    <span class="text-slate-300 hidden sm:inline">|</span>
                    <span class="text-emerald-600 font-medium">✓ System Verified</span>
                </p>
                <p class="mt-1">This is a system-generated report. Please verify all details before official use.</p>
            </div>
        </div>

    </div>
</div>
@endsection