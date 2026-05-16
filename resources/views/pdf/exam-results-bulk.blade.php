<!DOCTYPE html>
<html>
<head>
    <title>Exam Results - {{ $exam->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #f5f5f5; }
        .page-break { page-break-before: always; }
        .header { text-align: center; margin-bottom: 20px; }
        .student-name { font-size: 16px; font-weight: bold; margin-top: 5px; }
    </style>
</head>
<body>
@foreach($data as $item)
    <div class="header">
        <h2>{{ $exam->name }}</h2>
        <div class="student-name">{{ $item['student']->name }}</div>
        <p>{{ $item['student']->class->full_name ?? '' }}</p>
    </div>
    <table>
        <thead>
            <tr><th>Subject</th><th>Max Marks</th><th>Obtained</th><th>%</th><th>Grade</th></tr>
        </thead>
        <tbody>
            @foreach($item['marks'] as $m)
            <tr>
                <td>{{ $m->subject->name }}</td>
                <td>{{ $m->max_marks }}</td>
                <td>{{ $m->marks_obtained }}</td>
                <td>{{ $m->percentage }}%</td>
                <td>{{ $m->grade }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total</th>
                <th>{{ $item['summary']['total_marks'] }}/{{ $item['summary']['total_max'] }}</th>
                <th>{{ $item['summary']['percentage'] }}%</th>
                <th>{{ $item['summary']['grade'] }}</th>
            </tr>
        </tfoot>
    </table>
    @if(!$loop->last)<div class="page-break"></div>@endif
@endforeach
</body>
</html>