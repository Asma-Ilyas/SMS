<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Report Card - {{ $student->first_name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .student-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 5px;
            font-size: 12px;
        }
        .student-info .label {
            font-weight: bold;
            color: #555;
            width: 120px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #4f46e5;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
            margin: 20px 0 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background: #f3f4f6;
            color: #374151;
            font-weight: bold;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            border: 1px solid #d1d5db;
        }
        table td {
            padding: 8px 10px;
            border: 1px solid #d1d5db;
            font-size: 11px;
        }
        table .text-center {
            text-align: center;
        }
        .badge-success {
            color: #16a34a;
            font-weight: bold;
        }
        .badge-danger {
            color: #dc2626;
            font-weight: bold;
        }
        .badge-warning {
            color: #f59e0b;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
            margin-top: 20px;
        }
        .grade-scale {
            margin-top: 15px;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
        }
        .grade-scale table {
            margin-bottom: 0;
        }
        .grade-scale th {
            background: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Report Card</h1>
        <p>{{ $academicSession->name ?? 'Academic Year ' . date('Y') }}</p>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <table>
            <tr>
                <td class="label">Student Name:</td>
                <td><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></td>
                <td class="label">Admission No:</td>
                <td><strong>{{ $student->admission_number }}</strong></td>
            </tr>
            <tr>
                <td class="label">Class:</td>
                <td>{{ $student->classSection->grade->name ?? 'N/A' }}</td>
                <td class="label">Section:</td>
                <td>{{ $student->classSection->section_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Date of Birth:</td>
                <td>{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : 'N/A' }}</td>
                <td class="label">Gender:</td>
                <td>{{ $student->gender ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Overall Performance -->
    <div class="section-title">Overall Performance Summary</div>
    <table>
        <tr>
            <th>Total Marks</th>
            <th>Total Max Marks</th>
            <th>Average Percentage</th>
            <th>Overall Grade</th>
            <th>Exams Attempted</th>
        </tr>
        <tr>
            <td class="text-center">{{ number_format($overallPerformance['total_marks'], 1) }}</td>
            <td class="text-center">{{ number_format($overallPerformance['total_max_marks'], 1) }}</td>
            <td class="text-center">
                <span class="{{ $overallPerformance['average_percentage'] >= 80 ? 'badge-success' : ($overallPerformance['average_percentage'] >= 60 ? 'badge-warning' : 'badge-danger') }}">
                    {{ number_format($overallPerformance['average_percentage'], 1) }}%
                </span>
            </td>
            <td class="text-center"><strong>{{ $overallPerformance['overall_grade'] }}</strong></td>
            <td class="text-center">{{ $overallPerformance['total_exams'] }}</td>
        </tr>
    </table>

    <!-- Subject-wise Results -->
    <div class="section-title">Subject-wise Performance</div>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th class="text-center">Exams</th>
                <th class="text-center">Avg Marks</th>
                <th class="text-center">Avg %</th>
                <th class="text-center">Passed</th>
                <th class="text-center">Failed</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjectResults as $subject)
                <tr>
                    <td>{{ $subject->subject_name }}</td>
                    <td class="text-center">{{ $subject->exam_count }}</td>
                    <td class="text-center">{{ number_format($subject->average_marks, 1) }}</td>
                    <td class="text-center">
                        <span class="{{ $subject->average_percentage >= 80 ? 'badge-success' : ($subject->average_percentage >= 60 ? 'badge-warning' : 'badge-danger') }}">
                            {{ number_format($subject->average_percentage, 1) }}%
                        </span>
                    </td>
                    <td class="text-center">{{ $subject->passed_count }}</td>
                    <td class="text-center">{{ $subject->failed_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No subject results available</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Attendance Report -->
    <div class="section-title">Attendance Report</div>
    <table>
        <thead>
            <tr>
                <th class="text-center">Total Days</th>
                <th class="text-center">Present</th>
                <th class="text-center">Absent</th>
                <th class="text-center">Late</th>
                <th class="text-center">Half Days</th>
                <th class="text-center">Attendance %</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $attendanceData['total_days'] }}</td>
                <td class="text-center text-success">{{ $attendanceData['present_days'] }}</td>
                <td class="text-center text-danger">{{ $attendanceData['absent_days'] }}</td>
                <td class="text-center text-warning">{{ $attendanceData['late_days'] }}</td>
                <td class="text-center text-info">{{ $attendanceData['half_days'] }}</td>
                <td class="text-center">
                    <span class="{{ $attendanceData['percentage'] >= 75 ? 'badge-success' : ($attendanceData['percentage'] >= 60 ? 'badge-warning' : 'badge-danger') }}">
                        {{ number_format($attendanceData['percentage'], 1) }}%
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Monthly Attendance -->
    @if(!empty($attendanceData['monthly']))
    <div class="section-title">Monthly Attendance Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th class="text-center">Total</th>
                <th class="text-center">Present</th>
                <th class="text-center">Absent</th>
                <th class="text-center">Late</th>
                <th class="text-center">Half</th>
                <th class="text-center">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceData['monthly'] as $month => $data)
                <tr>
                    <td>{{ $month }}</td>
                    <td class="text-center">{{ $data['total'] }}</td>
                    <td class="text-center text-success">{{ $data['present'] }}</td>
                    <td class="text-center text-danger">{{ $data['absent'] }}</td>
                    <td class="text-center text-warning">{{ $data['late'] }}</td>
                    <td class="text-center text-info">{{ $data['half'] }}</td>
                    <td class="text-center">
                        <span class="{{ $data['percentage'] >= 75 ? 'badge-success' : ($data['percentage'] >= 60 ? 'badge-warning' : 'badge-danger') }}">
                            {{ number_format($data['percentage'], 1) }}%
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Grade Scale -->
    <div class="grade-scale">
        <strong style="font-size: 12px;">Grade Scale:</strong>
        <table>
            <thead>
                <tr>
                    <th class="text-center">Grade</th>
                    <th class="text-center">Percentage Range</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gradeScale as $grade)
                    <tr>
                        <td class="text-center"><strong>{{ $grade['grade'] }}</strong></td>
                        <td class="text-center">{{ $grade['min'] }}% - {{ $grade['max'] }}%</td>
                        <td>{{ $grade['description'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
        <p>This is a system-generated report card. Please verify all details before official use.</p>
    </div>
</body>
</html>