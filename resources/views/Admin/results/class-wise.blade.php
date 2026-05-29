@extends('layouts.app')
@section('title', 'Class-wise Results')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Class-wise Results</h1>
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <select name="exam_id" class="border rounded p-2">
            <option value="">Select Exam</option>
            @foreach($exams as $e)
                <option value="{{ $e->id }}" {{ $examId == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
            @endforeach
        </select>
        <select name="class_id" class="border rounded p-2">
            <option value="">Select Class</option>
            @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                    {{ $c->grade->name ?? '' }} {{ $c->stream->name ?? '' }} - {{ $c->section ?? '' }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">Filter</button>
    </form>

    @if($results->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr><th>Rank</th><th>Student Name</th><th>Total</th><th>Percentage</th><th>Grade</th><th>Remarks</th></tr>
                </thead>
                <tbody>
                    @foreach($results as $r)
                    <tr>
                        <td class="p-2 text-center">{{ $r->rank_in_class }}</td>
                        <td class="p-2">{{ $r->student->first_name }} {{ $r->student->last_name }}</td>
                        <td class="p-2 text-center">{{ $r->total_marks }}/{{ $r->total_max_marks }}</td>
                        <td class="p-2 text-center">{{ $r->percentage }}%</td>
                        <td class="p-2 text-center">{{ $r->grade }}</td>
                        <td class="p-2 text-center">{{ $r->remarks }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500">No results found. Please select exam and class.</p>
    @endif
</div>
@endsection