@extends('layouts.app')

@section('title', 'Section-wise Timetable')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pb-5 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📋 Section-wise Timetable</h1>
                <p class="text-sm text-gray-500 mt-1">
                    @if(isset($activeTiming) && $activeTiming)
                        <span class="text-green-600">✅ Active Session: {{ $activeTiming->name ?? 'Active' }}</span>
                        <span class="text-gray-400 ml-2">({{ $timeSlots->count() ?? 0 }} time slots)</span>
                    @else
                        <span class="text-red-500">⚠️ No active timing session found</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.timetable-reports.index') }}" 
                   class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">← Back</a>
                
                @if(isset($classSectionId) && $classSectionId > 0 && isset($activeTiming) && $activeTiming)
                    <a href="{{ route('admin.timetable-reports.print', ['class_section_id' => $classSectionId]) }}"
                       target="_blank" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">🖨 Print</a>
                    <a href="{{ route('admin.timetable-reports.edit-section', $classSectionId) }}"
                       class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">✏️ Edit</a>
                    <a href="{{ route('admin.timetable-reports.export', ['class_section_id' => $classSectionId]) }}"
                       class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">📥 Export</a>
                @endif
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

        {{-- Filter Bar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 mb-6">
            <form method="GET" action="{{ route('admin.timetable-reports.section') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Section</label>
                    <select name="class_section_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- All Sections --</option>
                        @foreach($classSections ?? [] as $section)
                            <option value="{{ $section->id }}" {{ ($classSectionId ?? 0) == $section->id ? 'selected' : '' }}>
                                {{ $section->full_name ?? $section->section_name ?? 'Section' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    🔍 Filter
                </button>
                
                @if(isset($classSectionId) && $classSectionId > 0)
                    <a href="{{ route('admin.timetable-reports.section') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        ✕ Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Timetable Display --}}
        @if(isset($selectedSection) && $selectedSection && isset($activeTiming) && $activeTiming)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800">
                        {{ $selectedSection->full_name ?? $selectedSection->section_name ?? 'Section' }}
                        <span class="text-sm font-normal text-gray-500 ml-2">
                            ({{ $entries->count() ?? 0 }} entries)
                        </span>
                    </h2>
                    <span class="text-xs text-gray-500">{{ $timeSlots->count() ?? 0 }} time slots</span>
                </div>
                
                @if(isset($timeSlots) && $timeSlots->count() > 0)
                <div class="overflow-x-auto p-4">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-100/70">
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-b sticky left-0 bg-gray-100/70 z-10 w-32">Day</th>
                                @foreach($timeSlots as $slot)
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-b min-w-[150px]">
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
                                $grouped = $entries->groupBy('day_of_week');
                            @endphp
                            
                            @foreach($days as $index => $day)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-gray-900 capitalize sticky left-0 bg-white border-r">
                                        {{ $dayLabels[$index] }}
                                    </td>
                                    @foreach($timeSlots as $slot)
                                        @php
                                            $entry = $grouped->get($day, collect())->firstWhere('time_slot_id', $slot->id);
                                        @endphp
                                        <td class="px-3 py-3 border-r last:border-r-0 align-top text-center">
                                            @if($slot->type === 'period')
                                                @if($entry)
                                                    <div class="bg-white border border-gray-200 rounded-lg p-2 hover:shadow-md transition shadow-sm">
                                                        <div class="font-bold text-gray-900 text-sm">{{ $entry->subject->name ?? 'N/A' }}</div>
                                                        <div class="text-xs text-gray-600 mt-0.5">{{ $entry->teacher->full_name ?? $entry->teacher->name ?? 'N/A' }}</div>
                                                        <div class="text-[10px] text-gray-400 mt-0.5">Room: {{ $entry->room->name ?? 'N/A' }}</div>
                                                        <button type="button" 
                                                                onclick="window.location.href='{{ route('admin.timetable-reports.edit-entry', $entry->id) }}'" 
                                                                class="mt-1 text-[10px] text-indigo-600 hover:text-indigo-800">
                                                            ✎ Edit
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="flex items-center justify-center h-16 border border-dashed border-gray-200 rounded-lg text-gray-300 text-xs italic">
                                                        Empty
                                                    </div>
                                                @endif
                                            @else
                                                <div class="py-4">
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded 
                                                        {{ $slot->type === 'break' ? 'bg-amber-100/80 text-amber-800' : 'bg-emerald-100/80 text-emerald-800' }} 
                                                        uppercase tracking-wider">
                                                        {{ ucfirst($slot->type) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-8 text-center text-gray-500">
                    <div class="text-4xl mb-2">⏰</div>
                    <p>No time slots configured for the active session.</p>
                    <p class="text-sm text-gray-400 mt-1">Please add time slots to the active timing session.</p>
                </div>
                @endif
            </div>

            {{-- Summary Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                    <p class="text-xs text-gray-500">Total Periods</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $entries->where('timeSlot.is_break', false)->count() ?? 0 }}
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                    <p class="text-xs text-gray-500">Breaks</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $entries->where('timeSlot.is_break', true)->count() ?? 0 }}
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                    <p class="text-xs text-gray-500">Days Covered</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $entries->groupBy('day_of_week')->count() ?? 0 }}/5
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                    <p class="text-xs text-gray-500">Time Slots</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $timeSlots->count() ?? 0 }}</p>
                </div>
            </div>

        @elseif(isset($classSectionId) && $classSectionId > 0 && !isset($activeTiming))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                <p class="text-sm text-red-700">⚠️ No active timing session found. Please activate a timing session first.</p>
            </div>
        @elseif(isset($classSectionId) && $classSectionId > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                <p class="text-sm text-yellow-700">💡 No timetable entries found for this section.</p>
                <p class="text-sm text-yellow-600 mt-1">Please generate the timetable or add entries manually.</p>
            </div>
        @else
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                <p class="text-sm text-blue-700">💡 Please select a section and click "Filter" to view the timetable.</p>
            </div>
        @endif

    </div>
</div>
@endsection

@push('styles')
<style>
    .sticky {
        position: sticky;
    }
    .left-0 {
        left: 0;
    }
    .z-10 {
        z-index: 10;
    }
</style>
@endpush