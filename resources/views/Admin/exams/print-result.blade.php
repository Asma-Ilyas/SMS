<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Card - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
            .print-container { box-shadow: none !important; border: none !important; }
        }
        body {
            font-family: 'Arial', sans-serif;
            background: #f3f4f6;
            padding: 20px;
        }
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            padding: 40px;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 28px;
            color: #1f2937;
            margin: 0;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
        }
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            background: #f9fafb;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .student-info label {
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .student-info span {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #4f46e5;
            color: white;
            padding: 10px 15px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 10px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:hover {
            background: #f9fafb;
        }
        .total-row {
            background: #f3f4f6;
            font-weight: bold;
        }
        .pass { color: #16a34a; }
        .fail { color: #dc2626; }
        .grade-box {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            border: 2px solid #4f46e5;
            border-radius: 8px;
        }
        .grade-box h2 {
            font-size: 48px;
            color: #4f46e5;
            margin: 0;
        }
        .grade-box p {
            color: #6b7280;
            margin: 5px 0 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pass { background: #dcfce7; color: #16a34a; }
        .status-fail { background: #fee2e2; color: #dc2626; }
        .rank-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #fef3c7;
            color: #d97706;
        }
        @media print {
            .no-print { display: none !important; }
            .print-container { padding: 20px; box-shadow: none; }
            body { background: white; padding: 0; }
        }
        .print-btn {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .print-btn:hover { background: #4338ca; }
    </style>
</head>
<body>
    <div class="print-container">
        <button onclick="window.print()" class="print-btn no-print">🖨️ Print Result Card</button>

        {{-- Header --}}
        <div class="header">
            <div class="school-name">🏫 SchoolAdmin</div>
            <h1>📋 Examination Result Card</h1>
            <p>{{ $exam->name }} - {{ $exam->classSection->full_name }}</p>
        </div>

        {{-- Student Info --}}
        <div class="student-info">
            <div>
                <label>Student Name</label>
                <span>{{ $student->first_name }} {{ $student->last_name }}</span>
            </div>
            <div>
                <label>Roll Number</label>
                <span>{{ $student->roll_number ?? 'N/A' }}</span>
            </div>
            <div>
                <label>Class / Section</label>
                <span>{{ $exam->classSection->full_name ?? 'N/A' }}</span>
            </div>
            <div>
                <label>Date</label>
                <span>{{ now()->format('d M Y') }}</span>
            </div>
        </div>

        {{-- Marks Table --}}
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th style="text-align:center">Max Marks</th>
                    <th style="text-align:center">Obtained</th>
                    <th style="text-align:center">Percentage</th>
                    <th style="text-align:center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marks as $index => $mark)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $mark->subject->name ?? 'N/A' }}</td>
                    <td style="text-align:center">{{ $mark->max_marks }}</td>
                    <td style="text-align:center"><strong>{{ $mark->marks_obtained }}</strong></td>
                    <td style="text-align:center">{{ $mark->percentage }}%</td>
                    <td style="text-align:center">
                        @if($mark->marks_obtained >= $mark->passing_marks)
                            <span class="status-badge status-pass">✅ Pass</span>
                        @else
                            <span class="status-badge status-fail">❌ Fail</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2"><strong>Total</strong></td>
                    <td style="text-align:center"><strong>{{ $result->total_max_marks ?? 0 }}</strong></td>
                    <td style="text-align:center"><strong>{{ $result->total_marks ?? 0 }}</strong></td>
                    <td style="text-align:center"><strong>{{ $result->percentage ?? 0 }}%</strong></td>
                    <td style="text-align:center">
                        @if(($result->percentage ?? 0) >= 40)
                            <span class="status-badge status-pass">✅ Pass</span>
                        @else
                            <span class="status-badge status-fail">❌ Fail</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Grade Box --}}
        <div class="grade-box">
            <h2>{{ $result->grade ?? 'N/A' }}</h2>
            <p>Overall Grade</p>
            <div style="margin-top:10px">
                <span class="rank-badge">🏆 Rank: #{{ $result->rank_in_class ?? 'N/A' }}</span>
            </div>
            <div style="margin-top:10px;font-size:14px;color:#6b7280;">
                Status: @if(($result->percentage ?? 0) >= 40) <span style="color:#16a34a;font-weight:bold">✅ Passed</span> @else <span style="color:#dc2626;font-weight:bold">❌ Failed</span> @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>This is a computer-generated result card. No signature required.</p>
            <p style="margin-top:5px">Generated on: {{ now()->format('d M Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>