<!DOCTYPE html>
<html>
<head>
    <title>{{ $exam->name }} – Marksheet</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; vertical-align: middle; }
        th { background: #f3f4f6; font-weight: bold; }
        .student-name { text-align: left; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
<div class="header">
    <h1>{{ $exam->name }}</h1>
</div>

@forelse($grouped as $className => $sections)
    @foreach($sections as $sectionName => $students)
        <h2>Class: {{ $className }} | Section: {{ $sectionName }}</h2>
        @if(count($students) > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    @foreach($subjects as $subject)
                        <th>{{ $subject->name }}</th>
                    @endforeach
                    <th>Total</th>
                    <th>%</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                    @php
                        $marksBySubject = [];
                        $totalObtained = 0;
                        $totalMax = 0;
                        foreach ($student->marks as $mark) {
                            if ($mark->exam_id == $exam->id) {
                                $marksBySubject[$mark->subject_id] = $mark->marks_obtained;
                                $totalObtained += $mark->marks_obtained;
                                $totalMax += $mark->max_marks;
                            }
                        }
                        $percentage = $totalMax ? round(($totalObtained / $totalMax) * 100, 1) : 0;
                        $grade = match(true) {
                            $percentage >= 90 => 'A+',
                            $percentage >= 80 => 'A',
                            $percentage >= 70 => 'B+',
                            $percentage >= 60 => 'B',
                            $percentage >= 50 => 'C',
                            $percentage >= 40 => 'D',
                            default => 'F'
                        };
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="student-name">{{ $student->first_name }} {{ $student->last_name }}</td>
                        @foreach($subjects as $subject)
                            <td>{{ $marksBySubject[$subject->id] ?? '-' }}</td>
                        @endforeach
                        <td>{{ $totalObtained }}/{{ $totalMax }}</td>
                        <td>{{ $percentage }}%</td>
                        <td>{{ $grade }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @endforeach
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@empty
    <p>No data found for this exam.</p>
@endforelse
</body>
</html>