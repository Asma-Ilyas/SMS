@extends('layouts.app')
@section('title', 'My Attendance History')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'My Attendance History', 'subtitle' => $month->format('F Y')])

    <form method="GET" class="flex items-end gap-3">
        <div><label class="text-xs text-gray-500">Month</label>
            <input type="month" name="month" value="{{ $month->format('Y-m') }}" class="block mt-1 rounded-lg border-gray-300 text-sm"></div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Show</button>
        <a href="{{ route('teacher.attendance.check') }}" class="px-4 py-2 bg-white border rounded-lg text-sm">Check In/Out</a>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3">
        @foreach([['Attendance', $summary['pct'].'%', 'text-indigo-600'], ['Present', $summary['present'], 'text-green-600'], ['Late', $summary['late'], 'text-yellow-600'], ['Absent', $summary['absent'], 'text-red-600'], ['Half day', $summary['half_day'], 'text-yellow-600'], ['Leave', $summary['leave'], 'text-blue-600'], ['Hours', $summary['hours'], 'text-gray-700'], ['Late min', $summary['late_min'], 'text-gray-700']] as [$l, $v, $c])
            <div class="bg-white rounded-xl border shadow-sm p-4 text-center"><div class="text-xs uppercase text-gray-500">{{ $l }}</div><div class="text-2xl font-bold {{ $c }}">{{ $v }}</div></div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">In</th><th class="px-4 py-3">Out</th><th class="px-4 py-3">Hours</th><th class="px-4 py-3">Late</th><th class="px-4 py-3">Overtime</th><th class="px-4 py-3">Remarks</th></tr></thead>
            <tbody class="divide-y">
                @forelse($records as $r)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ \Carbon\Carbon::parse($r->date)->format('D, d M') }}</td>
                        <td class="px-4 py-2">@include('teacher.partials.badge', ['status' => $r->status])</td>
                        <td class="px-4 py-2">{{ $r->check_in ? substr($r->check_in,0,5) : '—' }}</td>
                        <td class="px-4 py-2">{{ $r->check_out ? substr($r->check_out,0,5) : '—' }}</td>
                        <td class="px-4 py-2">{{ $r->work_hours ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $r->late_minutes ? $r->late_minutes.' min' : '—' }}</td>
                        <td class="px-4 py-2">{{ $r->overtime_minutes ? $r->overtime_minutes.' min' : '—' }}</td>
                        <td class="px-4 py-2 text-gray-500 text-xs">{{ $r->remarks ?: ($r->early_departure_reason ?: $r->late_reason) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No attendance records for this month.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
