@extends('layouts.app')
@section('title', 'Add Availability Exception')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🚫 Add Unavailability Exception</h1>
                    <p class="text-gray-500 mt-1">Mark a teacher as unavailable for specific time slots</p>
                </div>
                <a href="{{ route('admin.teacher-availability.index', ['teacher_id' => $selectedTeacher->id ?? '']) }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    ← Back
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <form method="POST" action="{{ route('admin.teacher-availability.store') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teacher *</label>
                    <select name="teacher_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ $selectedTeacher && $selectedTeacher->id == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Day *</label>
                    <select name="day_of_week" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Day</option>
                        @foreach($days as $day)
                        <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Slot *</label>
                    <select name="time_slot_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Time Slot</option>
                        @foreach($timeSlots as $slot)
                        <option value="{{ $slot->id }}">{{ $slot->label }} ({{ $slot->start_time }} - {{ $slot->end_time }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason (Optional)</label>
                    <input type="text" name="reason" placeholder="e.g., Medical leave, Training, Meeting" 
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800 text-sm flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span><strong>Note:</strong> Teachers are <span class="font-bold text-green-600">available by default</span> for all slots. Only mark exceptions when they are <span class="font-bold text-red-600">unavailable</span>.</span>
                    </p>
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ route('admin.teacher-availability.index', ['teacher_id' => $selectedTeacher->id ?? '']) }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 rounded-lg transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded-lg transition">
                        🚫 Mark as Unavailable
                    </button>
                </div>
            </form>
        </div>

        {{-- Quick Tips --}}
        <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
            <h3 class="font-bold text-gray-800 mb-3">💡 Quick Tips</h3>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start gap-2">
                    <span class="text-green-500">✅</span>
                    <span><strong>Available by default:</strong> Teachers are available for all slots. No need to mark available slots.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-red-500">🚫</span>
                    <span><strong>Only mark exceptions:</strong> Only mark slots when a teacher is NOT available (leave, training, etc.)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-500">🔄</span>
                    <span><strong>Remove exceptions:</strong> Click "Make Available" to remove an exception when the teacher becomes available again.</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection