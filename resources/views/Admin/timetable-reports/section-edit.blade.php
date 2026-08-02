@extends('layouts.app')

@section('title', 'Edit Section Timetable')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pb-5 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">✏️ Edit Section Timetable</h1>
                <p class="text-sm text-gray-500 mt-1">
                    @if(isset($section))
                        Editing: <span class="font-semibold text-gray-700">{{ $section->full_name ?? $section->section_name ?? 'Section' }}</span>
                    @endif
                    @if(isset($activeTiming) && $activeTiming)
                        <span class="ml-2 text-xs text-green-600">(Active Session: {{ $activeTiming->name ?? 'Active' }})</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.timetable-reports.section') }}" 
                   class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">← Back to Section View</a>
                <a href="{{ route('admin.timetable-reports.print', ['class_section_id' => $section->id ?? 0]) }}" 
                   target="_blank" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">🖨 Print</a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        {{-- Edit Form --}}
        <form method="POST" action="{{ route('admin.timetable-reports.update-section', $section->id ?? 0) }}" class="space-y-6">
            @csrf
            @method('POST')

            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <span class="font-bold text-gray-800">📋 Timetable Matrix</span>
                    <span class="text-xs text-gray-500">Click on any cell to edit</span>
                </div>

                <div class="overflow-x-auto p-4">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-100/70">
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-b w-32">Day</th>
                                @foreach($timeSlots ?? [] as $slot)
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-b min-w-[180px]">
                                        <div>{{ $slot->label ?? $slot->start_time }}</div>
                                        <div class="text-[10px] font-normal text-gray-400">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                        </div>
                                        @if($slot->type === 'break' || $slot->type === 'activity')
                                            <span class="inline-block mt-1 px-2 py-0.5 bg-amber-100 text-amber-800 rounded text-[10px] uppercase">{{ $slot->type }}</span>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @php
                                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                                $dayLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                            @endphp
                            
                            @foreach($days as $index => $day)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-gray-900 bg-gray-50/30 border-r capitalize">
                                        {{ $dayLabels[$index] }}
                                    </td>
                                    @foreach($timeSlots ?? [] as $slot)
                                        @php
                                            $entry = isset($entries[$day]) ? $entries[$day]->firstWhere('time_slot_id', $slot->id) : null;
                                            $fieldName = "entries[{$day}][{$slot->id}]";
                                        @endphp
                                        <td class="px-2 py-2 border-r last:border-r-0 align-top">
                                            @if($slot->type === 'period')
                                                <div class="space-y-2">
                                                    <input type="hidden" name="{{ $fieldName }}[day_of_week]" value="{{ $day }}">
                                                    <input type="hidden" name="{{ $fieldName }}[time_slot_id]" value="{{ $slot->id }}">
                                                    
                                                    <select name="{{ $fieldName }}[subject_id]" 
                                                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="">-- Subject --</option>
                                                        @foreach($subjects ?? [] as $subject)
                                                            <option value="{{ $subject->id }}" {{ ($entry->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                                                                {{ $subject->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    
                                                    <select name="{{ $fieldName }}[teacher_id]" 
                                                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="">-- Teacher --</option>
                                                        @foreach($teachers ?? [] as $teacher)
                                                            <option value="{{ $teacher->id }}" {{ ($entry->teacher_id ?? '') == $teacher->id ? 'selected' : '' }}>
                                                                {{ $teacher->full_name ?? $teacher->name ?? 'Teacher' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    
                                                    <select name="{{ $fieldName }}[room_id]" 
                                                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="">-- Room --</option>
                                                        @foreach($rooms ?? [] as $room)
                                                            <option value="{{ $room->id }}" {{ ($entry->room_id ?? '') == $room->id ? 'selected' : '' }}>
                                                                {{ $room->name }} (Capacity: {{ $room->capacity ?? 'N/A' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    
                                                    @if($entry)
                                                        <div class="text-[10px] text-gray-400 mt-1">
                                                            Current: <span class="font-medium text-gray-600">{{ $entry->subject->name ?? 'N/A' }}</span>
                                                            <span class="mx-1">|</span>
                                                            <span class="font-medium text-gray-600">{{ $entry->teacher->full_name ?? 'N/A' }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="py-4 text-center">
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded 
                                                        {{ $slot->type === 'break' ? 'bg-amber-100/80 text-amber-800' : 'bg-emerald-100/80 text-emerald-800' }} 
                                                        uppercase tracking-wider">
                                                        {{ ucfirst($slot->type) }}
                                                    </span>
                                                    <input type="hidden" name="{{ $fieldName }}[subject_id]" value="">
                                                    <input type="hidden" name="{{ $fieldName }}[teacher_id]" value="">
                                                    <input type="hidden" name="{{ $fieldName }}[room_id]" value="">
                                                    <input type="hidden" name="{{ $fieldName }}[day_of_week]" value="{{ $day }}">
                                                    <input type="hidden" name="{{ $fieldName }}[time_slot_id]" value="{{ $slot->id }}">
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-gray-200">
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                        💾 Save Changes
                    </button>
                    <a href="{{ route('admin.timetable-reports.section') }}" 
                       class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">
                        Cancel
                    </a>
                </div>
                <div class="text-sm text-gray-500">
                    <span class="font-medium">{{ count($timeSlots ?? []) }}</span> time slots × 
                    <span class="font-medium">5</span> days = 
                    <span class="font-medium">{{ count($timeSlots ?? []) * 5 }}</span> total cells
                </div>
            </div>
        </form>

        {{-- Legend --}}
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Legend</h4>
            <div class="flex flex-wrap gap-4 text-sm">
                <span class="flex items-center gap-2">
                    <span class="inline-block w-4 h-4 bg-indigo-600 rounded"></span>
                    <span class="text-gray-600">Period Cell</span>
                </span>
                <span class="flex items-center gap-2">
                    <span class="inline-block w-4 h-4 bg-amber-100 border border-amber-300 rounded"></span>
                    <span class="text-gray-600">Break</span>
                </span>
                <span class="flex items-center gap-2">
                    <span class="inline-block w-4 h-4 bg-emerald-100 border border-emerald-300 rounded"></span>
                    <span class="text-gray-600">Activity</span>
                </span>
                <span class="flex items-center gap-2 text-gray-400">
                    <span class="text-sm">💡</span>
                    <span>Select subject, teacher, and room for each period cell</span>
                </span>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Optional: Auto-save or validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Check if any required fields are empty
                const selects = form.querySelectorAll('select[name*="[subject_id]"]');
                let hasEmpty = false;
                let emptyCount = 0;
                
                selects.forEach(function(select) {
                    // Only check if it's a period cell (visible select)
                    if (select.closest('td') && !select.closest('td').querySelector('input[type="hidden"]')) {
                        if (!select.value) {
                            hasEmpty = true;
                            emptyCount++;
                            select.style.borderColor = '#ef4444';
                            select.style.backgroundColor = '#fef2f2';
                        } else {
                            select.style.borderColor = '';
                            select.style.backgroundColor = '';
                        }
                    }
                });
                
                if (hasEmpty) {
                    if (!confirm(`You have ${emptyCount} empty period cell(s). Continue anyway?`)) {
                        e.preventDefault();
                        return false;
                    }
                }
                
                return true;
            });
        }
    });
</script>
@endpush