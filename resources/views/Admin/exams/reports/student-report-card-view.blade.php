@extends('layouts.app')

@section('title', 'Student Report Card - ' . $student->first_name)

@section('content')

<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6 print:p-0 print:max-w-full">
    
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-id-card text-indigo-600"></i>
                <span>Student Report Card</span>
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $student->first_name }} {{ $student->last_name }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl px-5 py-2.5 shadow-md shadow-indigo-100 hover:bg-indigo-700 transition-all">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('admin.student-report-card.export-pdf', ['student_id' => $student->id, 'session_id' => request('session_id'), 'exam_id' => request('exam_id'), 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}"
               class="inline-flex items-center justify-center gap-2 bg-rose-600 text-white text-sm font-semibold rounded-xl px-5 py-2.5 shadow-md shadow-rose-100 hover:bg-rose-700 transition-all">
                <i class="fas fa-file-pdf"></i>
                <span>PDF</span>
            </a>
            <a href="{{ route('admin.student-report-card.index') }}" class="inline-flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl px-5 py-2.5 hover:bg-slate-50 transition-all">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>
    </div>

    <!-- Report Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden print:border print:border-slate-300 print:shadow-none print:rounded-none">
        
        <!-- Header -->
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-purple-700 p-6 md:p-8 print:from-indigo-600 print:to-indigo-600 print:p-6" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none print:hidden"></div>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl md:text-2xl font-bold text-white tracking-tight flex items-center gap-2.5">
                        <i class="fas fa-graduation-cap"></i>
                        <span>School Management System</span>
                    </h2>
                    <p class="text-white/80 text-sm font-medium">Student Academic Report Card</p>
                    <div class="pt-2 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 bg-white/10 border border-white/10 text-white text-xs font-semibold px-3.5 py-1.5 rounded-full shadow-sm" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                            <i class="fas fa-calendar-alt opacity-70"></i>
                            {{ $academicSession->name ?? 'Academic Year ' . date('Y') }}
                        </span>
                        @if(isset($dateRangeText) && $dateRangeText)
                        <span class="inline-flex items-center gap-1.5 bg-emerald-500/30 border border-emerald-400/30 text-white text-xs font-semibold px-3.5 py-1.5 rounded-full shadow-sm" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                            <i class="fas fa-clock opacity-70"></i>
                            {{ $dateRangeText }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="md:text-end">
                    <span class="inline-flex items-center gap-1.5 bg-white/10 border border-white/10 text-white text-xs font-mono font-bold tracking-wider px-3.5 py-1.5 rounded-full shadow-sm" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                        <i class="fas fa-qrcode opacity-70"></i>
                        {{ $student->admission_number }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Student Info -->
        <div class="p-6 md:p-8 bg-slate-50/50 border-b border-slate-100 grid grid-cols-2 md:grid-cols-5 gap-5 print:bg-slate-50 print:p-6 print:border-slate-300">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Student Name</p>
                <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $student->first_name }} {{ $student->last_name }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Admission No</p>
                <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $student->admission_number }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Class</p>
                <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $student->classSection->class->grade->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Section</p>
                <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $student->classSection->section_name ?? 'N/A' }}</p>
            </div>
            <div class="col-span-2 md:col-span-1">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Roll No</p>
                <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $student->roll_number ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Overall Performance -->
        <div class="p-6 md:p-8 border-b border-slate-100 print:p-6 print:border-slate-300">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fas fa-chart-line text-indigo-500"></i>
                <span>Overall Performance Summary</span>
            </h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-center print:border-slate-200" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                    <div class="text-indigo-500 text-lg mb-1"><i class="fas fa-star"></i></div>
                    <div class="text-xl font-black text-slate-800 tracking-tight">{{ number_format($overallPerformance['total_marks'], 1) }}</div>
                    <div class="text-xs font-semibold text-slate-400 mt-0.5">Total Marks</div>
                    <div class="text-[10px] text-slate-400 mt-1">Out of {{ number_format($overallPerformance['total_max_marks'], 1) }}</div>
                </div>

                @php
                    $isHigh = $overallPerformance['average_percentage'] >= 80;
                    $isMid = $overallPerformance['average_percentage'] >= 60;
                    $percentColorClass = $isHigh ? 'text-emerald-600' : ($isMid ? 'text-amber-500' : 'text-rose-500');
                    $percentIconColor = $isHigh ? 'text-emerald-500' : ($isMid ? 'text-amber-400' : 'text-rose-400');
                @endphp
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-center print:border-slate-200" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                    <div class="{{ $percentIconColor }} text-lg mb-1"><i class="fas fa-percentage"></i></div>
                    <div class="text-xl font-black {{ $percentColorClass }} tracking-tight">{{ number_format($overallPerformance['average_percentage'], 1) }}%</div>
                    <div class="text-xs font-semibold text-slate-400 mt-0.5">Average Percentage</div>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-center print:border-slate-200" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                    <div class="text-indigo-500 text-lg mb-1"><i class="fas fa-award"></i></div>
                    <div class="text-xl font-black text-indigo-600 tracking-tight">{{ $overallPerformance['overall_grade'] }}</div>
                    <div class="text-xs font-semibold text-slate-400 mt-0.5">Overall Grade</div>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-center print:border-slate-200" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                    <div class="text-amber-500 text-lg mb-1"><i class="fas fa-file-alt"></i></div>
                    <div class="text-xl font-black text-slate-800 tracking-tight">{{ $overallPerformance['total_exams'] }}</div>
                    <div class="text-xs font-semibold text-slate-400 mt-0.5">Exams Attempted</div>
                </div>
            </div>
        </div>

        <!-- Subject-wise Results -->
        <div class="p-6 md:p-8 border-b border-slate-100 print:p-6 print:border-slate-300">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fas fa-book text-indigo-500"></i>
                <span>Subject-wise Performance</span>
            </h3>
            <div class="overflow-x-auto ring-1 ring-slate-100 rounded-xl print:ring-0">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100 print:bg-slate-100 print:border-slate-300" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4 text-center">Exams</th>
                            <th class="py-3 px-4 text-center">Avg Marks</th>
                            <th class="py-3 px-4 text-center">Avg %</th>
                            <th class="py-3 px-4 text-center">Passed</th>
                            <th class="py-3 px-4 text-center">Failed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-600 print:divide-slate-200">
                        @forelse($subjectResults as $subject)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-800">{{ $subject->subject_name }}</span>
                                    <span class="block text-[11px] text-slate-400 font-medium tracking-wide mt-0.5">{{ $subject->subject_code }}</span>
                                </td>
                                <td class="py-3 px-4 text-center font-semibold text-slate-700">{{ $subject->exam_count }}</td>
                                <td class="py-3 px-4 text-center font-bold text-slate-800">{{ number_format($subject->average_marks, 1) }}</td>
                                <td class="py-3 px-4 text-center">
                                    @php
                                        $subHigh = $subject->average_percentage >= 80;
                                        $subMid = $subject->average_percentage >= 60;
                                        $badgeStyle = $subHigh ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($subMid ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-rose-50 text-rose-700 border-rose-100');
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full border {{ $badgeStyle }}" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                        {{ number_format($subject->average_percentage, 1) }}%
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-emerald-600">{{ $subject->passed_count }}</td>
                                <td class="py-3 px-4 text-center font-bold text-rose-500">{{ $subject->failed_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 font-medium">
                                    <i class="fas fa-info-circle mr-1.5 text-slate-300"></i>No subject results available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Attendance Report -->
        <div class="p-6 md:p-8 border-b border-slate-100 print:p-6 print:border-slate-300">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fas fa-calendar-check text-indigo-500"></i>
                <span>Attendance Report</span>
                @if(isset($dateRangeText) && $dateRangeText)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-100 text-indigo-700 border border-indigo-200" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                        <i class="fas fa-clock mr-1"></i>
                        {{ $dateRangeText }}
                    </span>
                @endif
            </h3>
            
            @if(isset($attendanceData['has_data']) && $attendanceData['has_data'])
                <!-- Date Range Info -->
                @if(isset($attendanceData['date_range_display']))
                    <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl px-4 py-2.5 mb-5 flex items-center gap-2" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                        <i class="fas fa-calendar-alt text-indigo-500"></i>
                        <span class="text-sm font-medium text-slate-700">
                            <span class="text-slate-500 font-normal">Attendance Period:</span> 
                            {{ $attendanceData['date_range_display'] }}
                        </span>
                    </div>
                @endif

                <!-- Attendance Summary -->
                <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-6">
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 text-center print:border-slate-200" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                        <div class="text-base font-black text-slate-800 tracking-tight">{{ $attendanceData['total_days'] }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Total</div>
                    </div>
                    <div class="bg-emerald-50/40 border border-emerald-100/50 rounded-xl p-3 text-center print:border-slate-200 print:bg-slate-50" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                        <div class="text-base font-black text-emerald-600 tracking-tight">{{ $attendanceData['present_days'] }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Present</div>
                    </div>
                    <div class="bg-rose-50/40 border border-rose-100/50 rounded-xl p-3 text-center print:border-slate-200 print:bg-slate-50" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                        <div class="text-base font-black text-rose-500 tracking-tight">{{ $attendanceData['absent_days'] }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Absent</div>
                    </div>
                    <div class="bg-amber-50/40 border border-amber-100/50 rounded-xl p-3 text-center print:border-slate-200 print:bg-slate-50" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                        <div class="text-base font-black text-amber-500 tracking-tight">{{ $attendanceData['late_days'] }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Late</div>
                    </div>
                    <div class="bg-cyan-50/40 border border-cyan-100/50 rounded-xl p-3 text-center print:border-slate-200 print:bg-slate-50" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                        <div class="text-base font-black text-cyan-600 tracking-tight">{{ $attendanceData['half_days'] }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Half Day</div>
                    </div>
                    <div class="bg-indigo-50/50 border border-indigo-100/50 rounded-xl p-3 text-center print:border-slate-200 print:bg-slate-50" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                        <div class="text-base font-black text-indigo-600 tracking-tight">{{ number_format($attendanceData['percentage'], 1) }}%</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Attendance %</div>
                    </div>
                </div>

                <!-- Monthly Attendance -->
                @if(!empty($attendanceData['monthly']))
                <div class="overflow-x-auto ring-1 ring-slate-100 rounded-xl print:ring-0">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100 print:bg-slate-100 print:border-slate-300" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                <th class="py-3 px-4">Month</th>
                                <th class="py-3 px-4 text-center">Total</th>
                                <th class="py-3 px-4 text-center">Present</th>
                                <th class="py-3 px-4 text-center">Absent</th>
                                <th class="py-3 px-4 text-center">Late</th>
                                <th class="py-3 px-4 text-center">Half</th>
                                <th class="py-3 px-4 text-center">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-600 print:divide-slate-200">
                            @foreach($attendanceData['monthly'] as $month => $data)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3 px-4 font-bold text-slate-800">{{ $month }}</td>
                                    <td class="py-3 px-4 text-center text-slate-700">{{ $data['total'] }}</td>
                                    <td class="py-3 px-4 text-center font-medium text-emerald-600">{{ $data['present'] }}</td>
                                    <td class="py-3 px-4 text-center font-medium text-rose-500">{{ $data['absent'] }}</td>
                                    <td class="py-3 px-4 text-center font-medium text-amber-500">{{ $data['late'] }}</td>
                                    <td class="py-3 px-4 text-center font-medium text-cyan-600">{{ $data['half'] }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @php
                                            $attHigh = $data['percentage'] >= 75;
                                            $attMid = $data['percentage'] >= 60;
                                            $attBadgeStyle = $attHigh ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($attMid ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-rose-50 text-rose-700 border-rose-100');
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full border {{ $attBadgeStyle }}" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                            {{ number_format($data['percentage'], 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                <!-- Daily Attendance (Expandable) -->
                @if(!empty($attendanceData['daily']))
                <div class="mt-4 print:hidden">
                    <details class="group">
                        <summary class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 cursor-pointer hover:text-indigo-600 transition-colors">
                            <i class="fas fa-list-ul"></i>
                            <span>View Daily Attendance ({{ count($attendanceData['daily']) }} days)</span>
                            <i class="fas fa-chevron-down text-xs group-open:rotate-180 transition-transform"></i>
                        </summary>
                        <div class="mt-3 overflow-x-auto ring-1 ring-slate-100 rounded-xl">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider border-b border-slate-100" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                        <th class="py-2 px-3">Date</th>
                                        <th class="py-2 px-3">Day</th>
                                        <th class="py-2 px-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @foreach($attendanceData['daily'] as $date => $data)
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="py-2 px-3 font-medium text-slate-700">{{ $data['date'] }}</td>
                                            <td class="py-2 px-3 text-slate-500">{{ $data['day'] }}</td>
                                            <td class="py-2 px-3">
                                                @if($data['status'] == 'present')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">Present</span>
                                                @elseif($data['status'] == 'absent')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-700 border border-rose-200">Absent</span>
                                                @elseif($data['status'] == 'late')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 border border-amber-200">Late</span>
                                                @elseif(in_array($data['status'], ['half_day', 'half', 'halfday']))
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-700 border border-cyan-200">Half Day</span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">{{ ucfirst($data['status']) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                </div>
                @endif

            @else
                <div class="text-center py-10 border border-dashed border-slate-200 rounded-2xl bg-slate-50/30">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                        <i class="fas fa-calendar-times text-lg"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700">No attendance records found</h4>
                    <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1 leading-relaxed">
                        @if(isset($dateRangeText) && $dateRangeText)
                            No attendance records found for the period: <strong>{{ $dateRangeText }}</strong>
                        @else
                            Attendance metrics are unavailable or haven't been submitted yet for this operational timeline.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Grade Scale -->
        <div class="p-6 md:p-8 print:p-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fas fa-graduation-cap text-indigo-500"></i>
                <span>Grade Scale Legend</span>
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                @foreach($gradeScale as $grade)
                    @php
                        $isF = $grade['grade'] == 'F';
                        $isA = $grade['grade'] == 'A+' || $grade['grade'] == 'A';
                        $scaleColor = $isF ? 'text-rose-500' : ($isA ? 'text-emerald-600' : 'text-indigo-600');
                    @endphp
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 text-center transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:shadow-slate-100/50 print:border-slate-200 print:hover:translate-y-0 print:hover:shadow-none" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                        <h4 class="text-lg font-black {{ $scaleColor }} tracking-tight">{{ $grade['grade'] }}</h4>
                        <div class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $grade['min'] }}% - {{ $grade['max'] }}%</div>
                        <div class="text-[10px] font-medium text-slate-500 truncate mt-0.5" title="{{ $grade['description'] }}">{{ $grade['description'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-slate-50 border-t border-slate-100 text-center px-4 py-4 space-y-1 print:bg-slate-100 print:border-slate-300" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
            <p class="text-xs font-medium text-slate-400 flex items-center justify-center flex-wrap gap-x-2">
                <span><i class="fas fa-print mr-1 opacity-70"></i>Generated on {{ now()->format('d M Y, h:i A') }}</span>
                <span class="text-slate-200 hidden sm:inline">|</span>
                <span class="text-emerald-600 font-semibold"><i class="fas fa-check-circle mr-1 text-emerald-500"></i>System Verified Authentic Record</span>
            </p>
            <p class="text-[10px] font-medium text-slate-400 max-w-xl mx-auto leading-relaxed">
                This is an automated performance report. Please verify cross-channel data variables with administration offices prior to official certification procedures.
            </p>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body { background: white !important; }
        .no-print { display: none !important; }
        .print\:block { display: block !important; }
        .print\:inline { display: inline !important; }
        .print\:inline-block { display: inline-block !important; }
        .print\:p-0 { padding: 0 !important; }
        .print\:p-6 { padding: 1.5rem !important; }
        .print\:border { border: 1px solid #cbd5e1 !important; }
        .print\:border-slate-300 { border-color: #cbd5e1 !important; }
        .print\:border-slate-200 { border-color: #e2e8f0 !important; }
        .print\:shadow-none { box-shadow: none !important; }
        .print\:rounded-none { border-radius: 0 !important; }
        .print\:bg-slate-50 { background-color: #f8fafc !important; }
        .print\:bg-slate-100 { background-color: #f1f5f9 !important; }
        .print\:from-indigo-600 { background: #4f46e5 !important; }
        .print\:to-indigo-600 { background: #4f46e5 !important; }
        .print\:w-full { width: 100% !important; }
        .print\:max-w-full { max-width: 100% !important; }
        .print\:divide-slate-200 > * + * { border-color: #e2e8f0 !important; }
        .print\:border-slate-300 { border-color: #cbd5e1 !important; }
        .print\:shadow-none { box-shadow: none !important; }
    }
</style>
@endpush
@endsection