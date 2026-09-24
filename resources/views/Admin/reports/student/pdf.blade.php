{{-- resources/views/admin/reports/student/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Student Report - {{ $student->admission_number }}</title>
<style>
    /* DomPDF only reliably supports basic CSS: tables, block/inline, no flexbox/grid. */
    body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; margin: 0; padding: 0; }
    .header { text-align: center; border-bottom: 2px solid #1D4ED8; padding-bottom: 10px; margin-bottom: 16px; }
    .header h1 { margin: 0; font-size: 20px; color: #1D4ED8; }
    .header p { margin: 2px 0 0; color: #6b7280; font-size: 11px; }

    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .info-table td { padding: 4px 6px; font-size: 11px; vertical-align: top; }
    .info-table .label { color: #6b7280; width: 90px; }
    .info-table .value { font-weight: bold; color: #111827; }

    .section-title {
        background: #1D4ED8; color: #fff; padding: 6px 10px; font-size: 12px;
        font-weight: bold; margin: 14px 0 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }

    table.data { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    table.data th {
        background: #EFF6FF; color: #1D4ED8; text-align: left; padding: 6px 8px;
        border: 1px solid #DBEAFE; font-size: 10px; text-transform: uppercase;
        -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    table.data td { padding: 6px 8px; border: 1px solid #E5E7EB; font-size: 11px; }
    table.data tr:nth-child(even) td { background: #F9FAFB; }

    .stat-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .stat-table td {
        width: 25%; text-align: center; padding: 10px 6px; border: 1px solid #E5E7EB;
        background: #F9FAFB;
    }
    .stat-table .stat-value { font-size: 18px; font-weight: bold; color: #1D4ED8; display: block; }
    .stat-table .stat-label { font-size: 9px; color: #6b7280; text-transform: uppercase; }

    .grade-badge {
        display: inline-block; padding: 3px 10px; border-radius: 10px; font-weight: bold;
        background: #DCFCE7; color: #15803D; -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }

    .footer { margin-top: 24px; padding-top: 8px; border-top: 1px solid #E5E7EB; font-size: 9px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>

    <div class="header">
        <h1>Student Report Card</h1>
        <p>{{ $academicSession->name ?? 'Current Session' }}</p>
    </div>

    {{-- Student info --}}
    <table class="info-table">
        <tr>
            <td class="label">Name</td>
            <td class="value">{{ trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}</td>
            <td class="label">Admission #</td>
            <td class="value">{{ $student->admission_number }}</td>
        </tr>
        <tr>
            <td class="label">Class</td>
            <td class="value">{{ $student->classSection->class->grade->name ?? '' }} - {{ $student->classSection->section_name ?? '' }}</td>
            <td class="label">Roll #</td>
            <td class="value">{{ $student->roll_number ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Father</td>
            <td class="value">{{ $student->father_name ?? '—' }}</td>
            <td class="label">Overall grade</td>
            <td class="value"><span class="grade-badge">{{ $overallGrade }}</span> ({{ number_format($overallPercentage, 1) }}%)</td>
        </tr>
    </table>

    {{-- Quick stats --}}
    <table class="stat-table">
        <tr>
            <td>
                <span class="stat-value">{{ number_format($stats['attendance_percentage'] ?? 0, 1) }}%</span>
                <span class="stat-label">Attendance</span>
            </td>
            <td>
                <span class="stat-value">{{ $stats['total_exams'] ?? 0 }}</span>
                <span class="stat-label">Exams Taken</span>
            </td>
            <td>
                <span class="stat-value">{{ number_format($stats['avg_percentage'] ?? 0, 1) }}%</span>
                <span class="stat-label">Average Score</span>
            </td>
            <td>
                <span class="stat-value">Rs. {{ number_format($stats['pending_fee'] ?? 0, 0) }}</span>
                <span class="stat-label">Fee Pending</span>
            </td>
        </tr>
    </table>

    {{-- Attendance summary --}}
    <div class="section-title">Attendance Summary</div>
    <table class="data">
        <thead>
            <tr>
                <th>Total Days</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Leave</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $attendanceSummary['total'] ?? 0 }}</td>
                <td>{{ $attendanceSummary['present'] ?? 0 }}</td>
                <td>{{ $attendanceSummary['absent'] ?? 0 }}</td>
                <td>{{ $attendanceSummary['leave'] ?? 0 }}</td>
                <td>{{ number_format($attendanceSummary['percentage'] ?? 0, 1) }}%</td>
            </tr>
        </tbody>
    </table>

    {{-- Subject-wise performance --}}
    <div class="section-title">Subject-wise Performance</div>
    @if(($subjectPerformance ?? collect())->isNotEmpty())
        <table class="data">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Avg Marks</th>
                    <th>Max Marks</th>
                    <th>Percentage</th>
                    <th>Grade</th>
                    <th>Pass Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subjectPerformance as $row)
                    <tr>
                        <td>{{ $row['subject']->name ?? 'N/A' }}</td>
                        <td>{{ $row['avg_marks'] }}</td>
                        <td>{{ $row['max_marks'] }}</td>
                        <td>{{ $row['percentage'] }}%</td>
                        <td>{{ $row['grade'] }}</td>
                        <td>{{ $row['pass_percentage'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color:#9ca3af; font-size:11px;">No exam records for this period.</p>
    @endif

    {{-- Fee summary --}}
    <div class="section-title">Fee Summary</div>
    <table class="data">
        <thead>
            <tr>
                <th>Total Fee</th>
                <th>Paid</th>
                <th>Pending</th>
                <th>Overdue Installments</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Rs. {{ number_format($feeSummary['total'] ?? 0, 2) }}</td>
                <td>Rs. {{ number_format($feeSummary['paid'] ?? 0, 2) }}</td>
                <td>Rs. {{ number_format($feeSummary['pending'] ?? 0, 2) }}</td>
                <td>{{ $feeSummary['overdue'] ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M Y, h:i A') }} &middot; This is a system-generated document.
    </div>

</body>
</html>