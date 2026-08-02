@extends('layouts.app')
@section('title', 'Add Class')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-6">Add New Class</h1>
        <form method="POST" action="{{ route('admin.classes.store') }}" class="bg-white shadow rounded-lg p-6">
            @csrf
            <div class="space-y-4">
                <!-- Academic Session -->
                <div>
                    <label class="block text-sm font-medium mb-1">Academic Session</label>
                    <select name="academic_session_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Grade -->
                <div>
                    <label class="block text-sm font-medium mb-1">Grade</label>
                    <select name="grade_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Stream -->
                <div>
                    <label class="block text-sm font-medium mb-1">Stream</label>
                    <select name="stream_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($streams as $stream)
                            <option value="{{ $stream->id }}" {{ old('stream_id') == $stream->id ? 'selected' : '' }}>
                                {{ $stream->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Elective Track -->
                <div>
                    <label class="block text-sm font-medium mb-1">Elective Track</label>
                    <select name="elective_track_id" class="w-full border rounded px-3 py-2">
                        <option value="">None</option>
                        @foreach($electiveTracks as $track)
                            <option value="{{ $track->id }}" {{ old('elective_track_id') == $track->id ? 'selected' : '' }}>
                                {{ $track->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Capacity -->
                <div>
                    <label class="block text-sm font-medium mb-1">Capacity</label>
                    <input type="number" name="capacity" value="{{ old('capacity') }}" class="w-full border rounded px-3 py-2" required>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('admin.classes.index') }}" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create Class</button>
            </div>
        </form>
    </div>
</div>
@endsection