@extends('layouts.app')
@section('title', 'Grade Distribution - Select')

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

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <form method="GET" action="{{ route('admin.exams.reports.grade-distribution') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Exam *</label>
                    <select name="exam_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select Exam --</option>
                        @foreach($exams as $exam)
                        <option value="{{ $exam->id }}">
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
                        <option value="{{ $section->id }}">
                            {{ $section->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        📊 View Distribution
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection