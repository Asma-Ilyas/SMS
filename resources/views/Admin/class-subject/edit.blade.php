@extends('layouts.app')
@section('title', 'Edit Subject Assignment')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Edit Subject Assignment</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.class-subject.update', $classSubject) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block font-medium">Class (Grade / Stream / Section) *</label>
                    <select name="class_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Select --</option>
                        @foreach($formattedClasses as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $classSubject->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->display }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Subject *</label>
                    <select name="subject_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Select --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $classSubject->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name ?? $subject->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Teacher *</label>
                    <select name="teacher_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Select --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $classSubject->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name ?? $teacher->id }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('admin.class-subject.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection