@extends('layouts.app')
@section('title', 'Teacher Availability - Exceptions')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">👨‍🏫 Teacher Availability</h1>
                    <p class="text-gray-500 mt-1">By default, all teachers are available. Only mark exceptions (unavailable slots).</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.teacher-availability.create') }}" 
                       class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        ➕ Add Exception
                    </a>
                    <a href="{{ route('admin.staff.index') }}" 
                       class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        ← Back
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">{{ session('success') }}</div>
        @endif

        {{-- Select Teacher --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('admin.teacher-availability.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Teacher</label>
                    <select name="teacher_id" class="w-full rounded-lg border-gray-300" onchange="this.form.submit()">
                        <option value="">-- Select Teacher --</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ $selectedTeacher && $selectedTeacher->id == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    @if($selectedTeacher)
                    <a href="{{ route('admin.teacher-availability.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        @if($selectedTeacher)
        {{-- Info Banner --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-blue-800">
                        <strong>{{ $selectedTeacher->first_name }} {{ $selectedTeacher->last_name }}</strong> is 
                        <span class="font-bold text-green-600">available by default</span> for all slots.
                    </p>
                    <p class="text-blue-700 text-sm mt-1">Only slots marked below are <span class="text-red-600 font-bold">UNAVAILABLE (exceptions)</span>.</p>
                </div>
            </div>
        </div>

        {{-- Exceptions Table --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">🚫 Unavailability Exceptions</h2>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500">{{ $exceptions->count() }} exception(s)</span>
                    <a href="{{ route('admin.teacher-availability.create', ['teacher_id' => $selectedTeacher->id]) }}" 
                       class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition">
                        + Add Exception
                    </a>
                </div>
            </div>
            
            @if($exceptions->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Slot</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($exceptions as $index => $exception)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <span class="capitalize font-medium">{{ $exception->day_of_week }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                    {{ $exception->timeSlot->label ?? 'N/A' }}
                                </span>
                                <span class="text-sm text-gray-500 ml-2">
                                    ({{ $exception->timeSlot->start_time ?? '' }} - {{ $exception->timeSlot->end_time ?? '' }})
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{ $exception->reason ?? 'No reason provided' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form method="POST" action="{{ route('admin.teacher-availability.destroy', $exception->id) }}" 
                                      onsubmit="return confirm('Remove this exception? Teacher will become available.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-green-600 hover:text-green-800 transition text-sm flex items-center justify-center gap-1 mx-auto">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Make Available
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Exceptions</h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $selectedTeacher->first_name }} {{ $selectedTeacher->last_name }} is available for all slots.
                </p>
                <a href="{{ route('admin.teacher-availability.create', ['teacher_id' => $selectedTeacher->id]) }}" 
                   class="mt-4 inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                    Add an Exception
                </a>
            </div>
            @endif
        </div>

        {{-- Legend --}}
        <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
            <h3 class="font-bold text-gray-800 mb-3">📖 How It Works</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <p class="font-medium text-green-800">✅ Default: Available</p>
                    <p class="text-green-700 text-xs mt-1">All teachers are available for all slots by default. No records needed.</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <p class="font-medium text-red-800">🚫 Exception: Unavailable</p>
                    <p class="text-red-700 text-xs mt-1">Only mark slots when a teacher is NOT available (leave, training, etc.)</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <p class="font-medium text-blue-800">🔄 Remove Exception</p>
                    <p class="text-blue-700 text-xs mt-1">Click "Make Available" to remove an exception. Teacher becomes available again.</p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Select a Teacher</h3>
            <p class="mt-2 text-sm text-gray-500">Choose a teacher above to view or manage their availability exceptions.</p>
        </div>
        @endif
    </div>
</div>
@endsection