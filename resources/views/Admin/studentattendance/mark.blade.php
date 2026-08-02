{{-- resources/views/admin/studentattendance/mark.blade.php --}}

@extends('layouts.app')

@section('title', 'Mark Student Attendance')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 py-6">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Enhanced Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg shadow-indigo-200">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 tracking-tight">
                                Mark Attendance
                            </h1>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    {{ $classSection->full_name }}
                                </span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span class="inline-flex items-center gap-1.5 text-sm text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ Carbon\Carbon::parse($date)->format('l, F d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons Group -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.studentattendance.index') }}" 
                       class="group px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all duration-300 flex items-center gap-2 font-medium hover:-translate-y-0.5">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back
                    </a>
                    <button onclick="markAll('present')" 
                            class="group px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl transition-all duration-300 flex items-center gap-2 font-medium hover:shadow-lg hover:shadow-emerald-200 hover:-translate-y-0.5">
                        <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                        All Present
                    </button>
                    <button onclick="markAll('absent')" 
                            class="group px-4 py-2.5 bg-gradient-to-r from-rose-500 to-rose-600 text-white rounded-xl transition-all duration-300 flex items-center gap-2 font-medium hover:shadow-lg hover:shadow-rose-200 hover:-translate-y-0.5">
                        <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        All Absent
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center gap-3">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-slate-500">Total Students</div>
                    <div class="text-lg font-bold text-slate-800">{{ count($students) }}</div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center gap-3">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-slate-500">Present</div>
                    <div class="text-lg font-bold text-emerald-600" id="presentCount">0</div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center gap-3">
                <div class="p-2 bg-rose-100 rounded-lg">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-slate-500">Absent</div>
                    <div class="text-lg font-bold text-rose-600" id="absentCount">0</div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center gap-3">
                <div class="p-2 bg-amber-100 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-slate-500">Other</div>
                    <div class="text-lg font-bold text-amber-600" id="otherCount">0</div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <form action="{{ route('admin.studentattendance.store') }}" method="POST" id="attendanceForm">
            @csrf
            <input type="hidden" name="class_section_id" value="{{ $classSection->id }}">
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="subject_id" value="{{ $subjects->first()->id ?? '' }}">

            <!-- Student Cards Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($students as $index => $student)
                    @php
                        $attendance = $attendances[$student->id] ?? null;
                    @endphp
                    
                    <div class="group bg-white rounded-2xl shadow-sm border border-slate-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden" 
                         id="card-{{ $student->id }}">
                        
                        <!-- Card Header -->
                        <div class="p-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-indigo-200">
                                        {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($student->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800 text-sm leading-tight">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </div>
                                        <div class="text-xs text-slate-400">
                                            Roll #{{ $student->roll_number ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-full">
                                    #{{ $loop->iteration }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="p-4 space-y-3">
                            <input type="hidden" name="attendance[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                            
                            <!-- Status Select with Visual Icons -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Attendance Status
                                </label>
                                <select name="attendance[{{ $student->id }}][status]" 
                                        class="status-select w-full appearance-none bg-slate-50 border-2 border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all hover:border-indigo-300 cursor-pointer"
                                        data-student="{{ $student->id }}"
                                        onchange="updateStudentStats()">
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" 
                                            {{ $attendance && $attendance->status == $value ? 'selected' : '' }}
                                            class="py-1">
                                            @switch($value)
                                                @case('present') ✅ Present @break
                                                @case('absent') ❌ Absent @break
                                                @case('late') ⏰ Late @break
                                                @case('half_day') 🌓 Half Day @break
                                                @case('leave') 📝 Leave @break
                                                @case('holiday') 🎉 Holiday @break
                                                @case('on_duty') 💼 On Duty @break
                                            @endswitch
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Half Day Details -->
                            <div class="half-day-details bg-amber-50 rounded-xl p-3 border-2 border-amber-200" 
                                 id="half-day-{{ $student->id }}" 
                                 style="display: {{ $attendance && $attendance->status == 'half_day' ? 'block' : 'none' }}">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-amber-500">🌓</span>
                                    <span class="text-xs font-semibold text-amber-700">Half Day Details</span>
                                </div>
                                <div class="space-y-2">
                                    <select name="attendance[{{ $student->id }}][half_day_type]" 
                                            class="w-full bg-white border border-amber-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                        @foreach($halfDayTypes as $value => $label)
                                            <option value="{{ $value }}" 
                                                {{ $attendance && $attendance->half_day_type == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    <select name="attendance[{{ $student->id }}][half_day_reason]" 
                                            class="half-day-reason-select w-full bg-white border border-amber-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                        <option value="">-- Select Reason --</option>
                                        @foreach($halfDayReasons as $reason)
                                            <option value="{{ $reason->reason }}" 
                                                {{ ($attendance && $attendance->half_day_reason == $reason->reason) ? 'selected' : '' }}>
                                                {{ $reason->reason }}
                                            </option>
                                        @endforeach
                                        <option value="other">Other (Specify)</option>
                                    </select>
                                    
                                    <input type="text" 
                                           name="attendance[{{ $student->id }}][half_day_reason_custom]" 
                                           class="half-day-reason-custom w-full bg-white border border-amber-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                           placeholder="Enter custom reason..."
                                           style="display: none;"
                                           value="{{ $attendance && $attendance->half_day_reason ? $attendance->half_day_reason : '' }}">
                                    
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="text-xs text-amber-600">In Time</label>
                                            <input type="time" 
                                                   name="attendance[{{ $student->id }}][half_day_in_time]" 
                                                   class="w-full bg-white border border-amber-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                                   value="{{ $attendance->half_day_in_time ?? '' }}">
                                        </div>
                                        <div>
                                            <label class="text-xs text-amber-600">Out Time</label>
                                            <input type="time" 
                                                   name="attendance[{{ $student->id }}][half_day_out_time]" 
                                                   class="w-full bg-white border border-amber-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                                   value="{{ $attendance->half_day_out_time ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Late Details -->
                            <div class="late-details bg-amber-50 rounded-xl p-3 border-2 border-amber-200" 
                                 id="late-{{ $student->id }}" 
                                 style="display: {{ $attendance && $attendance->status == 'late' ? 'block' : 'none' }}">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-amber-500">⏰</span>
                                    <span class="text-xs font-semibold text-amber-700">Late Arrival Details</span>
                                </div>
                                <div class="space-y-2">
                                    <div>
                                        <label class="text-xs text-amber-600">Arrival Time</label>
                                        <input type="time" 
                                               name="attendance[{{ $student->id }}][late_arrival_time]" 
                                               class="w-full bg-white border border-amber-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                               value="{{ $attendance->late_arrival_time ?? now()->format('H:i') }}">
                                    </div>
                                    <select name="attendance[{{ $student->id }}][late_reason]" 
                                            class="late-reason-select w-full bg-white border border-amber-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                        <option value="">-- Select Reason --</option>
                                        @foreach($lateReasons as $reason)
                                            <option value="{{ $reason->reason }}" 
                                                {{ ($attendance && $attendance->late_reason == $reason->reason) ? 'selected' : '' }}>
                                                {{ $reason->reason }}
                                            </option>
                                        @endforeach
                                        <option value="other">Other (Specify)</option>
                                    </select>
                                    <input type="text" 
                                           name="attendance[{{ $student->id }}][late_reason_custom]" 
                                           class="late-reason-custom w-full bg-white border border-amber-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                           placeholder="Enter custom reason..."
                                           style="display: none;"
                                           value="{{ $attendance && $attendance->late_reason ? $attendance->late_reason : '' }}">
                                </div>
                            </div>

                            <!-- Check In/Out & Remarks -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-xs text-slate-500">Check In</label>
                                    <input type="time" name="attendance[{{ $student->id }}][check_in]" 
                                           class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                           value="{{ $attendance->check_in ?? '' }}">
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500">Check Out</label>
                                    <input type="time" name="attendance[{{ $student->id }}][check_out]" 
                                           class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                           value="{{ $attendance->check_out ?? '' }}">
                                </div>
                            </div>
                            
                            <div>
                                <label class="text-xs text-slate-500">Remarks</label>
                                <input type="text" name="attendance[{{ $student->id }}][remarks]" 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                       placeholder="Add remarks..."
                                       value="{{ $attendance->remarks ?? '' }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Form Actions -->
            <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-sm text-slate-600">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                            <span id="footerPresent">0</span>
                        </span>
                        <span class="text-slate-300">|</span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-3 h-3 bg-rose-500 rounded-full"></span>
                            <span id="footerAbsent">0</span>
                        </span>
                        <span class="text-slate-300">|</span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                            <span id="footerOther">0</span>
                        </span>
                    </div>
                    <span class="text-xs text-slate-400">
                        Total: <span id="footerTotal">{{ count($students) }}</span> students
                    </span>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-xl transition-all duration-300 flex items-center gap-2 shadow-lg shadow-indigo-200 hover:shadow-xl hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Save Attendance
                    </button>
                    <button type="button" onclick="markAll('present')" 
                            class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold rounded-xl transition-all duration-300 hover:-translate-y-0.5">
                        ✅ All Present
                    </button>
                    <button type="button" onclick="markAll('absent')" 
                            class="px-6 py-3 bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-semibold rounded-xl transition-all duration-300 hover:-translate-y-0.5">
                        ❌ All Absent
                    </button>
                    <a href="{{ route('admin.studentattendance.index') }}" 
                       class="px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl transition-all duration-300 hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize stats
    updateStudentStats();
    
    // Toggle half day and late details
    $('.status-select').on('change', function() {
        const studentId = $(this).data('student');
        const status = $(this).val();
        
        // Hide both details panels first
        $(`#half-day-${studentId}`).slideUp(200);
        $(`#late-${studentId}`).slideUp(200);
        
        // Show relevant panel with animation
        if (status === 'half_day') {
            $(`#half-day-${studentId}`).slideDown(300);
        } else if (status === 'late') {
            $(`#late-${studentId}`).slideDown(300);
        }
        
        // Update statistics
        updateStudentStats();
        
        // Add visual feedback
        const card = $(`#card-${studentId}`);
        card.addClass('ring-2 ring-indigo-200');
        setTimeout(() => {
            card.removeClass('ring-2 ring-indigo-200');
        }, 500);
    });

    // Half day reason dropdown toggle with animation
    $(document).on('change', '.half-day-reason-select', function() {
        const $row = $(this).closest('.half-day-details');
        const $customInput = $row.find('.half-day-reason-custom');
        if ($(this).val() === 'other') {
            $customInput.slideDown(200).focus();
        } else {
            $customInput.slideUp(200).val('');
        }
    });

    // Late reason dropdown toggle with animation
    $(document).on('change', '.late-reason-select', function() {
        const $row = $(this).closest('.late-details');
        const $customInput = $row.find('.late-reason-custom');
        if ($(this).val() === 'other') {
            $customInput.slideDown(200).focus();
        } else {
            $customInput.slideUp(200).val('');
        }
    });
});

function markAll(status) {
    const statusLabel = status.charAt(0).toUpperCase() + status.slice(1);
    
    // Show confirmation with proper icon
    if (!confirm(`⚠️ Mark all ${$studentsCount} students as ${statusLabel}?`)) return;
    
    // Animate all cards
    $('.status-select').each(function() {
        const $select = $(this);
        const card = $select.closest('.card-wrapper');
        
        // Add highlight effect
        $select.closest('.bg-white').addClass('ring-2 ring-indigo-200');
        
        $select.val(status).trigger('change');
        
        setTimeout(() => {
            $select.closest('.bg-white').removeClass('ring-2 ring-indigo-200');
        }, 500);
    });
    
    // Show success toast
    showToast(`✅ All students marked as ${statusLabel}`, 'success');
}

function updateStudentStats() {
    let present = 0, absent = 0, other = 0;
    
    $('.status-select').each(function() {
        const status = $(this).val();
        if (status === 'present') present++;
        else if (status === 'absent') absent++;
        else other++;
    });
    
    // Update all counters with animation
    animateCounter('presentCount', present);
    animateCounter('absentCount', absent);
    animateCounter('otherCount', other);
    animateCounter('footerPresent', present);
    animateCounter('footerAbsent', absent);
    animateCounter('footerOther', other);
}

function animateCounter(elementId, value) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const current = parseInt(element.textContent) || 0;
    const diff = value - current;
    const steps = 10;
    let step = 0;
    
    if (diff === 0) {
        element.textContent = value;
        return;
    }
    
    const interval = setInterval(() => {
        step++;
        const progress = step / steps;
        const currentValue = Math.round(current + (diff * progress));
        element.textContent = currentValue;
        
        if (step >= steps) {
            element.textContent = value;
            clearInterval(interval);
        }
    }, 30);
}

function showToast(message, type = 'info') {
    const colors = {
        success: 'bg-emerald-50 border-emerald-400 text-emerald-800',
        error: 'bg-rose-50 border-rose-400 text-rose-800',
        info: 'bg-blue-50 border-blue-400 text-blue-800'
    };
    
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 ${colors[type]} border-2 rounded-xl px-6 py-4 shadow-2xl flex items-center gap-3 animate-slide-up z-50 max-w-md`;
    toast.innerHTML = `
        <span class="text-2xl">${message.split(' ')[0]}</span>
        <div>
            <p class="font-semibold">${message}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-4 opacity-60 hover:opacity-100 transition-opacity">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        if (toast.parentElement) toast.remove();
    }, 5000);
}

// Auto-save indicator
let autoSaveTimeout;
$(document).on('change', '.status-select, input, select', function() {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(() => {
        // Show auto-save indicator
        const indicator = document.createElement('div');
        indicator.className = 'fixed top-4 right-4 bg-indigo-600 text-white px-4 py-2 rounded-xl shadow-lg text-sm font-medium animate-slide-up z-50';
        indicator.textContent = '💾 Auto-saved draft';
        document.body.appendChild(indicator);
        setTimeout(() => {
            if (indicator.parentElement) indicator.remove();
        }, 2000);
    }, 3000);
});
</script>
@endpush

<style>
/* Smooth animations */
@keyframes slide-up {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.animate-slide-up {
    animation: slide-up 0.3s ease-out;
}

/* Card hover effects */
.group:hover .ring-2 {
    ring-color: #818cf8;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions for status changes */
.half-day-details, .late-details {
    transition: all 0.3s ease;
}

/* Card inner shadow on focus */
select:focus, input:focus {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Status select custom styling */
.status-select option {
    padding: 8px 12px;
}

/* Responsive grid adjustments */
@media (max-width: 640px) {
    .grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection