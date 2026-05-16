{{-- resources/views/admin/classes/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Class')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Create New Class</h2>

        <form method="POST" action="{{ route('admin.classes.store') }}" id="classForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Academic Session --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Academic Session <span class="text-red-500">*</span></label>
                    <select name="academic_session_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Session</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                        @endforeach
                    </select>
                    @error('academic_session_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Grade --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Grade <span class="text-red-500">*</span></label>
                    <select name="grade_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Grade</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                        @endforeach
                    </select>
                    @error('grade_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Stream --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stream <span class="text-red-500">*</span></label>
                    <select name="stream_id" id="stream_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Stream</option>
                        @foreach($streams as $stream)
                            <option value="{{ $stream->id }}" {{ old('stream_id') == $stream->id ? 'selected' : '' }}>{{ $stream->name }}</option>
                        @endforeach
                    </select>
                    @error('stream_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Elective Track (conditional) --}}
                <div id="elective_wrapper" style="display: {{ old('stream_id') && optional($streams->find(old('stream_id')))->name == 'Science' ? 'block' : 'none' }};">
                    <label class="block text-sm font-medium text-gray-700">Elective Track (for Science)</label>
                    <select name="elective_track_id" id="elective_track_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">None</option>
                        @foreach($electiveTracks as $track)
                            <option value="{{ $track->id }}" data-stream="{{ $track->stream_id }}" {{ old('elective_track_id') == $track->id ? 'selected' : '' }}>{{ $track->name }}</option>
                        @endforeach
                    </select>
                    @error('elective_track_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    <p class="text-xs text-gray-500 mt-1">Required only for Science stream.</p>
                </div>

                {{-- Section --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Section</label>
                    <input type="text" name="section" value="{{ old('section') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., A, B">
                    @error('section')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Capacity --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" name="capacity" value="{{ old('capacity') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Max students">
                    @error('capacity')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('admin.classes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Save Class</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const streamSelect = document.getElementById('stream_id');
        const electiveWrapper = document.getElementById('elective_wrapper');
        const electiveSelect = document.getElementById('elective_track_id');

        if (!streamSelect || !electiveWrapper || !electiveSelect) return;

        // Store all original elective track options (excluding the "None" placeholder)
        const allOptions = Array.from(electiveSelect.options).filter(opt => opt.value !== "");

        function updateElectiveTracks() {
            const selectedStreamId = streamSelect.value;
            const selectedStreamText = streamSelect.options[streamSelect.selectedIndex]?.text || '';

            // Check if Science stream is selected
            const isScience = selectedStreamText.toLowerCase() === 'science';

            if (isScience && selectedStreamId) {
                // Filter options that belong to this stream
                const filteredOptions = allOptions.filter(opt => opt.getAttribute('data-stream') == selectedStreamId);
                
                // Rebuild select: keep "None" + filtered options
                electiveSelect.innerHTML = '<option value="">None</option>';
                filteredOptions.forEach(opt => {
                    electiveSelect.appendChild(opt.cloneNode(true));
                });
                
                electiveWrapper.style.display = 'block';
                // If no options, show message maybe
                if (filteredOptions.length === 0) {
                    const msg = document.createElement('p');
                    msg.className = 'text-yellow-600 text-sm mt-1';
                    msg.innerText = 'No elective tracks defined for this science stream.';
                    electiveWrapper.appendChild(msg);
                }
            } else {
                electiveWrapper.style.display = 'none';
                electiveSelect.value = ''; // clear value when hidden
            }
        }

        streamSelect.addEventListener('change', updateElectiveTracks);
        // Run on page load to handle old() values
        updateElectiveTracks();
    })();
</script>
@endpush
@endsection