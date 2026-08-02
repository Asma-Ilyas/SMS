@extends('layouts.app')
@section('title', 'Grade Distribution')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.exams.reports.index') }}" class="hover:text-indigo-600 transition">← Back to Reports</a>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700 font-medium">Grade Distribution</span>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                <h1 class="text-3xl font-bold">📊 Grade Distribution</h1>
                <p class="text-indigo-100">Select an exam to view grade distribution</p>
            </div>
        </div>

        {{-- Filter Form --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('admin.exams.reports.grade-distribution') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Exam</label>
                    <select name="exam_id" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">-- Select Exam --</option>
                        @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }} ({{ $exam->classSection->full_name ?? 'N/A' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class Section (Optional)</label>
                    <select name="class_section_id" class="w-full rounded-lg border-gray-300">
                        <option value="">All Sections</option>
                        @foreach($classSections as $section)
                        <option value="{{ $section->id }}" {{ request('class_section_id') == $section->id ? 'selected' : '' }}>
                            {{ $section->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition flex-1">
                        📊 View
                    </button>
                    <a href="{{ route('admin.exams.reports.grade-distribution') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        @if(isset($exam) && isset($gradeScaleData) && !empty($gradeScaleData))
        {{-- Distribution Chart --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">📊 Distribution Chart</h2>
                <span class="text-sm text-gray-500">Total Students: {{ $totalStudents }}</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($distribution as $grade => $data)
                @php
                    $percentage = $totalStudents > 0 ? round(($data['count'] / $totalStudents) * 100, 1) : 0;
                    $colors = [
                        'A+' => 'bg-green-500',
                        'A' => 'bg-green-400',
                        'B+' => 'bg-blue-500',
                        'B' => 'bg-blue-400',
                        'C+' => 'bg-yellow-500',
                        'C' => 'bg-yellow-400',
                        'D' => 'bg-orange-500',
                        'F' => 'bg-red-500',
                    ];
                    $color = $colors[$grade] ?? 'bg-gray-500';
                @endphp
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-lg">{{ $grade }}</span>
                        <span class="text-sm text-gray-500">{{ $data['count'] }} students</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div class="{{ $color }} h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="mt-1 text-xs text-gray-400 text-right">{{ $percentage }}%</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Detailed Distribution Table --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-xl font-bold text-gray-800">📋 Detailed Distribution</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Range</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Students</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Percentage</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Visual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($distribution as $grade => $data)
                        @php
                            $percentage = $totalStudents > 0 ? round(($data['count'] / $totalStudents) * 100, 1) : 0;
                            $barWidth = $percentage;
                            $colors = [
                                'A+' => 'bg-green-500',
                                'A' => 'bg-green-400',
                                'B+' => 'bg-blue-500',
                                'B' => 'bg-blue-400',
                                'C+' => 'bg-yellow-500',
                                'C' => 'bg-yellow-400',
                                'D' => 'bg-orange-500',
                                'F' => 'bg-red-500',
                            ];
                            $color = $colors[$grade] ?? 'bg-gray-500';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-lg">{{ $grade }}</td>
                            <td class="px-6 py-4">{{ $data['min'] }}% - {{ $data['max'] }}%</td>
                            <td class="px-6 py-4 text-center font-medium">{{ $data['count'] }}</td>
                            <td class="px-6 py-4 text-center">{{ $percentage }}%</td>
                            <td class="px-6 py-4">
                                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden max-w-xs">
                                    <div class="{{ $color }} h-full rounded-full transition-all duration-500" style="width: {{ $barWidth }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @elseif(request('exam_id'))
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No Data Available</h3>
            <p class="mt-2 text-sm text-gray-500">No results found for the selected exam.</p>
        </div>
        @endif
    </div>
</div>
@endsection