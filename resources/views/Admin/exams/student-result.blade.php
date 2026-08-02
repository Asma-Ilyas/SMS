@extends('layouts.app')
@section('title', 'Student Result')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.exams.show', $exam->id) }}" class="hover:text-indigo-600 transition">← Back</a>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700 font-medium">Student Result</span>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">{{ $student->first_name }} {{ $student->last_name }}</h1>
                        <p class="text-indigo-100">{{ $exam->name }} - {{ $exam->classSection->full_name }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold">{{ $result->percentage ?? 0 }}%</div>
                        <div class="text-sm text-indigo-200">Overall Score</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Result Summary --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
                <p class="text-sm text-gray-500">Total Marks</p>
                <p class="text-2xl font-bold text-gray-800">{{ $result->total_marks ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <p class="text-sm text-gray-500">Max Marks</p>
                <p class="text-2xl font-bold text-green-600">{{ $result->total_max_marks ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <p class="text-sm text-gray-500">Grade</p>
                <p class="text-2xl font-bold text-purple-600">{{ $result->grade ?? 'N/A' }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500">Rank</p>
                <p class="text-2xl font-bold text-yellow-600">#{{ $result->rank_in_class ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Subject-wise Marks --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-xl font-bold text-gray-800">📋 Subject-wise Marks</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Max Marks</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Obtained</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Percentage</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($marks as $index => $mark)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium">{{ $mark->subject->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center">{{ $mark->max_marks }}</td>
                            <td class="px-6 py-4 text-center font-medium">{{ $mark->marks_obtained }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="{{ $mark->percentage >= 40 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $mark->percentage }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($mark->marks_obtained >= $mark->passing_marks)
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

        {{-- Actions --}}
        <div class="mt-6 flex gap-3">
            <a href="{{ route('admin.exams.print-result', ['exam' => $exam->id, 'student' => $student->id]) }}" 
               class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition" target="_blank">
                🖨 Print Result
            </a>
            <a href="{{ route('admin.exams.marks-entry', $exam->id) }}" 
               class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                ✏️ Edit Marks
            </a>
        </div>
    </div>
</div>
@endsection