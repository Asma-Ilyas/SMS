@extends('layouts.app')
@section('title', 'Select Exam, Class & Subject')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Mark Entry – Selection</h1>
        <form method="GET" action="{{ route('admin.exam-marks.create') }}">
            <div class="space-y-4">
                <div>
                    <label>Exam</label>
                    <select name="exam_id" class="w-full border rounded p-2" required>
                        <option value="">Select Exam</option>
                        @foreach($exams as $e)
                            <option value="{{ $e->id }}" {{ $selectedExam && $selectedExam->id == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Class</label>
                    <select name="class_id" class="w-full border rounded p-2" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClass && $selectedClass->id == $c->id ? 'selected' : '' }}>{{ $c->name }} {{ $c->section ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Subject</label>
                    <select name="subject_id" class="w-full border rounded p-2" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ $selectedSubject && $selectedSubject->id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Proceed</button>
            </div>
        </form>
    </div>
</div>
@endsection