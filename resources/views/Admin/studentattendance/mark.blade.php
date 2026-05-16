@extends('layouts.app')
@section('title', 'Mark Student Attendance')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Mark Student Attendance</h1>
        <a href="{{ route('admin.studentattendance.index') }}" class="bg-gray-500 text-white px-3 py-1 rounded">Back</a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ $errors->first() }}</div>
    @endif

    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
        <p><strong>Class:</strong> {{ $classId }}</p>
        <p><strong>Subject:</strong> {{ $firstPeriod->subject->name ?? 'N/A' }} (First period of the day)</p>
        <p><strong>Date:</strong> {{ $date }}</p>
        <p><strong>Period:</strong> {{ $firstPeriod->start_time ?? '' }} - {{ $firstPeriod->end_time ?? '' }}</p>
    </div>

    <form method="POST" action="{{ route('admin.studentattendance.store') }}">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Student Name</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($students as $student)
                    @php $att = $attendances[$student->id] ?? null; @endphp
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td class="px-4 py-2 text-center">
                            <select name="attendance[{{ $student->id }}][status]" class="border rounded px-2 py-1" required>
                                <option value="present" {{ $att && $att->status == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ $att && $att->status == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ $att && $att->status == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="half_day" {{ $att && $att->status == 'half_day' ? 'selected' : '' }}>Half Day</option>
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <input type="text" name="attendance[{{ $student->id }}][remarks]" value="{{ $att->remarks ?? '' }}" class="border rounded px-2 py-1 w-full">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Attendance</button>
        </div>
    </form>
</div>
@endsection