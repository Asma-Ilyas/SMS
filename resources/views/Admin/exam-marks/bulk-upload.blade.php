@extends('layouts.app')
@section('title', 'Bulk Upload Marks')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.exam-marks.index') }}" class="hover:text-indigo-600 transition">← Back</a>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700 font-medium">Bulk Upload Marks</span>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                <h1 class="text-3xl font-bold">📤 Bulk Upload Marks</h1>
                <p class="text-indigo-100">Upload marks using Excel or CSV file</p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <form method="POST" action="{{ route('admin.exam-marks.bulk-upload.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Exam *</label>
                        <select name="exam_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Exam --</option>
                            @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->name }} ({{ $exam->classSection->full_name ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class Section *</label>
                        <select name="class_section_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Class --</option>
                            @foreach($classSections as $section)
                            <option value="{{ $section->id }}">{{ $section->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                        <select name="subject_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Subject --</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload File *</label>
                        <input type="file" name="file" required accept=".csv,.xlsx,.xls" 
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Supported formats: CSV, XLSX, XLS</p>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800 text-sm flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span><strong>File Format:</strong> CSV file with columns: <strong>student_id, marks_obtained</strong></span>
                    </p>
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ route('admin.exam-marks.index') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2.5 rounded-lg transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition">
                        📤 Upload Marks
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-medium text-blue-800 mb-2">📋 Sample CSV Format</h3>
            <div class="bg-white rounded p-3 border border-gray-200 text-sm font-mono overflow-x-auto">
                <div class="text-gray-600">student_id,marks_obtained</div>
                <div>101,75</div>
                <div>102,82</div>
                <div>103,45</div>
                <div>104,38</div>
            </div>
        </div>
    </div>
</div>
@endsection