@extends('layouts.app')
@section('title', 'Staff Attendance Records')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Staff Attendance</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.attendance.check') }}" class="bg-green-600 text-white px-4 py-2 rounded">Check In/Out</a>
            <a href="{{ route('admin.attendance.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Manual Entry</a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium">Employee</label>
                <select name="employee_id" class="w-full border rounded px-3 py-2">
                    <option value="">All</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->first_name }} {{ $emp->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded w-full">Filter</button>
            </div>
        </form>
    </div>

    <!-- Attendance List -->
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Work Hours</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                @php
                    // Calculate work hours if not stored
                    $workHours = $att->work_hours;
                    if (is_null($workHours) && $att->check_in && $att->check_out) {
                        $checkIn = \Carbon\Carbon::parse($att->check_in);
                        $checkOut = \Carbon\Carbon::parse($att->check_out);
                        $workMinutes = $checkIn->diffInMinutes($checkOut);
                        $workHours = round($workMinutes / 60, 2);
                    } else {
                        $workHours = $workHours ?? '—';
                    }
                @endphp
                <tr>
                    <td class="px-4 py-2">{{ $att->staff->first_name ?? '' }} {{ $att->staff->last_name ?? '' }}</td>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($att->date)->format('d-m-Y') }}</td>
                    <td class="px-4 py-2">{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i:s') : '—' }}</td>
                    <td class="px-4 py-2">{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i:s') : '—' }}</td>
                    <td class="px-4 py-2">{{ is_numeric($workHours) ? $workHours . ' hrs' : $workHours }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($att->status == 'present') bg-green-100 text-green-800
                            @elseif($att->status == 'late') bg-yellow-100 text-yellow-800
                            @elseif($att->status == 'half‑day') bg-orange-100 text-orange-800
                            @elseif($att->status == 'absent') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($att->status ?? '—') }}
                        </span>
                    </td>
                    <td class="px-4 py-2 max-w-xs truncate">{{ $att->remarks ?? '—' }}</td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="{{ route('admin.attendance.edit', $att) }}" class="text-blue-600">Edit</a>
                        <form action="{{ route('admin.attendance.destroy', $att) }}" method="POST" class="inline" onsubmit="return confirm('Delete record?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-8 text-gray-500">No attendance records found.{% endfor %}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $attendances->appends(request()->query())->links() }}</div>
</div>
@endsection