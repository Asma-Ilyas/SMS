{{-- resources/views/admin/studentattendance/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Attendance')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">✏️ Edit Attendance</h1>
                    <p class="text-gray-500 mt-1">{{ $attendance->student->first_name ?? 'N/A' }} {{ $attendance->student->last_name ?? '' }} - {{ $attendance->date->format('l, F d, Y') }}</p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <a href="{{ route('admin.studentattendance.show', $attendance->id) }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        ← Back
                    </a>
                    <a href="{{ route('admin.studentattendance.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        📋 Dashboard
                    </a>
                </div>
            </div>
        </div>

        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <form action="{{ route('admin.studentattendance.update', $attendance->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select name="status" id="statusSelect" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ $attendance->status == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Approved</label>
                        <select name="is_approved" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1" {{ $attendance->is_approved ? 'selected' : '' }}>✅ Approved</option>
                            <option value="0" {{ !$attendance->is_approved ? 'selected' : '' }}>⏳ Pending</option>
                        </select>
                    </div>
                </div>

                <!-- Half Day Fields -->
                <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200" id="halfDayFields" 
                     style="display: {{ $attendance->status == 'half_day' ? 'block' : 'none' }}">
                    <h3 class="font-semibold text-blue-800 mb-2">🌓 Half Day Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="half_day_type" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($halfDayTypes as $value => $label)
                                    <option value="{{ $value }}" {{ $attendance->half_day_type == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                            <select name="half_day_reason" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select Reason --</option>
                                @foreach($halfDayReasons as $reason)
                                    <option value="{{ $reason->reason }}" {{ $attendance->half_day_reason == $reason->reason ? 'selected' : '' }}>
                                        {{ $reason->reason }}
                                    </option>
                                @endforeach
                                <option value="other" {{ !in_array($attendance->half_day_reason, $halfDayReasons->pluck('reason')->toArray()) && $attendance->half_day_reason ? 'selected' : '' }}>Other</option>
                            </select>
                            <input type="text" name="half_day_reason_custom" 
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 mt-1"
                                   placeholder="Enter custom reason"
                                   value="{{ !in_array($attendance->half_day_reason, $halfDayReasons->pluck('reason')->toArray()) ? $attendance->half_day_reason : '' }}"
                                   style="display: {{ !in_array($attendance->half_day_reason, $halfDayReasons->pluck('reason')->toArray()) && $attendance->half_day_reason ? 'block' : 'none' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">In Time</label>
                            <input type="time" name="half_day_in_time" value="{{ $attendance->half_day_in_time }}" 
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Out Time</label>
                            <input type="time" name="half_day_out_time" value="{{ $attendance->half_day_out_time }}" 
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Late Fields -->
                <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200" id="lateFields" 
                     style="display: {{ $attendance->status == 'late' ? 'block' : 'none' }}">
                    <h3 class="font-semibold text-yellow-800 mb-2">⏰ Late Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Arrival Time</label>
                            <input type="time" name="late_arrival_time" value="{{ $attendance->late_arrival_time }}" 
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                            <select name="late_reason" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select Reason --</option>
                                @foreach($lateReasons as $reason)
                                    <option value="{{ $reason->reason }}" {{ $attendance->late_reason == $reason->reason ? 'selected' : '' }}>
                                        {{ $reason->reason }}
                                    </option>
                                @endforeach
                                <option value="other" {{ !in_array($attendance->late_reason, $lateReasons->pluck('reason')->toArray()) && $attendance->late_reason ? 'selected' : '' }}>Other</option>
                            </select>
                            <input type="text" name="late_reason_custom" 
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 mt-1"
                                   placeholder="Enter custom reason"
                                   value="{{ !in_array($attendance->late_reason, $lateReasons->pluck('reason')->toArray()) ? $attendance->late_reason : '' }}"
                                   style="display: {{ !in_array($attendance->late_reason, $lateReasons->pluck('reason')->toArray()) && $attendance->late_reason ? 'block' : 'none' }}">
                        </div>
                    </div>
                </div>

                <!-- Leave Fields -->
                <div class="mt-4 p-4 bg-purple-50 rounded-lg border border-purple-200" id="leaveFields" 
                     style="display: {{ $attendance->status == 'leave' ? 'block' : 'none' }}">
                    <h3 class="font-semibold text-purple-800 mb-2">📝 Leave Details</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <input type="text" name="leave_reason" value="{{ $attendance->leave_reason }}" 
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="Enter leave reason">
                    </div>
                </div>

                <!-- Check In/Out -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check In</label>
                        <input type="time" name="check_in" value="{{ $attendance->check_in }}" 
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check Out</label>
                        <input type="time" name="check_out" value="{{ $attendance->check_out }}" 
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Remarks -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">{{ $attendance->remarks }}</textarea>
                </div>

                <div class="mt-6 flex gap-4 flex-wrap">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                        💾 Update Attendance
                    </button>
                    <a href="{{ route('admin.studentattendance.show', $attendance->id) }}" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        Cancel
                    </a>
                    <a href="{{ route('admin.studentattendance.index') }}" class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                        📋 Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('statusSelect');
    const halfDayFields = document.getElementById('halfDayFields');
    const lateFields = document.getElementById('lateFields');
    const leaveFields = document.getElementById('leaveFields');

    function toggleFields() {
        const status = statusSelect.value;
        halfDayFields.style.display = status === 'half_day' ? 'block' : 'none';
        lateFields.style.display = status === 'late' ? 'block' : 'none';
        leaveFields.style.display = status === 'leave' ? 'block' : 'none';
    }

    statusSelect.addEventListener('change', toggleFields);
    toggleFields();

    // Handle custom reason toggle for half day
    const halfDayReasonSelect = document.querySelector('select[name="half_day_reason"]');
    const halfDayCustomInput = document.querySelector('input[name="half_day_reason_custom"]');
    
    if (halfDayReasonSelect && halfDayCustomInput) {
        halfDayReasonSelect.addEventListener('change', function() {
            if (this.value === 'other') {
                halfDayCustomInput.style.display = 'block';
                halfDayCustomInput.focus();
            } else {
                halfDayCustomInput.style.display = 'none';
                halfDayCustomInput.value = '';
            }
        });
    }

    // Handle custom reason toggle for late
    const lateReasonSelect = document.querySelector('select[name="late_reason"]');
    const lateCustomInput = document.querySelector('input[name="late_reason_custom"]');
    
    if (lateReasonSelect && lateCustomInput) {
        lateReasonSelect.addEventListener('change', function() {
            if (this.value === 'other') {
                lateCustomInput.style.display = 'block';
                lateCustomInput.focus();
            } else {
                lateCustomInput.style.display = 'none';
                lateCustomInput.value = '';
            }
        });
    }
});

// Handle form submission - copy custom reasons to main fields
document.querySelector('form').addEventListener('submit', function(e) {
    const halfDayCustom = document.querySelector('input[name="half_day_reason_custom"]');
    const halfDaySelect = document.querySelector('select[name="half_day_reason"]');
    if (halfDayCustom && halfDayCustom.style.display !== 'none' && halfDayCustom.value) {
        halfDaySelect.value = halfDayCustom.value;
    }

    const lateCustom = document.querySelector('input[name="late_reason_custom"]');
    const lateSelect = document.querySelector('select[name="late_reason"]');
    if (lateCustom && lateCustom.style.display !== 'none' && lateCustom.value) {
        lateSelect.value = lateCustom.value;
    }
});
</script>
@endpush
@endsection