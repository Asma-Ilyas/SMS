{{-- resources/views/admin/exams/reports/subject-wise.blade.php --}}

@extends('layouts.app')

@section('title', 'Subject Wise Report')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">📊 Subject Wise Report</h1>
                    <p class="text-gray-500 mt-1">
                        {{ $subject->name }} ({{ $subject->code }})
                        @if(request('class_section_id'))
                            - {{ $classSection->full_name ?? 'Selected Section' }}
                        @endif
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.exams.reports.subject-wise-form') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        🔍 Change Filters
                    </a>
                    <a href="{{ route('admin.exams.reports.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        ← Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Summary -->
        <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
            <div class="flex flex-wrap gap-4 text-sm">
                <span class="font-medium text-gray-700">Filters Applied:</span>
                <span class="bg-gray-100 px-3 py-1 rounded-full">
                    Subject: {{ $subject->name }}
                </span>
                @if(request('class_section_id'))
                    <span class="bg-gray-100 px-3 py-1 rounded-full">
                        Section: {{ $classSection->full_name ?? 'N/A' }}
                    </span>
                @endif
                @if(request('class_id'))
                    <span class="bg-gray-100 px-3 py-1 rounded-full">
                        Class: {{ $class->grade->name ?? 'N/A' }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Summary Cards -->
        @php
            $totalStudents = $examResults->total();
            $passed = $examResults->filter(function($result) {
                $percentage = $result->max_marks > 0 ? round(($result->marks_obtained / $result->max_marks) * 100, 2) : 0;
                return $percentage >= 40;
            })->count();
            $failed = $totalStudents - $passed;
            $avgMarks = $examResults->avg('marks_obtained') ?? 0;
            $highest = $examResults->max('marks_obtained') ?? 0;
            $lowest = $examResults->min('marks_obtained') ?? 0;
            $passPercentage = $totalStudents > 0 ? round(($passed / $totalStudents) * 100, 2) : 0;
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center hover:shadow-lg transition-all duration-300">
                <div class="text-2xl font-bold text-gray-800">{{ $totalStudents }}</div>
                <div class="text-sm text-gray-500">Total Students</div>
            </div>
            <div class="bg-green-50 rounded-xl shadow p-4 text-center border-2 border-green-200 hover:shadow-lg transition-all duration-300">
                <div class="text-2xl font-bold text-green-600">{{ $passed }}</div>
                <div class="text-sm text-green-700">✅ Passed</div>
            </div>
            <div class="bg-red-50 rounded-xl shadow p-4 text-center border-2 border-red-200 hover:shadow-lg transition-all duration-300">
                <div class="text-2xl font-bold text-red-600">{{ $failed }}</div>
                <div class="text-sm text-red-700">❌ Failed</div>
            </div>
            <div class="bg-blue-50 rounded-xl shadow p-4 text-center border-2 border-blue-200 hover:shadow-lg transition-all duration-300">
                <div class="text-2xl font-bold text-blue-600">{{ $passPercentage }}%</div>
                <div class="text-sm text-blue-700">Pass %</div>
            </div>
            <div class="bg-purple-50 rounded-xl shadow p-4 text-center border-2 border-purple-200 hover:shadow-lg transition-all duration-300">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($avgMarks, 2) }}</div>
                <div class="text-sm text-purple-700">Avg Marks</div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📋</span> Exam Results
                </h2>
                <span class="text-sm text-gray-500">{{ $examResults->total() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Class</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Exam</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Marks</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Max Marks</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Percentage</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($examResults as $result)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">
                                    {{ $result->student->first_name ?? 'N/A' }} {{ $result->student->last_name ?? '' }}
                                </div>
                                <div class="text-xs text-gray-400">Roll: {{ $result->student->roll_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @php
                                    $classSection = $result->student->classSection ?? null;
                                    $className = 'N/A';
                                    if ($classSection) {
                                        $gradeName = $classSection->class->grade->name ?? '';
                                        $streamName = $classSection->class->stream->name ?? '';
                                        $sectionName = $classSection->section_name ?? '';
                                        if ($streamName && !in_array($gradeName, ['KG', '1', '2', '3', '4', '5'])) {
                                            $className = $gradeName . ' ' . $streamName . ' - ' . $sectionName;
                                        } else {
                                            $className = $gradeName . ' - ' . $sectionName;
                                        }
                                    }
                                @endphp
                                {{ $className }}
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $result->exam->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-indigo-600">{{ $result->marks_obtained ?? 0 }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $result->max_marks ?? 0 }}</td>
                            <td class="px-4 py-3 text-sm">
                                @php
                                    $percentage = $result->max_marks > 0 ? round(($result->marks_obtained / $result->max_marks) * 100, 2) : 0;
                                @endphp
                                <span class="font-semibold {{ $percentage >= 40 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $percentage }}%
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($percentage >= 40)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">✅ Pass</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">❌ Fail</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $result->exam->start_date ? date('d M Y', strtotime($result->exam->start_date)) : 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2">No results found for this subject</p>
                                <p class="text-sm">Try adjusting your filters</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $examResults->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection