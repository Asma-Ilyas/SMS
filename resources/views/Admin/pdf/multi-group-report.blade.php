<!DOCTYPE html>
<html>
<head>
    <title>Multi Group Academic Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: center; }
        th { background: #f0f0f0; }
        .group-title { background: #e0e7ff; padding: 8px; margin-top: 20px; font-weight: bold; }
        .overall { margin-top: 20px; padding: 10px; background: #f3f4f6; }
    </style>
</head>
<body>
<h1>Academic Report for {{ $student->name }}</h1>
<p>Class: {{ $student->class->full_name ?? '' }}</p>

@foreach($reportData as $groupData)
    <div class="group-title">{{ $groupData['group']->name }}</div>
    @foreach($groupData['exams'] as $examData)
        <h3>{{ $examData['exam']->name }}</h3>
        <table>
            <thead><tr><th>Subject</th><th>Max</th><th>Obtained</th><th>%</th><th>Grade</th></tr></thead>
            <tbody>
                @foreach($examData['marks'] as $m)
                <tr>
                    <td>{{ $m->subject->name }}</td>
                    <td>{{ $m->max_marks }}</td>
                    <td>{{ $m->marks_obtained }}</td>
                    <td>{{ $m->percentage }}%</td>
                    <td>{{ $m->grade }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot><tr><th colspan="2">Total</th><th>{{ $examData['summary']['total_marks'] }}/{{ $examData['summary']['total_max'] }}</th><th>{{ $examData['summary']['percentage'] }}%</th><th>{{ $examData['summary']['grade'] }}</th></tr></tfoot>
        </table>
    @endforeach
    <p><strong>Group Total:</strong> {{ $groupData['total_marks'] }}/{{ $groupData['total_max'] }} | Percentage: {{ $groupData['percentage'] }}% | Grade: {{ $groupData['grade'] }}</p>
    <hr>
@endforeach

<div class="overall">
    <strong>Final Summary</strong><br>
    <em>This report combines all selected exam groups.</em>
</div>
</body>
</html>