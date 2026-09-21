@extends('layouts.app')
@section('title', 'My Attendance')
@section('content')
<div class="space-y-6">
    @include('student.partials.header', ['title' => 'My Attendance', 'subtitle' => $month->format('F Y')])

    <form method="GET" class="flex items-end gap-3">
        <div>
            <label class="text-xs text-gray-500">Month</label>
            <input type="month" name="month" value="{{ $month->format('Y-m') }}" class="block mt-1 rounded-lg border-gray-300 text-sm">
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Show</button>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3">
        @foreach([['Percentage', $summary['percentage'].'%', 'text-indigo-600'], ['Present', $summary['present'], 'text-green-600'], ['Absent', $summary['absent'], 'text-red-600'], ['Late', $summary['late'], 'text-yellow-600'], ['Half Day', $summary['half_day'], 'text-yellow-600'], ['Leave', $summary['leave'], 'text-blue-600'], ['On Duty', $summary['on_duty'], 'text-indigo-600'], ['Overall %', $overall['percentage'].'%', 'text-gray-700']] as [$l, $v, $c])
            <div class="bg-white rounded-xl border shadow-sm p-4 text-center">
                <div class="text-xs uppercase text-gray-500">{{ $l }}</div>
                <div class="text-2xl font-bold {{ $c }}">{{ $v }}</div>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Last 6 months</h3>
        <div class="space-y-2">
            @foreach($trend as $t)
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-20 text-gray-500">{{ $t['label'] }}</div>
                    <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full {{ $t['pct'] >= 75 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min(100, $t['pct']) }}%"></div>
                    </div>
                    <div class="w-24 text-right text-gray-700">{{ $t['pct'] }}% <span class="text-xs text-gray-400">({{ $t['days'] }}d)</span></div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-left">
                <tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Subject</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Details</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($byDate as $date => $rows)
                    @foreach($rows as $i => $r)
                        <tr>
                            @if($i === 0)
                                <td class="px-4 py-2 font-medium text-gray-800 align-top" rowspan="{{ $rows->count() }}">
                                    {{ \Carbon\Carbon::parse($date)->format('D, d M') }}
                                </td>
                            @endif
                            <td class="px-4 py-2">{{ $r->subject_name }}</td>
                            <td class="px-4 py-2">@include('student.partials.badge', ['status' => $r->status])</td>
                            <td class="px-4 py-2 text-gray-500 text-xs">
                                @if($r->status === 'late') {{ $r->late_minutes }} min late @if($r->late_reason) – {{ $r->late_reason }} @endif
                                @elseif($r->status === 'half_day') {{ ucfirst($r->half_day_type) }} @if($r->half_day_reason) – {{ $r->half_day_reason }} @endif
                                @elseif($r->status === 'leave') {{ $r->leave_reason }} {{ $r->is_approved ? '(approved)' : '(pending approval)' }}
                                @endif
                                {{ $r->remarks }}
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No attendance records for this month.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
