@extends('layouts.app')

@section('title', 'Complete Student Report')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pb-5 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📊 Complete Student Report</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $student->first_name }} {{ $student->last_name }} 
                    ({{ $student->admission_number ?? 'N/A' }})
                    @if(isset($academicSession))
                        - {{ $academicSession->name }}
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.reports.student.dashboard') }}" 
                   class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    ← Back
                </a>
                <button onclick="window.print()" 
                        class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    🖨 Print
                </button>
                <a href="{{ route('admin.reports.student.export-pdf', ['student_id' => $student->id]) }}" 
                   class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    📥 Export PDF
                </a>
            </div>
        </div>

        {{-- Student Information Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-800">👤 Student Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Full Name</p>
                    <p class="font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Admission Number</p>
                    <p class="font-medium text-gray-900">{{ $student->admission_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Roll Number</p>
                    <p class="font-medium text-gray-900">{{ $student->roll_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Section</p>
                    <p class="font-medium text-gray-900">
                        @if($student->classSection)
                            {{ optional($student->classSection->class)->grade->name ?? '' }} 
                            {{ $student->classSection->section_name ?? '' }}
                        @else
                            Not Assigned
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Gender</p>
                    <p class="font-medium text-gray-900">{{ $student->gender ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Date of Birth</p>
                    <p class="font-medium text-gray-900">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Father's Name</p>
                    <p class="font-medium text-gray-900">{{ $student->father_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Mother's Name</p>
                    <p class="font-medium text-gray-900">{{ $student->mother_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <p class="font-medium">
                        @if($student->status == 'Active')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Active</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">Inactive</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Attendance</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ number_format($stats['attendance_percentage'] ?? 0, 1) }}%</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Average Marks</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['avg_percentage'] ?? 0, 1) }}%</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Exams</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['total_exams'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Fee Status</p>
                        @php
                            $feeStatus = 'N/A';
                            if (isset($stats['pending_fee']) && isset($stats['paid_fee'])) {
                                if ($stats['pending_fee'] <= 0 && $stats['paid_fee'] > 0) {
                                    $feeStatus = 'Paid';
                                } elseif ($stats['paid_fee'] > 0) {
                                    $feeStatus = 'Partial';
                                } else {
                                    $feeStatus = 'Unpaid';
                                }
                            }
                            $feeColor = $feeStatus == 'Paid' ? 'green' : ($feeStatus == 'Partial' ? 'yellow' : 'red');
                        @endphp
                        <p class="text-2xl font-bold text-{{ $feeColor }}-600">
                            {{ $feeStatus }}
                        </p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-lg">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attendance Summary Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-800">📋 Attendance Summary</h3>
            </div>
            <div class="p-6">
                @php
                    $attendanceData = $attendanceSummary ?? [];
                    $hasAttendance = is_array($attendanceData) && count($attendanceData) > 0;
                @endphp
                
                @if($hasAttendance)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Total Days</p>
                        <p class="text-xl font-bold text-gray-900">{{ $attendanceData['total'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Present</p>
                        <p class="text-xl font-bold text-green-600">{{ $attendanceData['present'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Absent</p>
                        <p class="text-xl font-bold text-red-600">{{ $attendanceData['absent'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Percentage</p>
                        <p class="text-xl font-bold text-indigo-600">{{ number_format($attendanceData['percentage'] ?? 0, 1) }}%</p>
                    </div>
                </div>

                {{-- Monthly Attendance --}}
                @if(isset($attendanceMonthly) && count($attendanceMonthly) > 0)
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100/70">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Month</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Present</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Absent</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Leave</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($attendanceMonthly as $month => $data)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $month }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $data['total'] ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center text-green-600 font-semibold">{{ $data['present'] ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center text-red-600">{{ $data['absent'] ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center text-blue-600">{{ $data['leave'] ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold">{{ number_format($data['percentage'] ?? 0, 1) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                @else
                <p class="text-sm text-gray-500 text-center py-4">No attendance data available.</p>
                @endif
            </div>
        </div>

        {{-- Exam Performance Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-800">📝 Exam Performance</h3>
            </div>
            <div class="p-6">
                @if(isset($examPerformance) && count($examPerformance) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100/70">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Exam</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Marks</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Max</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">%</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Grade</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @php
                                // Helper function for grade calculation
                                $calculateGrade = function($percentage) {
                                    if ($percentage >= 90) return 'A+';
                                    if ($percentage >= 80) return 'A';
                                    if ($percentage >= 70) return 'B+';
                                    if ($percentage >= 60) return 'B';
                                    if ($percentage >= 50) return 'C';
                                    if ($percentage >= 40) return 'D';
                                    return 'F';
                                };
                            @endphp
                            @foreach($examPerformance as $exam)
                            @php
                                $percentage = ($exam->max_marks > 0) ? ($exam->marks_obtained / $exam->max_marks) * 100 : 0;
                                $grade = $calculateGrade($percentage);
                                $gradeColor = $grade == 'F' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $exam->exam->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $exam->subject->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-center font-semibold">{{ $exam->marks_obtained ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $exam->max_marks ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold text-indigo-600">{{ number_format($percentage, 1) }}%</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $gradeColor }}">
                                        {{ $grade }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    @if($exam->marks_obtained >= $exam->passing_marks)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Passed</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">Failed</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-500 text-center py-4">No exam performance data available.</p>
                @endif
            </div>
        </div>

        {{-- Subject Performance Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-800">📚 Subject Performance</h3>
            </div>
            <div class="p-6">
                @if(isset($subjectPerformance) && count($subjectPerformance) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100/70">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Avg Marks</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Max Marks</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">%</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Pass %</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Grade</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @php
                                $calculateGrade = function($percentage) {
                                    if ($percentage >= 90) return 'A+';
                                    if ($percentage >= 80) return 'A';
                                    if ($percentage >= 70) return 'B+';
                                    if ($percentage >= 60) return 'B';
                                    if ($percentage >= 50) return 'C';
                                    if ($percentage >= 40) return 'D';
                                    return 'F';
                                };
                            @endphp
                            @foreach($subjectPerformance as $subject)
                            @php
                                $grade = $calculateGrade($subject['percentage'] ?? 0);
                                $gradeColor = $grade == 'F' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $subject['subject']->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-center font-semibold">{{ number_format($subject['avg_marks'] ?? 0, 1) }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ number_format($subject['max_marks'] ?? 0, 1) }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold text-indigo-600">{{ number_format($subject['percentage'] ?? 0, 1) }}%</td>
                                <td class="px-4 py-3 text-sm text-center">{{ number_format($subject['pass_percentage'] ?? 0, 1) }}%</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $gradeColor }}">
                                        {{ $grade }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-500 text-center py-4">No subject performance data available.</p>
                @endif
            </div>
        </div>

        {{-- Fee Summary Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-800">💰 Fee Summary</h3>
            </div>
            <div class="p-6">
                @if(isset($feeSummary) && count($feeSummary) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Total Fee</p>
                        <p class="text-xl font-bold text-gray-900">${{ number_format($feeSummary['total'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Paid</p>
                        <p class="text-xl font-bold text-green-600">${{ number_format($feeSummary['paid'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Pending</p>
                        <p class="text-xl font-bold text-red-600">${{ number_format($feeSummary['pending'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Overdue</p>
                        <p class="text-xl font-bold text-orange-600">{{ $feeSummary['overdue'] ?? 0 }}</p>
                    </div>
                </div>

                {{-- Fee Installments --}}
                @if(isset($feeInstallments) && count($feeInstallments) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100/70">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Paid</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($feeInstallments as $installment)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $installment->feeSubmissionType->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-center font-semibold">${{ number_format($installment->amount, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ \Carbon\Carbon::parse($installment->due_date)->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    @if($installment->status == 'paid')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Paid</span>
                                    @elseif($installment->status == 'partial')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold">Partial</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-center">${{ number_format($installment->paid_amount ?? 0, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                @else
                <p class="text-sm text-gray-500 text-center py-4">No fee data available.</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .no-print { display: none; }
        body { background: white; }
        .bg-gray-50 { background: white !important; }
        .shadow-sm { box-shadow: none !important; }
        .border { border: 1px solid #e5e7eb !important; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-print if print parameter is present
        @if(request()->has('print'))
            window.print();
        @endif
    });
</script>
@endpush