{{-- resources/views/admin/students/exam-results.blade.php --}}

@extends('layouts.app')

@section('title', 'Exam Results - ' . ($student->first_name ?? 'N/A') . ' ' . ($student->last_name ?? ''))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">📝 Exam Results</h1>
                    <p class="text-gray-500 mt-0.5">{{ $student->first_name ?? 'N/A' }} {{ $student->last_name ?? '' }} - {{ $student->admission_number ?? 'N/A' }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.students.show', $student->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        👤 Back to Profile
                    </a>
                    <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        ← Back to Students
                    </a>
                    <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        🖨 Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Student Info Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Student Name</div>
                <div class="font-bold text-gray-800 mt-1">{{ $student->first_name ?? 'N/A' }} {{ $student->last_name ?? '' }}</div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Class</div>
                <div class="font-bold text-gray-800 mt-1">{{ $student->classSection->full_name ?? 'N/A' }}</div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Roll No</div>
                <div class="font-bold text-gray-800 mt-1">{{ $student->roll_number ?? 'N/A' }}</div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Admission No</div>
                <div class="font-bold text-gray-800 mt-1">{{ $student->admission_number ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Performance Summary -->
        @php
            $totalMarks = 0;
            $totalMaxMarks = 0;
            $totalSubjects = $subjectWise->count();
            $totalExams = $examResults->count();
            $passedSubjects = 0;
            $failedSubjects = 0;
            $subjectGrades = [];
            
            foreach ($subjectWise as $subjectId => $marks) {
                $subTotal = 0;
                $subMax = 0;
                foreach ($marks as $mark) {
                    $subTotal += $mark->marks_obtained ?? 0;
                    $subMax += $mark->max_marks ?? 0;
                    $totalMarks += $mark->marks_obtained ?? 0;
                    $totalMaxMarks += $mark->max_marks ?? 0;
                }
                $subPercentage = $subMax > 0 ? ($subTotal / $subMax) * 100 : 0;
                if ($subPercentage >= 40) {
                    $passedSubjects++;
                } else {
                    $failedSubjects++;
                }
            }
            $overallPercentage = $totalMaxMarks > 0 ? round(($totalMarks / $totalMaxMarks) * 100, 2) : 0;
            
            $grade = 'F';
            if ($overallPercentage >= 90) $grade = 'A+';
            elseif ($overallPercentage >= 80) $grade = 'A';
            elseif ($overallPercentage >= 70) $grade = 'B+';
            elseif ($overallPercentage >= 60) $grade = 'B';
            elseif ($overallPercentage >= 50) $grade = 'C';
            elseif ($overallPercentage >= 40) $grade = 'D';
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center hover:shadow-lg transition">
                <div class="text-2xl font-bold text-gray-800">{{ $totalExams }}</div>
                <div class="text-xs text-gray-500">Total Exams</div>
            </div>
            <div class="bg-indigo-50 rounded-xl shadow p-4 text-center border border-indigo-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-indigo-600">{{ $totalSubjects }}</div>
                <div class="text-xs text-indigo-600">Subjects</div>
            </div>
            <div class="bg-blue-50 rounded-xl shadow p-4 text-center border border-blue-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($totalMarks, 1) }}</div>
                <div class="text-xs text-blue-600">Total Marks</div>
            </div>
            <div class="bg-green-50 rounded-xl shadow p-4 text-center border border-green-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-green-600">{{ $overallPercentage }}%</div>
                <div class="text-xs text-green-600">Overall %</div>
            </div>
            <div class="bg-purple-50 rounded-xl shadow p-4 text-center border border-purple-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-purple-600">{{ $grade }}</div>
                <div class="text-xs text-purple-600">Overall Grade</div>
            </div>
            <div class="bg-teal-50 rounded-xl shadow p-4 text-center border border-teal-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-teal-600">{{ $passedSubjects }}</div>
                <div class="text-xs text-teal-600">✅ Passed</div>
                <div class="text-xs text-red-500">Failed: {{ $failedSubjects }}</div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- SECTION 1: SUBJECT-WISE PERFORMANCE -->
        <!-- ============================================ -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6 hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📚</span> Subject-wise Performance
                    <span class="text-sm font-normal text-gray-500">(Across all exams)</span>
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Subject</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Exams</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Marks</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Avg Marks</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Avg %</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Grade</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Best</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Worst</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($subjectWise as $subjectId => $marks)
                            @php
                                $subject = $marks->first()->subject ?? null;
                                $examCount = $marks->count();
                                $totalMarksSub = $marks->sum('marks_obtained');
                                $totalMaxMarksSub = $marks->sum('max_marks');
                                $avgMarks = $examCount > 0 ? round($totalMarksSub / $examCount, 2) : 0;
                                $avgPercentage = $totalMaxMarksSub > 0 ? round(($totalMarksSub / $totalMaxMarksSub) * 100, 2) : 0;
                                $best = $marks->max('marks_obtained') ?? 0;
                                $worst = $marks->min('marks_obtained') ?? 0;
                                
                                $subGrade = 'F';
                                if ($avgPercentage >= 90) $subGrade = 'A+';
                                elseif ($avgPercentage >= 80) $subGrade = 'A';
                                elseif ($avgPercentage >= 70) $subGrade = 'B+';
                                elseif ($avgPercentage >= 60) $subGrade = 'B';
                                elseif ($avgPercentage >= 50) $subGrade = 'C';
                                elseif ($avgPercentage >= 40) $subGrade = 'D';
                                
                                $subHigh = $avgPercentage >= 80;
                                $subMid = $avgPercentage >= 60;
                                $badgeColor = $subHigh ? 'bg-green-100 text-green-800' : ($subMid ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                $isPass = $avgPercentage >= 40;
                            @endphp
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $subject->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center text-sm">{{ $examCount }}</td>
                                <td class="px-4 py-3 text-center text-sm font-semibold">{{ number_format($totalMarksSub, 1) }} / {{ number_format($totalMaxMarksSub, 1) }}</td>
                                <td class="px-4 py-3 text-center text-sm">{{ number_format($avgMarks, 1) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full {{ $badgeColor }}">
                                        {{ $avgPercentage }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold {{ $subHigh ? 'text-green-600' : ($subMid ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $subGrade }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-green-600 font-semibold">{{ $best }}</td>
                                <td class="px-4 py-3 text-center text-sm text-red-600 font-semibold">{{ $worst }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($isPass)
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
        </div>

        <!-- ============================================ -->
        <!-- SECTION 2: EXAM-WISE DETAILED MARKS -->
        <!-- ============================================ -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📝</span> Exam-wise Detailed Marks
                    <span class="text-sm font-normal text-gray-500">(Subject-wise breakdown for each exam)</span>
                </h2>
            </div>
            <div class="p-6">
                @forelse($examResults as $result)
                    @php
                        $isPass = $result->percentage >= 40;
                        $examMarks = $subjectWise->flatMap(function($marks, $subjectId) use ($result) {
                            return $marks->filter(function($mark) use ($result) {
                                return $mark->exam_id == $result->exam_id;
                            });
                        });
                    @endphp
                    <div class="border rounded-xl p-4 mb-4 last:mb-0 hover:shadow-lg transition-all">
                        <!-- Exam Header -->
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-3 pb-3 border-b">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">{{ $result->exam->name ?? 'N/A' }}</h3>
                                <p class="text-sm text-gray-500">{{ $result->exam->examType->name ?? 'N/A' }} | {{ $result->created_at ? $result->created_at->format('d M Y') : 'N/A' }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-4">
                                <div class="text-center">
                                    <div class="text-sm text-gray-500">Total</div>
                                    <div class="text-lg font-bold text-indigo-600">{{ number_format($result->total_marks, 1) }}</div>
                                    <div class="text-xs text-gray-400">/ {{ number_format($result->total_max_marks, 1) }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm text-gray-500">Percentage</div>
                                    <div class="text-lg font-bold {{ $isPass ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($result->percentage, 1) }}%
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm text-gray-500">Grade</div>
                                    <div class="text-lg font-bold {{ $isPass ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $result->grade ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm text-gray-500">Rank</div>
                                    <div class="text-lg font-bold text-gray-700">#{{ $result->rank_in_class ?? 'N/A' }}</div>
                                </div>
                                <div class="text-center">
                                    @if($isPass)
                                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">✅ Passed</span>
                                    @else
                                        <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800">❌ Failed</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Subject-wise Marks for this Exam -->
                        @if($examMarks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Subject</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Obtained</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Max Marks</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Passing Marks</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Percentage</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Status</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($examMarks as $mark)
                                        @php
                                            $subPercentage = $mark->max_marks > 0 ? round(($mark->marks_obtained / $mark->max_marks) * 100, 2) : 0;
                                            $subPass = $subPercentage >= 40;
                                            $subject = $mark->subject ?? null;
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-3 py-2 font-medium text-gray-800">{{ $subject->name ?? 'N/A' }}</td>
                                            <td class="px-3 py-2 text-center font-semibold {{ $subPass ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $mark->marks_obtained ?? 0 }}
                                            </td>
                                            <td class="px-3 py-2 text-center text-gray-500">{{ $mark->max_marks ?? 0 }}</td>
                                            <td class="px-3 py-2 text-center text-gray-500">{{ $mark->passing_marks ?? 0 }}</td>
                                            <td class="px-3 py-2 text-center font-bold {{ $subPass ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $subPercentage }}%
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                @if($subPass)
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">✅ Pass</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">❌ Fail</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-500">{{ $mark->remarks ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-gray-400 text-sm">
                            No subject marks available for this exam
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-2">No exam results found for this student</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ============================================ -->
        <!-- SECTION 3: GRADE SCALE LEGEND -->
        <!-- ============================================ -->
        <div class="mt-6 bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📊</span> Grade Scale Legend
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    @php
                        $gradeScale = [
                            ['grade' => 'A+', 'min' => 90, 'max' => 100, 'color' => 'text-emerald-600'],
                            ['grade' => 'A', 'min' => 80, 'max' => 89, 'color' => 'text-green-600'],
                            ['grade' => 'B+', 'min' => 70, 'max' => 79, 'color' => 'text-blue-600'],
                            ['grade' => 'B', 'min' => 60, 'max' => 69, 'color' => 'text-indigo-600'],
                            ['grade' => 'C', 'min' => 50, 'max' => 59, 'color' => 'text-yellow-600'],
                            ['grade' => 'D', 'min' => 40, 'max' => 49, 'color' => 'text-orange-600'],
                            ['grade' => 'F', 'min' => 0, 'max' => 39, 'color' => 'text-red-600'],
                        ];
                    @endphp
                    @foreach($gradeScale as $scale)
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 text-center hover:shadow-md transition">
                            <h4 class="text-2xl font-black {{ $scale['color'] }}">{{ $scale['grade'] }}</h4>
                            <div class="text-xs font-bold text-gray-500">{{ $scale['min'] }}% - {{ $scale['max'] }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

@push('styles')
<style>
@media print {
    body { background: white !important; }
    .no-print { display: none !important; }
    .container { max-width: 100% !important; padding: 0 !important; }
    .shadow-xl { box-shadow: none !important; }
    .rounded-2xl { border-radius: 0 !important; }
    .bg-gradient-to-br { background: white !important; }
    .border { border-color: #e2e8f0 !important; }
    .border-b { border-bottom: 1px solid #e2e8f0 !important; }
    .hover\:shadow-lg:hover { box-shadow: none !important; }
}
</style>
@endpush
@endsection