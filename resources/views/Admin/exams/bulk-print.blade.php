@extends('layouts.app')
@section('title', 'Bulk Print - ' . $exam->name)
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h1 class="text-2xl font-bold">Bulk Print Results: {{ $exam->name }}</h1>
        </div>
        <form method="POST" action="{{ route('admin.exam-results.bulk-print', $exam) }}" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block font-medium mb-2">Select Students (hold Ctrl/Cmd to multi-select)</label>
                <select name="student_ids[]" multiple required size="10" class="w-full border rounded-lg p-2">
                    @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Generate PDF</button>
            </div>
        </form>
    </div>
</div>
@endsection