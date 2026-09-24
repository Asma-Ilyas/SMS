<!DOCTYPE html>
<html>
<head>
    <title>Timetable - {{ $timing->session_name }}</title>
    <style>
        @page { size: A4 landscape; margin: 1cm; }
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #999; padding: 6px; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; }
        .header { text-align: center; margin-bottom: 20px; }
        .slot-summary { margin-bottom: 20px; }
        .slot-summary span { display: inline-block; background: #eee; padding: 4px 8px; margin: 2px; border-radius: 4px; }
        .print-button { text-align: center; margin-bottom: 20px; }
        .text-gray-400 { color: #9ca3af; }
        .text-gray-500 { color: #6b7280; }
        .text-center { text-align: center; }
        .text-xs { font-size: 10px; }
        .mt-8 { margin-top: 2rem; }
        @media print {
            .print-button { display: none; }
        }
    </style>
</head>
<body>
<div class="print-button">
    <button onclick="window.print()">Print / Save PDF</button>
</div>

<div class="header">
    <h2>{{ $timing->session_name }} – Timetable</h2>
    <p>School Timing: {{ $timing->school_start }} to {{ $timing->school_end }} | Period Duration: {{ $timing->period_duration }} min</p>
</div>

<div class="slot-summary">
    <strong>Slots:</strong><br>
    @foreach($slots as $slot)
    <span>{{ $slot->label }} ({{ $slot->start_time }}–{{ $slot->end_time }})</span>
    @endforeach
</div>

@if($timing->breaks)
<div class="slot-summary">
    <strong>Breaks:</strong><br>
    @foreach($timing->breaks as $break)
    <span>{{ $break['label'] }}: {{ $break['start'] }}–{{ $break['end'] }}</span>
    @endforeach
</div>
@endif

@if($timing->has_activity)
<div class="slot-summary">
    <strong>Activity:</strong> {{ $timing->activity_label }} ({{ $timing->activity_start }}–{{ $timing->activity_end }})
</div>
@endif

@php
    // Group entries by "day_slot" so each cell can be looked up directly
    // instead of looping/filtering the whole collection per cell.
    $grouped = $entries->groupBy(fn($e) => $e->day_of_week . '_' . $e->time_slot_id);
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
@endphp

<table>
    <thead>
        <tr><th>Time / Day</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th></tr>
    </thead>
    <tbody>
        @forelse($slots as $slot)
        <tr>
            <td><strong>{{ $slot->label }}</strong><br>{{ $slot->start_time }} – {{ $slot->end_time }}</td>
            @foreach($days as $day)
                @php
                    $entry = $grouped->get($day . '_' . $slot->id)?->first();
                @endphp
                <td>
                    @if($entry)
                        <strong>{{ $entry->subject->name ?? '—' }}</strong><br>
                        {{ $entry->teacher->full_name ?? '' }}<br>
                        {{ $entry->room->name ?? '' }}
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
            @endforeach
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-gray-500">No time slots configured for this session.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<p class="text-center text-gray-500 text-xs mt-8">Generated on {{ now()->format('d M Y H:i') }}</p>
</body>
</html>