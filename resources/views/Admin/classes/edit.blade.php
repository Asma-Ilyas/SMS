@extends('layouts.app')
@section('title', 'Edit Class')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-6">Edit Class</h1>
        <form method="POST" action="{{ route('admin.classes.update', $class) }}" class="bg-white shadow rounded-lg p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Academic Session</label>
                    <select name="academic_session_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ old('academic_session_id', $class->academic_session_id) == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_session_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Grade</label>
                    <select name="grade_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('grade_id', $class->grade_id) == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('grade_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Stream</label>
                    <select name="stream_id" id="streamSelect" class="w-full border rounded px-3 py-2" required>
                        @foreach($streams as $stream)
                            <option value="{{ $stream->id }}" {{ old('stream_id', $class->stream_id) == $stream->id ? 'selected' : '' }}>
                                {{ $stream->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('stream_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Elective Track</label>
                    <select name="elective_track_id" id="electiveTrackSelect" class="w-full border rounded px-3 py-2">
                        <option value="">None</option>
                        @foreach($electiveTracks as $track)
                            <option value="{{ $track->id }}" 
                                    data-stream="{{ $track->stream_id }}"
                                    {{ old('elective_track_id', $class->elective_track_id) == $track->id ? 'selected' : '' }}>
                                {{ $track->name }} ({{ optional($track->stream)->name ?? 'No Stream' }})
                            </option>
                        @endforeach
                    </select>
                    @error('elective_track_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Section</label>
                    <input type="text" name="section" value="{{ old('section', $class->section) }}" class="w-full border rounded px-3 py-2" required>
                    @error('section')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Capacity</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $class->capacity) }}" class="w-full border rounded px-3 py-2" required>
                    @error('capacity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('admin.classes.index') }}" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update Class</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Filter elective tracks based on selected stream
    document.addEventListener('DOMContentLoaded', function() {
        const streamSelect = document.getElementById('streamSelect');
        const trackSelect = document.getElementById('electiveTrackSelect');

        function filterTracks() {
            const selectedStreamId = streamSelect.value;
            const options = trackSelect.querySelectorAll('option');
            options.forEach(opt => {
                if (opt.value === '') return;
                const trackStream = opt.getAttribute('data-stream');
                if (trackStream == selectedStreamId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });
            if (trackSelect.selectedOptions[0] && trackSelect.selectedOptions[0].style.display === 'none') {
                trackSelect.value = '';
            }
        }

        streamSelect.addEventListener('change', filterTracks);
        filterTracks();
    });
</script>
@endsection