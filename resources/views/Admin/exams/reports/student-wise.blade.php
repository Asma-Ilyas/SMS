{{-- resources/views/admin/exams/reports/student-wise.blade.php --}}

@extends('layouts.app')

@section('title', 'Student Wise Results - ' . ($student->first_name ?? 'N/A'))

@php
    // Helper function to calculate grade
    function calculateGradeView($percentage) {
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
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">📊 Student Wise Results</h1>
                    <p class="text-gray-500 mt-0.5">{{ $student->first_name ?? 'N/A' }} {{ $student->last_name ?? '' }} - {{ $student->admission_number ?? 'N/A' }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.exams.reports.student-wise-form') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        🔍 Change Selection
                    </a>
                    <a href="{{ route('admin.exams.reports.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        ← Back
                    </a>
                    <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        🖨 Print
                    </button>
                </div>
            </div>
        </div>

        @if(isset($student) && $student)
        
        <!-- Student Info Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Student Name</div>
                <div class="font-bold text-gray-800 mt-1">{{ $student->first_name ?? 'N/A' }} {{ $student->last_name ?? '' }}</div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Admission No</div>
                <div class="font-bold text-gray-800 mt-1">{{ $student->admission_number ?? 'N/A' }}</div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Class</div>
                <div class="font-bold text-gray-800 mt-1">
                    @if($student->classSection)
                        @php
                            $class = $student->classSection->class;
                            $gradeName = $class->grade->name ?? 'N/A';
                            $streamName = $class->stream->name ?? '';
                            $sectionName = $student->classSection->section_name ?? '';
                            $className = $gradeName;
                            if ($streamName && !in_array($gradeName, ['KG', '1', '2', '3', '4', '5'])) {
                                $className .= ' ' . $streamName;
                            }
                            $className .= '-' . $sectionName;
                        @endphp
                        {{ $className }}
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Roll No</div>
                <div class="font-bold text-gray-800 mt-1">{{ $student->roll_number ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Performance Trend -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📈</span> Performance Trend
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($exams ?? [] as $exam)
                        @php
                            $result = isset($results[$exam->id]) ? $results[$exam->id] : null;
                            $percentage = $result ? $result->percentage : 0;
                            $totalMarks = $result ? $result->total_marks : 0;
                            $totalMaxMarks = $result ? $result->total_max_marks : 0;
                            $grade = $result ? $result->grade : 'N/A';
                            $isPass = $percentage >= 40;
                        @endphp
                        <div class="bg-white border rounded-xl p-4 hover:shadow-lg transition-all {{ $isPass ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-red-500' }}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-800">{{ $exam->name ?? 'N/A' }}</h4>
                                    <p class="text-xs text-gray-400">{{ isset($exam->start_date) ? \Carbon\Carbon::parse($exam->start_date)->format('d M Y') : 'N/A' }}</p>
                                </div>
                                <span class="text-xl font-bold {{ $isPass ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($percentage, 1) }}%
                                </span>
                            </div>
                            <div class="mt-2 text-sm text-gray-500">
                                📝 {{ number_format($totalMarks, 1) }} / {{ number_format($totalMaxMarks, 1) }}
                                <span class="ml-3">🏷️ Grade: <span class="font-bold {{ $isPass ? 'text-green-600' : 'text-red-600' }}">{{ $grade }}</span></span>
                            </div>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $isPass ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="mt-2 text-xs">
                                @if($isPass)
                                    <span class="text-green-600">✅ Passed</span>
                                @else
                                    <span class="text-red-600">❌ Failed</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-400">
                            No exam results found for this student
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Subject Summary -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📚</span> Subject Summary
                    <span class="text-sm font-normal text-gray-500">(Across all tests)</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Subject</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Tests</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Avg Marks</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Avg %</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Highest</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Lowest</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Passed</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Pass %</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($subjectResults ?? [] as $subject)
                            @php
                                $subAvgPercentage = $subject->average_percentage ?? 0;
                                // Calculate grade directly
                                if ($subAvgPercentage >= 90) {
                                    $subGrade = 'A+';
                                } elseif ($subAvgPercentage >= 80) {
                                    $subGrade = 'A';
                                } elseif ($subAvgPercentage >= 70) {
                                    $subGrade = 'B+';
                                } elseif ($subAvgPercentage >= 60) {
                                    $subGrade = 'B';
                                } elseif ($subAvgPercentage >= 50) {
                                    $subGrade = 'C';
                                } elseif ($subAvgPercentage >= 40) {
                                    $subGrade = 'D';
                                } else {
                                    $subGrade = 'F';
                                }
                                $subHigh = $subAvgPercentage >= 80;
                                $subMid = $subAvgPercentage >= 60;
                                $badgeColor = $subHigh ? 'bg-green-100 text-green-800' : ($subMid ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $subject->subject_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center text-sm">{{ $subject->exam_count ?? 0 }}</td>
                                <td class="px-4 py-3 text-center text-sm font-semibold">{{ number_format($subject->average_marks ?? 0, 1) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full {{ $badgeColor }}">
                                        {{ number_format($subAvgPercentage, 1) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-green-600 font-semibold">{{ $subject->highest_marks ?? 0 }}</td>
                                <td class="px-4 py-3 text-center text-sm text-red-600 font-semibold">{{ $subject->lowest_marks ?? 0 }}</td>
                                <td class="px-4 py-3 text-center text-sm font-semibold text-green-600">{{ $subject->passed_count ?? 0 }}</td>
                                <td class="px-4 py-3 text-center text-sm">
                                    @php
                                        $passPercentage = $subject->exam_count > 0 ? ($subject->passed_count / $subject->exam_count) * 100 : 0;
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-bold rounded-full {{ $passPercentage >= 80 ? 'bg-green-100 text-green-800' : ($passPercentage >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($passPercentage, 1) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold {{ $subHigh ? 'text-green-600' : ($subMid ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $subGrade }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-400">No subject results available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Test-wise Performance -->
        @if(isset($exams) && $exams->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📝</span> Test-wise Performance
                </h3>
            </div>
            <div class="p-6">
                @foreach($exams as $exam)
                    @php
                        $result = isset($results[$exam->id]) ? $results[$exam->id] : null;
                        $percentage = $result ? $result->percentage : 0;
                        $totalMarks = $result ? $result->total_marks : 0;
                        $totalMaxMarks = $result ? $result->total_max_marks : 0;
                        $grade = $result ? $result->grade : 'N/A';
                        $isPass = $percentage >= 40;
                        
                        $examSubjectMarks = isset($examMarks[$exam->id]) ? $examMarks[$exam->id] : collect();
                        $passedSubjects = $examSubjectMarks->filter(function($mark) {
                            return $mark->marks_obtained >= $mark->passing_marks;
                        })->count();
                        $failedSubjects = $examSubjectMarks->filter(function($mark) {
                            return $mark->marks_obtained < $mark->passing_marks;
                        })->count();
                    @endphp
                    <div class="border rounded-xl p-4 mb-4 last:mb-0 hover:shadow-lg transition-all">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h4 class="text-lg font-bold text-gray-800">{{ $exam->name ?? 'N/A' }}</h4>
                                <p class="text-sm text-gray-400">{{ isset($exam->start_date) ? \Carbon\Carbon::parse($exam->start_date)->format('d M Y') : 'N/A' }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-4">
                                <div class="text-center">
                                    <div class="text-xl font-bold {{ $isPass ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                    <div class="text-xs text-gray-500">Percentage</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-indigo-600">{{ number_format($totalMarks, 1) }}</div>
                                    <div class="text-xs text-gray-500">Total Marks</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-600">{{ number_format($totalMaxMarks, 1) }}</div>
                                    <div class="text-xs text-gray-500">Max Marks</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold {{ $isPass ? 'text-green-600' : 'text-red-600' }}">{{ $grade }}</div>
                                    <div class="text-xs text-gray-500">Grade</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-green-600">{{ $passedSubjects }}</div>
                                    <div class="text-xs text-green-600">✅ Passed</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-red-600">{{ $failedSubjects }}</div>
                                    <div class="text-xs text-red-600">❌ Failed</div>
                                </div>
                            </div>
                        </div>
                        
                        @if($examSubjectMarks->count() > 0)
                        <div class="mt-4 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Subject</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Marks</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Max Marks</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">%</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($examSubjectMarks as $mark)
                                        @php
                                            $subPercentage = $mark->max_marks > 0 ? round(($mark->marks_obtained / $mark->max_marks) * 100, 2) : 0;
                                            $subPass = $subPercentage >= 40;
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-3 py-2 font-medium text-gray-800">{{ $mark->subject_name ?? 'N/A' }}</td>
                                            <td class="px-3 py-2 text-center font-semibold {{ $subPass ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $mark->marks_obtained ?? 0 }}
                                            </td>
                                            <td class="px-3 py-2 text-center text-gray-500">{{ $mark->max_marks ?? 0 }}</td>
                                            <td class="px-3 py-2 text-center font-bold {{ $subPass ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($subPercentage, 1) }}%
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                @if($subPass)
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">✅ Pass</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">❌ Fail</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @else
        <!-- No Student Selected -->
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-4 text-xl font-medium text-gray-900">No Student Selected</h3>
            <p class="mt-2 text-gray-500">Please select a student from the filters above.</p>
            <a href="{{ route('admin.exams.reports.student-wise-form') }}" class="mt-4 inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                🔍 Select Student
            </a>
        </div>
        @endif
        
    </div>
</div>

@push('scripts')
<script>
function calculateGrade(percentage) {
    if (percentage >= 90) return 'A+';
    if (percentage >= 80) return 'A';
    if (percentage >= 70) return 'B+';
    if (percentage >= 60) return 'B';
    if (percentage >= 50) return 'C';
    if (percentage >= 40) return 'D';
    return 'F';
}
</script>
@endpush
@endsection