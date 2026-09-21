@extends('layouts.app')
@section('title', 'Check In / Check Out')
@section('content')
<div class="max-w-xl space-y-6">
    @include('teacher.partials.header', ['title' => 'Check In / Check Out', 'subtitle' => now()->format('l, d M Y')])

    <div class="bg-white rounded-xl border shadow-sm p-6 space-y-4">
        <div class="text-center">
            <div class="text-4xl font-bold text-gray-800" id="clock">{{ now()->format('h:i:s A') }}</div>
            <div class="text-xs text-gray-500 mt-1">
                Scheduled: {{ $t->cat_arrival ? substr($t->cat_arrival,0,5) : '—' }} – {{ $t->cat_departure ? substr($t->cat_departure,0,5) : '—' }}
            </div>
        </div>

        @if($todayRecord)
            <dl class="grid grid-cols-2 gap-3 text-sm border-t pt-4">
                <div><dt class="text-xs uppercase text-gray-500">Status</dt><dd>@include('teacher.partials.badge', ['status' => $todayRecord->status])</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Late</dt><dd>{{ $todayRecord->late_minutes ? $todayRecord->late_minutes.' min' : '—' }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Check-in</dt><dd>{{ $todayRecord->check_in ? substr($todayRecord->check_in,0,5) : '—' }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Check-out</dt><dd>{{ $todayRecord->check_out ? substr($todayRecord->check_out,0,5) : '—' }}</dd></div>
                @if($todayRecord->work_hours)<div><dt class="text-xs uppercase text-gray-500">Hours worked</dt><dd>{{ $todayRecord->work_hours }} h</dd></div>@endif
                @if($todayRecord->overtime_minutes)<div><dt class="text-xs uppercase text-gray-500">Overtime</dt><dd>{{ $todayRecord->overtime_minutes }} min</dd></div>@endif
            </dl>
        @endif

        @if(!$todayRecord || !$todayRecord->check_in)
            <form method="POST" action="{{ route('teacher.attendance.checkin') }}" class="space-y-3">
                @csrf
                <input type="text" name="reason" placeholder="Reason if you are late (optional)" class="w-full rounded-lg border-gray-300 text-sm">
                <button class="w-full py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700">✅ Check In</button>
            </form>
        @elseif(!$todayRecord->check_out)
            <form method="POST" action="{{ route('teacher.attendance.checkout') }}" class="space-y-3">
                @csrf
                <input type="text" name="reason" placeholder="Reason if leaving early (optional)" class="w-full rounded-lg border-gray-300 text-sm">
                <button class="w-full py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700">🚪 Check Out</button>
            </form>
        @else
            <div class="text-center text-sm text-green-700 bg-green-50 rounded-lg p-3">You have completed today's attendance. 🎉</div>
        @endif
    </div>
    <a href="{{ route('teacher.attendance.history') }}" class="inline-block text-sm text-indigo-600 hover:underline">View my attendance history →</a>
</div>
<script>
    setInterval(function () {
        document.getElementById('clock').textContent = new Date().toLocaleTimeString('en-US');
    }, 1000);
</script>
@endsection
