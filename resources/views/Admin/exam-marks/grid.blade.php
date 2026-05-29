@extends('layouts.app')
@section('title', 'Enter Marks')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold">{{ $exam->name }}</h1>
    <p>Class: {{ $class->name }} {{ $class->section ?? '' }} | Subject: {{ $subject->name }}</p>
    <form method="POST" action="{{ route('admin.exam-marks.store') }}">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
        <div class="overflow-x-auto mt-4">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr><th>Student</th><th>Marks (0-100)</th></tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr class="border-b">
                        <td class="p-2">{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td class="p-2"><input type="number" name="marks[{{ $student->id }}]" value="{{ $existingMarks[$student->id]->marks_obtained ?? '' }}" class="border p-1 w-24" step="any" min="0" max="100"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit" class="mt-4 bg-green-600 text-white px-4 py-2 rounded">Save Marks</button>
    </form>
</div>
@endsection