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

<table>
    <thead>
        <tr><th>Time / Day</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th></tr>
    </thead>
    <tbody>
        @foreach($slots as $slot)
        <tr>
            <td><strong>{{ $slot->label }}</strong><br>{{ $slot->start_time }} – {{ $slot->end_time }}</td>
            @for($i=1;$i<=5;$i++)<td class="text-gray-400">—</td>@endfor
        </tr>
        @endforeach
    </tbody>
</table>
<p class="text-center text-gray-500 text-xs mt-8">Generated on {{ now()->format('d M Y H:i') }}</p>
</body>
</html>