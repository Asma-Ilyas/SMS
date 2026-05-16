@extends('layouts.app')
@section('title', 'Enter Marks - ' . $exam->name)
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Enter Marks – {{ $exam->name }}</h1>
        <a href="{{ route('admin.exams.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded">Back to Exams</a>
    </div>

    <form method="POST" action="{{ route('admin.exam-results.store-marks', $exam) }}">
        @csrf
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Student</th>
                            @foreach($subjects as $subject)
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">
                                    {{ $subject->name }}<br>
                                    <input type="number" name="max_marks[{{ $subject->id }}]" value="{{ $subject->max_marks ?? 100 }}" class="w-20 text-center border rounded px-2 py-1 text-xs" placeholder="Max">
                                </th>
                            @endforeach
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $student)
                        <tr>
                            <td class="px-4 py-2 font-medium">{{ $student->name }}<br><span class="text-xs text-gray-500">{{ $student->roll_no ?? '' }}</span></td>
                            @foreach($subjects as $subject)
                                @php
                                    $existingMark = $student->marks->where('subject_id', $subject->id)->where('exam_id', $exam->id)->first();
                                @endphp
                                <td class="px-4 py-2 text-center">
                                    <input type="number" name="marks[{{ $student->id }}][{{ $subject->id }}]"
                                           value="{{ $existingMark->marks_obtained ?? '' }}"
                                           class="w-24 text-center border rounded px-2 py-1"
                                           step="any" min="0">
                                </td>
                            @endforeach
                            <td class="px-4 py-2 text-center">
                                <button type="button" onclick="clearStudentMarks(this)" class="text-red-500 text-sm">Clear</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Save All Marks</button>
        </div>
    </form>
</div>

<script>
function clearStudentMarks(btn) {
    let row = btn.closest('tr');
    let inputs = row.querySelectorAll('input[type="number"]');
    inputs.forEach(input => input.value = '');
}
</script>
@endsection