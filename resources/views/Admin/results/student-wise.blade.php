@extends('layouts.app')
@section('title', 'Student-wise Report')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Student-wise Results</h1>
    <form method="GET" class="flex gap-4 mb-6">
        <select name="student_id" class="border rounded p-2 w-64">
            <option value="">Select Student</option>
            @foreach($students as $s)
                <option value="{{ $s->id }}" {{ $studentId == $s->id ? 'selected' : '' }}>{{ $s->first_name }} {{ $s->last_name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">View</button>
    </form>

    @if(count($results) > 0)
        <div class="space-y-6">
            @foreach($results as $result)
                <div class="border p-4 rounded">
                    <h2 class="text-xl font-semibold">{{ $result->exam->name }}</h2>
                    <p>Total: {{ $result->total_marks }}/{{ $result->total_max_marks }} | Percentage: {{ $result->percentage }}% | Grade: {{ $result->grade }} | Rank: {{ $result->rank_in_class }}</p>
                    <p>Remarks: {{ $result->remarks }}</p>
                </div>
            @endforeach
        </div>
    @elseif($studentId)
        <p>No results found for this student.</p>
    @endif
</div>
@endsection