@extends('layouts.app')
@section('title', 'Add Attendance')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h1 class="text-2xl font-bold">Add Attendance Record</h1>
        </div>
        <form method="POST" action="{{ route('admin.attendance.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block font-medium">Employee *</label>
                <select name="employee_id" required class="w-full border rounded px-3 py-2">
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
                @error('employee_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="block font-medium">Date *</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full border rounded px-3 py-2">
                @error('date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <!-- Status Dropdown -->
            <div>
                <label class="block font-medium">Status *</label>
                <select name="status" id="status" required class="w-full border rounded px-3 py-2">
                    <option value="present" {{ old('status')=='present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ old('status')=='absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ old('status')=='late' ? 'selected' : '' }}>Late</option>
                    <option value="half-day" {{ old('status')=='half-day' ? 'selected' : '' }}>Half Day</option>
                    <option value="leave" {{ old('status')=='leave' ? 'selected' : '' }}>Leave</option>
                </select>
                @error('status')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <!-- Arrival & Departure Times (disable when status = leave or absent) -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Arrival Time</label>
                    <input type="time" name="arrival_time" id="arrival_time" value="{{ old('arrival_time') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label>Departure Time</label>
                    <input type="time" name="departure_time" id="departure_time" value="{{ old('departure_time') }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div>
                <label>Remarks</label>
                <textarea name="remarks" rows="2" class="w-full border rounded px-3 py-2">{{ old('remarks') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.attendance.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save Record</button>
            </div>
        </form>
    </div>
</div>

<script>
    const statusSelect = document.getElementById('status');
    const arrivalInput = document.getElementById('arrival_time');
    const departureInput = document.getElementById('departure_time');

    function toggleTimeFields() {
        const status = statusSelect.value;
        const disable = (status === 'leave' || status === 'absent');
        arrivalInput.disabled = disable;
        departureInput.disabled = disable;
        if (disable) {
            arrivalInput.value = '';
            departureInput.value = '';
        }
    }

    // Initial state
    toggleTimeFields();

    // Listen for changes
    statusSelect.addEventListener('change', toggleTimeFields);
</script>
@endsection