@extends('layouts.app')
@section('title', 'Edit Timetable Entry')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Edit Timetable Entry</h1>
        <form method="POST" action="{{ route('admin.timetable.update', $timetable) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <!-- Combined Grade / Stream / Section Dropdown -->
                <div class="col-span-2">
                    <label class="block font-medium">Grade / Stream / Section *</label>
                    <select name="class_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Select Grade, Stream & Section --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $timetable->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->display }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Day of Week -->
                <div>
                    <label class="block font-medium">Day (1=Mon, 2=Tue, … 7=Sun)</label>
                    <input type="number" name="day_of_week" min="1" max="7" value="{{ old('day_of_week', $timetable->day_of_week) }}" required class="w-full border rounded px-3 py-2">
                </div>

                <!-- Period Number -->
                <div>
                    <label class="block font-medium">Period Number</label>
                    <input type="number" name="period_number" min="1" value="{{ old('period_number', $timetable->period_number) }}" required class="w-full border rounded px-3 py-2">
                </div>

                <!-- Start Time -->
                <div>
                    <label class="block font-medium">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $timetable->start_time) }}" required class="w-full border rounded px-3 py-2">
                </div>

                <!-- End Time -->
                <div>
                    <label class="block font-medium">End Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $timetable->end_time) }}" required class="w-full border rounded px-3 py-2">
                </div>

                <!-- Subject -->
                <div>
                    <label class="block font-medium">Subject</label>
                    <select name="subject_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $timetable->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name ?? $subject->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Teacher -->
                <div>
                    <label class="block font-medium">Teacher</label>
                    <select name="teacher_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $timetable->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name ?? $teacher->id }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('admin.timetable.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection