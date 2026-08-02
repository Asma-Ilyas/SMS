@extends('layouts.app')
@section('title', 'Teacher Availability - ' . $teacher->first_name . ' ' . $teacher->last_name)

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8">
    {{-- Header with back button --}}
    <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Teacher Availability</h1>
            <p class="text-gray-500 text-sm mt-1 flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                    {{ $teacher->employee_id }}
                </span>
                {{ $teacher->first_name }} {{ $teacher->last_name }}
            </p>
        </div>
        <a href="{{ route('admin.staff.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Staff
        </a>
    </div>

    {{-- Error messages --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-800">Please fix the following errors:</p>
                    <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Info box --}}
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="text-sm text-blue-800">
            <strong>Availability Matrix</strong><br>
            Check the boxes to mark when the teacher is <span class="font-semibold">available</span>. Unchecked slots will be treated as unavailable. This affects automatic timetable generation.
        </div>
    </div>

    {{-- Main form card --}}
    <form method="POST" action="{{ route('admin.teacher-availability.update', $teacher) }}">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/40 flex justify-between items-center flex-wrap gap-2">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Weekly Schedule
                </h2>
                <div class="flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span> Available</span>
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-300"></span> Unavailable</span>
                </div>
            </div>

            {{-- Make table container scrollable horizontally --}}
            <div class="overflow-x-auto custom-scrollbar">
                <table class="min-w-[800px] w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100 sticky left-0 z-10 shadow-sm" style="min-width: 120px;">Day / Time</th>
                            @foreach($timeSlots as $slot)
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    <div class="font-semibold text-gray-700">{{ $slot->label }}</div>
                                    <div class="text-gray-400 text-[10px]">{{ $slot->start_time }} – {{ $slot->end_time }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach(['monday','tuesday','wednesday','thursday','friday'] as $index => $day)
                            @php
                                $dayName = ucfirst($day);
                                $bgClass = $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/30';
                                $dotColors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-400', 'bg-blue-400'];
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition {{ $bgClass }}">
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 bg-white/90 sticky left-0 z-10 shadow-sm {{ $bgClass }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $dotColors[$index] }}"></span>
                                        {{ $dayName }}
                                    </div>
                                </th>
                                @foreach($timeSlots as $slot)
                                    @php
                                        $key = $day . '_' . $slot->id;
                                        $isAvailable = $availabilities[$key]->is_available ?? true;
                                    @endphp
                                    <td class="px-3 py-2 text-center">
                                        <label class="inline-flex items-center justify-center cursor-pointer">
                                            <input type="hidden" name="availability[{{ $day }}][{{ $slot->id }}]" value="0">
                                            <input type="checkbox" name="availability[{{ $day }}][{{ $slot->id }}]" value="1"
                                                   class="availability-toggle w-5 h-5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 transition"
                                                   {{ $isAvailable ? 'checked' : '' }}>
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Bulk actions row --}}
            <div class="px-6 py-3 border-t border-gray-200 bg-gray-50/40 flex justify-between items-center flex-wrap gap-2">
                <div class="flex gap-2">
                    <button type="button" id="check-all" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">Check All</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" id="uncheck-all" class="text-xs text-gray-600 hover:text-gray-800 font-medium transition">Uncheck All</button>
                </div>
                <p class="text-xs text-gray-500">Tip: Use bulk actions to quickly set availability for all slots.</p>
            </div>

            {{-- Form actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('admin.staff.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Availability
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .sticky {
        background-color: inherit;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAllBtn = document.getElementById('check-all');
        const uncheckAllBtn = document.getElementById('uncheck-all');
        const toggles = document.querySelectorAll('.availability-toggle');

        if (checkAllBtn) {
            checkAllBtn.addEventListener('click', (e) => {
                e.preventDefault();
                toggles.forEach(toggle => toggle.checked = true);
            });
        }
        if (uncheckAllBtn) {
            uncheckAllBtn.addEventListener('click', (e) => {
                e.preventDefault();
                toggles.forEach(toggle => toggle.checked = false);
            });
        }
    });
</script>
@endsection