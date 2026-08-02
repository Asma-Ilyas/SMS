{{-- resources/views/admin/exams/reports/class-wise.blade.php --}}

@extends('layouts.app')

@section('title', 'Class-wise Exam Report')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🏫 Class-wise Exam Report</h1>
                    <p class="text-gray-500 mt-1">
                        {{ $exam->name }} - {{ $classSection->full_name ?? $classSection->section_name }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.exams.reports.class-wise-form') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        ← Change Selection
                    </a>
                    <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        🖨 Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        @php
            $totalStudents = $students->count();
            $totalMarks = 0;
            $totalMaxMarks = 0;
            $passed = 0;
            $failed = 0;
            
            foreach ($students as $student) {
                $result = $results[$student->id] ?? null;
                if ($result) {
                    $totalMarks += $result->total_marks;
                    $totalMaxMarks += $result->total_max_marks;
                    if ($result->percentage >= 40) {
                        $passed++;
                    } else {
                        $failed++;
                    }
                }
            }
            $avgPercentage = $totalMaxMarks > 0 ? round(($totalMarks / $totalMaxMarks) * 100, 2) : 0;
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <div class="text-2xl font-bold text-gray-800">{{ $totalStudents }}</div>
                <div class="text-sm text-gray-500">Total Students</div>
            </div>
            <div class="bg-blue-50 rounded-xl shadow p-4 text-center border border-blue-200">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($totalMarks, 0) }}</div>
                <div class="text-sm text-blue-700">Total Marks</div>
            </div>
            <div class="bg-indigo-50 rounded-xl shadow p-4 text-center border border-indigo-200">
                <div class="text-2xl font-bold text-indigo-600">{{ $avgPercentage }}%</div>
                <div class="text-sm text-indigo-700">Average %</div>
            </div>
            <div class="bg-green-50 rounded-xl shadow p-4 text-center border border-green-200">
                <div class="text-2xl font-bold text-green-600">{{ $passed }}</div>
                <div class="text-sm text-green-700">✅ Pass</div>
                <div class="text-xs text-red-500">Fail: {{ $failed }}</div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📋</span> Results
                </h2>
                <div class="flex gap-2">
                    <button onclick="window.print()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">
                        🖨 Print
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                            @foreach($subjects as $subject)
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ $subject->name }}
                                    <br>
                                    <span class="text-[10px] font-normal text-gray-400">(Max: {{ $subject->max_marks ?? 100 }})</span>
                                </th>
                            @endforeach
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">%</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Grade</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Rank</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($students as $student)
                            @php
                                $result = $results[$student->id] ?? null;
                                $studentMarks = $marks[$student->id] ?? collect();
                                $gradeScaleData = $gradeScale ? $gradeScale->grades : [];
                                $grade = 'N/A';
                                
                                if ($result && $result->percentage !== null) {
                                    foreach ($gradeScaleData as $range) {
                                        if ($result->percentage >= $range['min'] && $result->percentage <= $range['max']) {
                                            $grade = $range['grade'];
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200">
                                <td class="px-3 py-3 text-center font-medium">{{ $loop->iteration }}</td>
                                <td class="px-3 py-3">
                                    <div class="font-medium text-gray-800">{{ $student->first_name }} {{ $student->last_name }}</div>
                                    <div class="text-xs text-gray-400">Roll: {{ $student->roll_number ?? 'N/A' }}</div>
                                </td>
                                
                                @foreach($subjects as $subject)
                                    @php
                                        $mark = $studentMarks->firstWhere('subject_id', $subject->id);
                                        $marksObtained = $mark ? $mark->marks_obtained : '-';
                                        $maxMarks = $mark ? $mark->max_marks : ($subject->max_marks ?? 100);
                                    @endphp
                                    <td class="px-3 py-3 text-center">
                                        <span class="font-medium {{ $marksObtained !== '-' && $marksObtained < ($maxMarks * 0.4) ? 'text-red-600' : 'text-gray-800' }}">
                                            {{ $marksObtained }}
                                        </span>
                                        <span class="text-xs text-gray-400">/ {{ $maxMarks }}</span>
                                    </td>
                                @endforeach
                                
                                <td class="px-3 py-3 text-center font-semibold">
                                    {{ $result ? number_format($result->total_marks, 2) : '-' }}
                                </td>
                                <td class="px-3 py-3 text-center font-bold {{ $result && $result->percentage >= 40 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $result ? $result->percentage . '%' : '-' }}
                                </td>
                                <td class="px-3 py-3 text-center font-bold {{ $grade == 'F' ? 'text-red-600' : 'text-blue-600' }}">
                                    {{ $grade }}
                                </td>
                                <td class="px-3 py-3 text-center font-semibold text-gray-700">
                                    #{{ $result ? $result->rank_in_class : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .container, .container * {
        visibility: visible;
    }
    .container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
    table {
        font-size: 10px !important;
    }
    th, td {
        padding: 4px 6px !important;
    }
}
</style>
@endpush
@endsection