@extends('layouts.app')

@section('title', 'Teacher Attendance')

@section('content')
<div class="container px-4 py-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 mb-6 text-white">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <span>👨‍🏫</span> Teacher Attendance
                </h1>
                <p class="text-indigo-100 text-sm mt-1">Mark and manage teacher attendance</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.teacher-attendance.class-wise') }}" 
                   class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition text-sm font-medium">
                    📊 Class Wise Report
                </a>
                <a href="{{ route('admin.teacher-attendance.arrival-departure') }}" 
                   class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition text-sm font-medium">
                    🕐 Arrival/Departure
                </a>
                <a href="{{ route('admin.teacher-attendance.summary') }}" 
                   class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition text-sm font-medium">
                    📈 Summary
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 mb-6 flex items-center gap-3">
            <span class="text-xl">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Date Filter -->
    <form method="GET" action="{{ route('admin.teacher-attendance.index') }}" class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-slate-100">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1">Date</label>
                <input type="date" name="date" value="{{ $date }}" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-medium">
                🔍 View
            </button>
            <a href="{{ route('admin.teacher-attendance.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">
                🔄 Reset
            </a>
        </div>
    </form>

    <!-- Attendance Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Teacher</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Arrival</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Departure</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Class/Section</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Subject</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Period</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teachers as $teacher)
                        @php
                            $attendance = $teacher->attendances->first();
                            $statusBadge = $attendance ? $attendance->status_badge : 'secondary';
                            $statusLabel = $attendance ? $attendance->status_label : 'Not Marked';
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $teacher->full_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $teacher->employee_id }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($attendance)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $statusBadge }}-100 text-{{ $statusBadge }}-700">
                                        {{ $statusLabel }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">Not Marked</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $attendance ? \Carbon\Carbon::parse($attendance->arrival_time)->format('h:i A') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $attendance ? \Carbon\Carbon::parse($attendance->departure_time)->format('h:i A') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $attendance && $attendance->classSection ? $attendance->classSection->section_name : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $attendance && $attendance->subject ? $attendance->subject->name : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $attendance && $attendance->timeSlot ? $attendance->timeSlot->label : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="openMarkModal({{ $teacher->id }}, '{{ $teacher->full_name }}')" 
                                        class="px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition text-xs font-medium">
                                    Mark
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                <span class="text-2xl block mb-2">👨‍🏫</span>
                                No teachers found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Mark Attendance Modal -->
<div id="markModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Mark Attendance</h3>
            <button onclick="closeMarkModal()" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.teacher-attendance.mark') }}" id="markForm">
            @csrf
            <input type="hidden" name="teacher_id" id="teacher_id">
            <input type="hidden" name="date" value="{{ $date }}">
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Status *</label>
                        <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                            <option value="present">✅ Present</option>
                            <option value="absent">❌ Absent</option>
                            <option value="late">⏰ Late</option>
                            <option value="leave">📋 Leave</option>
                            <option value="on_duty">💼 On Duty</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Teacher</label>
                        <input type="text" id="teacher_name" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Arrival Time</label>
                        <input type="time" name="arrival_time" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Departure Time</label>
                        <input type="time" name="departure_time" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Class Section</label>
                        <select name="class_section_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                            <option value="">Select</option>
                            @foreach($classSections as $section)
                                <option value="{{ $section->id }}">{{ $section->full_name ?? $section->section_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Subject</label>
                        <select name="subject_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                            <option value="">Select</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Period</label>
                        <select name="time_slot_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                            <option value="">Select</option>
                            @foreach($timeSlots as $slot)
                                <option value="{{ $slot->id }}">{{ $slot->label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Remarks</label>
                    <textarea name="remarks" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Any remarks..."></textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeMarkModal()" class="flex-1 px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-medium">Save Attendance</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openMarkModal(id, name) {
        document.getElementById('teacher_id').value = id;
        document.getElementById('teacher_name').value = name;
        document.getElementById('markModal').classList.remove('hidden');
        document.getElementById('markModal').classList.add('flex');
    }

    function closeMarkModal() {
        document.getElementById('markModal').classList.add('hidden');
        document.getElementById('markModal').classList.remove('flex');
    }

    document.getElementById('markModal').addEventListener('click', function(e) {
        if (e.target === this) closeMarkModal();
    });
</script>
@endpush
@endsection