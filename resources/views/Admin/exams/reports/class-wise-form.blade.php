{{-- resources/views/admin/exams/reports/class-wise-form.blade.php --}}

@extends('layouts.app')

@section('title', 'Class-wise Report')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🏫 Class-wise Report</h1>
                    <p class="text-gray-500 mt-1">Select class and exam to generate report</p>
                </div>
                <a href="{{ route('admin.exams.reports.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    ← Back
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <form method="GET" action="{{ route('admin.exams.reports.class-wise') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class Section *</label>
                    <select name="class_section_id" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Select Class --</option>
                        @foreach($classSections as $section)
                            <option value="{{ $section->id }}" {{ request('class_section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->full_name ?? $section->section_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Exam *</label>
                    <select name="exam_id" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Select Exam --</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                {{ $exam->name }} ({{ $exam->start_date ? date('d M Y', strtotime($exam->start_date)) : 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                        📊 Generate Report
                    </button>
                    <a href="{{ route('admin.exams.reports.class-wise-form') }}" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection