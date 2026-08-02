@extends('layouts.app')

@section('title', isset($timing) && $timing->exists ? 'Edit School Timing' : 'Add School Timing')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ isset($timing) && $timing->exists ? 'Edit School Timing' : 'Add New School Timing' }}</h1>
        <a href="{{ route('admin.school-timings.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">Back</a>
    </div>

    <!-- Academic Session Selector + Action Dropdown -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <label class="block text-sm font-medium text-gray-700 mb-2">Select Academic Session & Proceed</label>
        <div class="flex flex-wrap gap-3">
            <select id="sessionSelector" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Select an academic session --</option>
                @foreach(\App\Models\SchoolTiming::orderBy('session_start', 'desc')->get() as $s)
                    <option value="{{ $s->id }}" {{ isset($timing) && $timing->id == $s->id ? 'selected' : '' }}>
                        {{ $s->session_name }} ({{ $s->session_start->format('Y-m-d') }} to {{ $s->session_end->format('Y-m-d') }})
                    </option>
                @endforeach
            </select>

            <select id="actionSelector" class="w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="edit">✏️ Edit Session</option>
                <option value="slots">⏱️ Manage Slots</option>
                <option value="timetable">📅 View Timetable</option>
            </select>

            <button type="button" onclick="proceedWithAction()" class="bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700">
                Proceed →
            </button>
            <a href="{{ route('admin.school-timings.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Create New Session</a>
        </div>
    </div>

    <!-- Create / Edit Form -->
    <form method="POST" action="{{ isset($timing) && $timing->exists ? route('admin.school-timings.update', $timing) : route('admin.school-timings.store') }}" class="bg-white rounded-xl shadow-md p-6 space-y-6">
        @csrf
        @if(isset($timing) && $timing->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Session Name -->
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">Session Name</label>
                <input type="text" name="session_name" value="{{ old('session_name', $timing->session_name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                @error('session_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Period Duration -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Period Duration (minutes)</label>
                <input type="number" name="period_duration" value="{{ old('period_duration', $timing->period_duration ?? 45) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                @error('period_duration')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Is Active -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="is_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="0" {{ old('is_active', $timing->is_active ?? false) ? '' : 'selected' }}>Inactive</option>
                    <option value="1" {{ old('is_active', $timing->is_active ?? false) ? 'selected' : '' }}>Active</option>
                </select>
                @error('is_active')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Session Start Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Session Start Date</label>
                <input type="date" name="session_start" 
                       value="{{ old('session_start', isset($timing) && $timing->session_start ? $timing->session_start->format('Y-m-d') : '') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                @error('session_start')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Session End Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Session End Date</label>
                <input type="date" name="session_end" 
                       value="{{ old('session_end', isset($timing) && $timing->session_end ? $timing->session_end->format('Y-m-d') : '') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                @error('session_end')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- School Start Time -->
            <div>
                <label class="block text-sm font-medium text-gray-700">School Start Time</label>
                <input type="time" name="school_start" value="{{ old('school_start', $timing->school_start ?? '08:00') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                @error('school_start')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- School End Time -->
            <div>
                <label class="block text-sm font-medium text-gray-700">School End Time</label>
                <input type="time" name="school_end" value="{{ old('school_end', $timing->school_end ?? '14:00') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                @error('school_end')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium">
                {{ isset($timing) && $timing->exists ? 'Update' : 'Create' }}
            </button>
            <a href="{{ route('admin.school-timings.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg">Cancel</a>
        </div>
    </form>
</div>

<script>
    function proceedWithAction() {
        let sessionId = document.getElementById('sessionSelector').value;
        if (!sessionId) {
            alert('Please select an academic session first.');
            return;
        }

        let action = document.getElementById('actionSelector').value;
        let url = '';

        switch (action) {
            case 'edit':
                url = '{{ route("admin.school-timings.edit", ":id") }}'.replace(':id', sessionId);
                break;
            case 'slots':
                url = '{{ route("admin.slots.index", ":id") }}'.replace(':id', sessionId);
                break;
            case 'timetable':
                url = '{{ route("admin.reports.index") }}?timing_id=' + sessionId;
                break;
        }

        if (url) {
            window.location.href = url;
        }
    }
</script>
@endsection