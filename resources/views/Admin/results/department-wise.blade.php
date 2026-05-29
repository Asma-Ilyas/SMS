@extends('layouts.app')
@section('title', 'Department-wise Results')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Department-wise Results</h1>
    <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <select name="exam_id" class="border rounded p-2">
            <option value="">Select Exam</option>
            @foreach($exams as $e)
                <option value="{{ $e->id }}" {{ $examId == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
            @endforeach
        </select>
        <select name="stream_id" class="border rounded p-2">
            <option value="">Select Stream</option>
            @foreach($streams as $s)
                <option value="{{ $s->id }}" {{ $streamId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>
        <input type="number" name="from_grade" placeholder="From Grade" value="{{ $fromGrade }}" class="border rounded p-2">
        <input type="number" name="to_grade" placeholder="To Grade" value="{{ $toGrade }}" class="border rounded p-2">
        <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">Filter</button>
    </form>

    @if($results->count())
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr><th>Student</th><th>Class</th><th>Percentage</th><th>Grade</th><th>Remarks</th></tr>
                </thead>
                <tbody>
                    @foreach($results as $r)
                    <tr>
                        <td class="p-2">{{ $r->student->first_name }} {{ $r->student->last_name }}</td>
                        <td class="p-2">{{ $r->class->name ?? '' }} {{ $r->class->section ?? '' }}</td>
                        <td class="p-2 text-center">{{ $r->percentage }}%</td>
                        <td class="p-2 text-center">{{ $r->grade }}</td>
                        <td class="p-2 text-center">{{ $r->remarks }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>No results found.</p>
    @endif
</div>
@endsection