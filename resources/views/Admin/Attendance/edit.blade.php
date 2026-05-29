@extends('layouts.app')
@section('title', 'Edit Attendance')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Edit Attendance Record</h1>
        <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block font-medium">Employee</label>
                    <select name="employee_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id', $attendance->staff_id) == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Date</label>
                    <input type="date" name="date" value="{{ old('date', $attendance->date) }}" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Arrival Time</label>
                    <input type="time" name="arrival_time" value="{{ old('arrival_time', $attendance->check_in) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Departure Time</label>
                    <input type="time" name="departure_time" value="{{ old('departure_time', $attendance->check_out) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Remarks</label>
                    <textarea name="remarks" rows="2" class="w-full border rounded px-3 py-2">{{ old('remarks', $attendance->remarks) }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('admin.attendance.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection