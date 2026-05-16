@extends('layouts.app')
@section('title', 'Edit Attendance')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h1 class="text-2xl font-bold">Edit Attendance Record</h1>
        </div>
        <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block font-medium">Employee *</label>
                <select name="employee_id" required class="w-full border rounded px-3 py-2">
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('employee_id', $attendance->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
                @error('employee_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="block font-medium">Date *</label>
                <input type="date" name="date" value="{{ old('date', $attendance->date->format('Y-m-d')) }}" required class="w-full border rounded px-3 py-2">
                @error('date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label>Check In</label><input type="time" name="check_in" value="{{ old('check_in', $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '') }}" class="w-full border rounded px-3 py-2"></div>
                <div><label>Check Out</label><input type="time" name="check_out" value="{{ old('check_out', $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '') }}" class="w-full border rounded px-3 py-2"></div>
            </div>
            <div>
                <label class="block font-medium">Status *</label>
                <select name="status" required class="w-full border rounded px-3 py-2">
                    <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ old('status', $attendance->status) == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="half-day" {{ old('status', $attendance->status) == 'half-day' ? 'selected' : '' }}>Half Day</option>
                </select>
            </div>
            <div><label>Remarks</label><textarea name="remarks" rows="2" class="w-full border rounded px-3 py-2">{{ old('remarks', $attendance->remarks) }}</textarea></div>
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.attendance.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update Record</button>
            </div>
        </form>
    </div>
</div>
@endsection