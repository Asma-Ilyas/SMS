{{-- resources/views/classes/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <x-form.card title="Edit Class">
        <form method="POST" action="{{ route('admin.classes.update', $class) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Session --}}
                <x-form.select name="academic_session_id" label="Academic Session *"
                    :options="$sessions->pluck('name', 'id')->toArray()"
                    :selected="$class->academic_session_id" required />

                {{-- Grade --}}
                <x-form.select name="grade_id" label="Grade *"
                    :options="$grades->pluck('name', 'id')->toArray()"
                    :selected="$class->grade_id" required />

                {{-- Stream --}}
                <x-form.select name="stream_id" label="Stream *" id="stream_id"
                    :options="$streams->pluck('name', 'id')->toArray()"
                    :selected="$class->stream_id" required />

                {{-- Elective Track – manual select with data-stream --}}
                <div id="elective_wrapper" class="mb-4" 
                     style="{{ $class->stream->name == 'Science' ? '' : 'display: none;' }}">
                    <label for="elective_track_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Elective Track (for Science)
                    </label>
                    <select name="elective_track_id" id="elective_track_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">None</option>
                        @foreach($electiveTracks as $track)
                            <option value="{{ $track->id }}" 
                                    data-stream="{{ $track->stream_id }}"
                                    {{ old('elective_track_id', $class->elective_track_id) == $track->id ? 'selected' : '' }}>
                                {{ $track->name }} ({{ $track->stream->name }})
                            </option>
                        @endforeach
                    </select>
                    <x-form.error name="elective_track_id" />
                </div>

                {{-- Section --}}
                <x-form.input name="section" label="Section" :value="$class->section" />

                {{-- Capacity --}}
                <x-form.input name="capacity" label="Capacity" type="number" :value="$class->capacity" />
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                <x-form.button variant="secondary" type="button" onclick="window.history.back()">Cancel</x-form.button>
                <x-form.button variant="primary">Update Class</x-form.button>
            </div>
        </form>
    </x-form.card>

    @push('scripts')
    <script>
        const streamSelect = document.getElementById('stream_id');
        const electiveWrapper = document.getElementById('elective_wrapper');
        const electiveSelect = electiveWrapper?.querySelector('select');

        function updateElectiveTracks() {
            const selectedStreamId = streamSelect.value;
            const selectedStreamName = streamSelect.options[streamSelect.selectedIndex]?.text;

            if (selectedStreamName === 'Science') {
                electiveWrapper.style.display = 'block';
                // Filter options based on data-stream
                for (let i = 0; i < electiveSelect.options.length; i++) {
                    const opt = electiveSelect.options[i];
                    if (opt.value === '') continue; // keep "None"
                    if (opt.getAttribute('data-stream') == selectedStreamId) {
                        opt.style.display = '';
                    } else {
                        opt.style.display = 'none';
                    }
                }
            } else {
                electiveWrapper.style.display = 'none';
                electiveSelect.value = '';
            }
        }

        streamSelect.addEventListener('change', updateElectiveTracks);
        updateElectiveTracks();
    </script>
    @endpush
@endsection