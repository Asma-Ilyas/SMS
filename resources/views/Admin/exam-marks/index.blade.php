@extends('layouts.app')
@section('title', 'Exam Marks Dashboard')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Exam Marks Dashboard</h1>
    <div class="grid gap-4">
        @foreach($exams as $exam)
            <div class="border p-4 rounded flex justify-between items-center">
                <div>
                    <strong>{{ $exam->name }}</strong><br>
                    Class: {{ $exam->class->name ?? '?' }} {{ $exam->class->section ?? '' }}
                </div>
                <a href="{{ route('admin.exam-marks.create', ['exam_id' => $exam->id]) }}" class="bg-blue-600 text-white px-3 py-1 rounded">Enter Marks</a>
            </div>
        @endforeach
    </div>
</div>
@endsection