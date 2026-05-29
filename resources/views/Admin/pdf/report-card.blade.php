<!DOCTYPE html>
<html>
<head>
    <title>Report Card</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px; }
        h1, h2, h3 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background: #f2f2f2; }
        .footer { margin-top: 30px; font-size: 12px; text-align: center; }
    </style>
</head>
<body>
    <h1>Report Card</h1>
    <h2>{{ $student->first_name }} {{ $student->last_name }}</h2>
    <p>Class: {{ $student->class->name ?? 'N/A' }} | Section: {{ $student->section->name ?? 'N/A' }}</p>

    @foreach($data as $item)
        <h3>{{ $item['exam']->name }}</h3>
        <table>
            <thead><tr><th>Subject</th><th>Obtained</th><th>Max Marks</th></tr></thead>
            <tbody>
                @foreach($item['marks'] as $mark)
                <tr>
                    <td>{{ $mark->subject->name }}</td>
                    <td>{{ $mark->marks_obtained }}</td>
                    <td>{{ $mark->max_marks }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($item['result'])
            <p><strong>Total:</strong> {{ $item['result']->total_marks }} / {{ $item['result']->total_max_marks }}</p>
            <p><strong>Percentage:</strong> {{ $item['result']->percentage }}% &nbsp; | &nbsp; <strong>Grade:</strong> {{ $item['result']->grade }} &nbsp; | &nbsp; <strong>Remarks:</strong> {{ $item['result']->remarks }}</p>
        @endif
        <hr>
    @endforeach
    <div class="footer">Generated on {{ now()->format('d-m-Y H:i') }}</div>
</body>
</html>