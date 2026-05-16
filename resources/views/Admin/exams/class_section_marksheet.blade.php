<!DOCTYPE html>
<html>
<head>
    <title>{{ $exam->name }} – Subject-wise Marks</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px; }
        .class-block { margin-bottom: 30px; page-break-inside: avoid; }
        .class-title { background: #4f46e5; color: white; padding: 8px; margin-top: 20px; font-size: 18px; }
        .section-title { background: #e0e7ff; padding: 6px; margin: 10px 0 5px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; vertical-align: middle; }
        th { background: #f3f4f6; font-weight: bold; }
        .student-name { text-align: left; font-weight: 500; }
    </style>
</head>
<body>
<h1>{{ $exam->name }} – Marks by Class & Section</h1>

@foreach($grouped as $className => $sections)
    <div class="class-block">
        <div class="class-title">Class: {{ $className }}</div>
        @foreach($sections as $sectionName => $students)
            <div class="section-title">Section: {{ $sectionName }}</div>
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
                            $grade = $percentage >= 90 ? 'A+' : ($percentage >= 80 ? 'A' : ($percentage >= 70 ? 'B+' : ($percentage >= 60 ? 'B' : ($percentage >= 50 ? 'C' : ($percentage >= 40 ? 'D' : 'F')))));
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="student-name">{{ $student->name }}</td>
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
        @endforeach
    </div>
@endforeach
</body>
</html>