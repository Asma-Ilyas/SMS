@extends('layouts.app')
@section('title', 'My Exam Results')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">My Exam Results</h1>
    @if($exams->isEmpty())
        <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">No results published yet.</div>
    @else
        @foreach($exams as $exam)
        <div class="bg-white rounded-xl shadow-md mb-4 overflow-hidden">
            <div class="px-6 py-3 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold">{{ $exam->name }} ({{ $exam->start_date->format('M Y') }})</h3>
                <a href="{{ route('student.exam.result.detail', [$exam, $student]) }}" class="text-blue-600 hover:underline">View Details →</a>
            </div>
            <div class="p-4">
                @php
                    $marks = \App\Models\ExamMark::where('exam_id', $exam->id)->where('student_id', $student->id)->get();
                    $total = $marks->sum('marks_obtained');
                    $max = $marks->sum('max_marks');
                    $percent = $max ? round(($total/$max)*100,2) : 0;
                @endphp
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div><span class="text-gray-500">Total Marks:</span> {{ $total }} / {{ $max }}</div>
                    <div><span class="text-gray-500">Percentage:</span> {{ $percent }}%</div>
                    <div><span class="text-gray-500">Grade:</span> <span class="font-bold">{{ \App\Helpers\GradeHelper::getGrade($percent) }}</span></div>
                    <div><span class="text-gray-500">Result:</span> {{ $percent >= 40 ? 'PASS' : 'FAIL' }}</div>
                </div>
            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection