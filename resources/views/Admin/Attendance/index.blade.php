@extends('layouts.app')
@section('title', 'Attendance Records')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Attendance Records</h1>
        <a href="{{ route('admin.attendance.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Attendance</a>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white rounded shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-sm">Employee</label>
                <select name="employee_id" class="w-full border rounded px-2 py-1">
                    <option value="">All</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label>From Date</label><input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full border rounded"></div>
            <div><label>To Date</label><input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full border rounded"></div>
            <div class="flex items-end space-x-2"><button type="submit" class="bg-gray-800 text-white px-4 py-1 rounded">Filter</button><a href="{{ route('admin.attendance.index') }}" class="border px-4 py-1 rounded">Reset</a></div>
        </form>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr><th>Employee</th><th>Date</th><th>Check In</th><th>Check Out</th><th>Total Hours</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                <tr>
                    <td>{{ $att->staff->name }}</td>
                    <td>{{ $att->date->format('d-m-Y') }}</td>
                    <td>{{ $att->check_in ?? '-' }}</td>
                    <td>{{ $att->check_out ?? '-' }}</td>
                    <td>{{ $att->total_hours ?? '-' }}</td>
                    <td><span class="px-2 py-1 text-xs rounded-full {{ $att->status=='present'?'bg-green-100 text-green-800':($att->status=='absent'?'bg-red-100 text-red-800':'bg-yellow-100') }}">{{ ucfirst($att->status) }}</span></td>
                    <td class="space-x-2"><a href="{{ route('admin.attendance.edit', $att) }}" class="text-blue-600">Edit</a><form action="{{ route('admin.attendance.destroy', $att) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="text-red-600">Delete</button></form></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-2">{{ $attendances->links() }}</div>
    </div>
</div>
@endsection