@extends('layouts.app')
@section('title', 'Multi-Group Academic Report')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-700">
            <h2 class="text-2xl font-bold text-white">Academic Multi Group Report</h2>
        </div>
        <form method="POST" action="{{ route('admin.exam-results.multi-group-report.generate') }}" class="p-6 space-y-5">
            @csrf
            <div>
                <label class="block font-medium">Select Student</label>
                <select name="student_id" required class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Choose Student --</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->class->full_name ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-medium">Select Exam Groups (Ctrl+Click for multiple)</label>
                <select name="group_ids[]" multiple required size="6" class="w-full border rounded-lg px-3 py-2">
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Generate PDF Report</button>
            </div>
        </form>
    </div>
</div>
@endsection