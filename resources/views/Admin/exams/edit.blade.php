@extends('layouts.app')
@section('title', 'Edit Exam')
@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-yellow-600 to-orange-600">
            <h2 class="text-2xl font-bold text-white">Edit Exam</h2>
        </div>
        <form method="POST" action="{{ route('admin.exams.update', $exam) }}" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label>Exam Type *</label>
                    <select name="exam_type_id" required class="w-full border rounded-lg px-3 py-2">
                        @foreach($examTypes as $type)
                            <option value="{{ $type->id }}" {{ old('exam_type_id', $exam->exam_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Exam Group</label>
                    <select name="exam_group_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">-- None --</option>
                        @foreach($examGroups as $group)
                            <option value="{{ $group->id }}" {{ old('exam_group_id', $exam->exam_group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Class *</label>
                    <select name="class_id" required class="w-full border rounded-lg px-3 py-2">
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->grade->name ?? '' }} {{ $class->stream->name ?? '' }} - {{ $class->section }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Exam Name *</label>
                    <input type="text" name="name" value="{{ old('name', $exam->name) }}" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label>Exam Center</label>
                    <input type="text" name="exam_center" value="{{ old('exam_center', $exam->exam_center) }}" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label>Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $exam->start_date->format('Y-m-d')) }}" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label>End Date *</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $exam->end_date->format('Y-m-d')) }}" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label>Time Table (PDF/Image)</label>
                    @if($exam->time_table)
                        <div class="mb-1"><a href="{{ Storage::url($exam->time_table) }}" target="_blank" class="text-blue-600 text-sm">Current Timetable</a></div>
                    @endif
                    <input type="file" name="time_table" accept=".pdf,.doc,.docx,.jpg,.png" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label>Description</label>
                    <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('description', $exam->description) }}</textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update Exam</button>
            </div>
        </form>
    </div>
</div>
@endsection