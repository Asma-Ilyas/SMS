<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceReason;
use App\Models\AttendanceTeacherSection;
use App\Models\ClassSection;
use App\Models\CommonSubjectClass;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentAttendanceSummary;
use App\Models\Subject;
use App\Models\Room;
use App\Support\PortalAlert;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class StudentAttendanceController extends Controller
{
    /**
     * Display attendance dashboard
     */
    public function index()
    {
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        
        $pendingLeaves = StudentAttendance::where('status', 'leave')
            ->where('is_approved', false)
            ->count();
        
        $pendingHalfDays = StudentAttendance::where('status', 'half_day')
            ->where('is_approved', false)
            ->count();
        
        $recentAttendance = StudentAttendance::with(['student', 'classSection', 'teacher'])
            ->latest()
            ->take(10)
            ->get();
        
        // Get today's statistics
        $today = now()->toDateString();
        $todayStats = [
            'total' => StudentAttendance::where('date', $today)->count(),
            'present' => StudentAttendance::where('date', $today)->where('status', 'present')->count(),
            'absent' => StudentAttendance::where('date', $today)->where('status', 'absent')->count(),
            'late' => StudentAttendance::where('date', $today)->where('status', 'late')->count(),
            'half_day' => StudentAttendance::where('date', $today)->where('status', 'half_day')->count(),
            'leave' => StudentAttendance::where('date', $today)->where('status', 'leave')->count(),
            'holiday' => StudentAttendance::where('date', $today)->where('status', 'holiday')->count(),
            'on_duty' => StudentAttendance::where('date', $today)->where('status', 'on_duty')->count(),
        ];
        
        return view('admin.studentattendance.index', compact(
            'classSections', 
            'pendingLeaves', 
            'pendingHalfDays',
            'recentAttendance',
            'todayStats'
        ));
    }

    /**
     * Show mark attendance form
     */
   /**
 * Show mark attendance form
 */
public function create(Request $request)
{
    $classSectionId = $request->class_section_id;
    $date = $request->date ?? Carbon::today()->toDateString();
    
    // If no class section is selected, show selection form
    if (!$classSectionId) {
        $classSections = ClassSection::with(['class.grade', 'class.stream'])->get();
        return view('admin.studentattendance.select-class', compact('classSections'));
    }
    
    $classSection = ClassSection::with(['class.grade', 'class.stream'])->findOrFail($classSectionId);
    
    $students = Student::where('class_section_id', $classSectionId)
        ->orderBy('first_name')
        ->get();
    
    $attendances = StudentAttendance::where('class_section_id', $classSectionId)
        ->where('date', $date)
        ->get()
        ->keyBy('student_id');
    
    $teacher = Staff::first();
    $teacherName = $teacher ? $teacher->full_name : 'Admin';
    
    $subjects = Subject::all();
    
    // Get reasons for dropdowns
    $halfDayReasons = AttendanceReason::halfDayReasons()->active()->ordered()->get();
    $lateReasons = AttendanceReason::lateReasons()->active()->ordered()->get();
    
    // Get status options
    $statusOptions = StudentAttendance::getStatusOptions();
    $halfDayTypes = StudentAttendance::getHalfDayTypeOptions();
    
    return view('admin.studentattendance.mark', compact(
        'classSection', 
        'date', 
        'students', 
        'attendances', 
        'teacherName', 
        'subjects',
        'halfDayReasons',
        'lateReasons',
        'statusOptions',
        'halfDayTypes'
    ));
}

    /**
     * Store or update attendance
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late,half_day,leave,holiday,on_duty',
            'attendance.*.remarks' => 'nullable|string|max:500',
            'attendance.*.leave_reason' => 'nullable|string|max:500',
            'attendance.*.half_day_type' => 'required_if:attendance.*.status,half_day|in:morning,afternoon,custom|nullable',
            'attendance.*.half_day_reason' => 'nullable|string|max:500',
            'attendance.*.half_day_in_time' => 'nullable|date_format:H:i',
            'attendance.*.half_day_out_time' => 'nullable|date_format:H:i',
            'attendance.*.late_arrival_time' => 'required_if:attendance.*.status,late|nullable|date_format:H:i',
            'attendance.*.late_reason' => 'nullable|string|max:500',
            'attendance.*.check_in' => 'nullable|date_format:H:i',
            'attendance.*.check_out' => 'nullable|date_format:H:i|after:attendance.*.check_in',
        ]);
        
        $classSectionId = $request->class_section_id;
        $date = $request->date;
        
        $teacher = Staff::first();
        $teacherId = $teacher ? $teacher->id : null;
        
        $subjectId = $request->subject_id;
        if (!$subjectId) {
            $firstSubject = Subject::first();
            $subjectId = $firstSubject ? $firstSubject->id : null;
        }
        
        $savedCount = 0;
        $errors = [];

        foreach ($request->attendance as $studentId => $data) {
            try {
                $status = $data['status'];
                $isLeave = ($status == 'leave');
                $isHalfDay = ($status == 'half_day');
                $isLate = ($status == 'late');
                
                $attendanceData = [
                    'student_id' => $studentId,
                    'class_section_id' => $classSectionId,
                    'subject_id' => $subjectId,
                    'date' => $date,
                    'status' => $status,
                    'remarks' => $data['remarks'] ?? null,
                    'leave_reason' => $data['leave_reason'] ?? null,
                    'is_approved' => ($isLeave || $isHalfDay) ? false : true,
                    'approved_at' => ($isLeave || $isHalfDay) ? null : now(),
                    'teacher_id' => $teacherId,
                    'marked_by' => $teacherId,
                ];

                // Handle Half Day
                if ($isHalfDay) {
                    $attendanceData['half_day_type'] = $data['half_day_type'] ?? 'morning';
                    $attendanceData['half_day_reason'] = $data['half_day_reason'] ?? null;
                    $attendanceData['half_day_in_time'] = $data['half_day_in_time'] ?? null;
                    $attendanceData['half_day_out_time'] = $data['half_day_out_time'] ?? null;
                    $attendanceData['late_arrival_time'] = null;
                    $attendanceData['late_minutes'] = 0;
                    $attendanceData['late_reason'] = null;
                }

                // Handle Late
                if ($isLate) {
                    $attendanceData['late_arrival_time'] = $data['late_arrival_time'] ?? now()->toTimeString();
                    $attendanceData['late_minutes'] = $this->calculateLateMinutes($attendanceData['late_arrival_time']);
                    $attendanceData['late_reason'] = $data['late_reason'] ?? null;
                    $attendanceData['half_day_type'] = null;
                    $attendanceData['half_day_reason'] = null;
                    $attendanceData['half_day_in_time'] = null;
                    $attendanceData['half_day_out_time'] = null;
                }

                // If status is present, absent, or leave, clear both
                if (in_array($status, ['present', 'absent', 'leave', 'holiday', 'on_duty'])) {
                    $attendanceData['half_day_type'] = null;
                    $attendanceData['half_day_reason'] = null;
                    $attendanceData['half_day_in_time'] = null;
                    $attendanceData['half_day_out_time'] = null;
                    $attendanceData['late_arrival_time'] = null;
                    $attendanceData['late_minutes'] = 0;
                    $attendanceData['late_reason'] = null;
                }

                // Add check-in/out if provided
                if (!empty($data['check_in'])) {
                    $attendanceData['check_in'] = $data['check_in'];
                }
                if (!empty($data['check_out'])) {
                    $attendanceData['check_out'] = $data['check_out'];
                }

                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'date' => $date,
                        'class_section_id' => $classSectionId,
                    ],
                    $attendanceData
                );

                $savedCount++;
            } catch (\Exception $e) {
                $errors[] = "Student ID: $studentId - " . $e->getMessage();
            }
        }

        // Generate monthly summary if any attendance was saved
        if ($savedCount > 0) {
            $this->generateMonthlySummary($classSectionId, $date);
        }

        if ($savedCount > 0) {
            return redirect()->route('admin.studentattendance.index')
                ->with('success', "Attendance saved for $savedCount students successfully!");
        } else {
            return redirect()->back()
                ->with('error', 'Failed to save attendance: ' . implode(', ', $errors));
        }
    }

    /**
     * Show single attendance record
     */
    public function show($id)
    {
        $attendance = StudentAttendance::with([
            'student', 
            'classSection', 
            'subject', 
            'teacher', 
            'approvedBy', 
            'markedBy'
        ])->findOrFail($id);
        
        $statusOptions = StudentAttendance::getStatusOptions();
        $halfDayTypes = StudentAttendance::getHalfDayTypeOptions();
        
        // Get reasons for dropdowns
        $halfDayReasons = AttendanceReason::halfDayReasons()->active()->ordered()->get();
        $lateReasons = AttendanceReason::lateReasons()->active()->ordered()->get();
        
        return view('admin.studentattendance.show', compact(
            'attendance', 
            'statusOptions', 
            'halfDayTypes', 
            'halfDayReasons', 
            'lateReasons'
        ));
    }

    /**
     * Edit attendance record
     */
    public function edit($id)
    {
        $attendance = StudentAttendance::with(['student', 'classSection'])->findOrFail($id);
        
        $statusOptions = StudentAttendance::getStatusOptions();
        $halfDayTypes = StudentAttendance::getHalfDayTypeOptions();
        $halfDayReasons = AttendanceReason::halfDayReasons()->active()->ordered()->get();
        $lateReasons = AttendanceReason::lateReasons()->active()->ordered()->get();
        
        return view('admin.studentattendance.edit', compact(
            'attendance',
            'statusOptions',
            'halfDayTypes',
            'halfDayReasons',
            'lateReasons'
        ));
    }

    /**
     * Update attendance record
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:present,absent,late,half_day,leave,holiday,on_duty',
            'half_day_type' => 'required_if:status,half_day|in:morning,afternoon,custom|nullable',
            'half_day_reason' => 'nullable|string|max:500',
            'half_day_in_time' => 'nullable|date_format:H:i',
            'half_day_out_time' => 'nullable|date_format:H:i|after:half_day_in_time',
            'late_arrival_time' => 'required_if:status,late|nullable|date_format:H:i',
            'late_reason' => 'nullable|string|max:500',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'remarks' => 'nullable|string|max:500',
            'leave_reason' => 'nullable|string|max:500',
            'is_approved' => 'nullable|boolean',
        ]);

        $attendance = StudentAttendance::findOrFail($id);
        
        $data = [
            'status' => $request->status,
            'remarks' => $request->remarks,
            'leave_reason' => $request->leave_reason,
        ];

        // Handle half day
        if ($request->status === 'half_day') {
            $data['half_day_type'] = $request->half_day_type;
            $data['half_day_reason'] = $request->half_day_reason;
            $data['half_day_in_time'] = $request->half_day_in_time;
            $data['half_day_out_time'] = $request->half_day_out_time;
            $data['late_arrival_time'] = null;
            $data['late_minutes'] = 0;
            $data['late_reason'] = null;
        }

        // Handle late
        if ($request->status === 'late') {
            $data['late_arrival_time'] = $request->late_arrival_time;
            $data['late_minutes'] = $this->calculateLateMinutes($request->late_arrival_time);
            $data['late_reason'] = $request->late_reason;
            $data['half_day_type'] = null;
            $data['half_day_reason'] = null;
            $data['half_day_in_time'] = null;
            $data['half_day_out_time'] = null;
        }

        // If status is present, absent, or leave, clear both
        if (in_array($request->status, ['present', 'absent', 'leave', 'holiday', 'on_duty'])) {
            $data['half_day_type'] = null;
            $data['half_day_reason'] = null;
            $data['half_day_in_time'] = null;
            $data['half_day_out_time'] = null;
            $data['late_arrival_time'] = null;
            $data['late_minutes'] = 0;
            $data['late_reason'] = null;
        }

        // Check-in/out
        if ($request->has('check_in')) {
            $data['check_in'] = $request->check_in;
        }
        if ($request->has('check_out')) {
            $data['check_out'] = $request->check_out;
        }

        // Approval
        if ($request->has('is_approved')) {
            $data['is_approved'] = $request->is_approved;
            if ($request->is_approved) {
                $data['approved_at'] = now();
                $data['approved_by'] = Staff::where('email', auth()->user()?->email)->value('id');
            }
        }

        $attendance->update($data);

        // Regenerate summary
        $this->generateMonthlySummary($attendance->class_section_id, $attendance->date);

        return redirect()->route('admin.studentattendance.index')
            ->with('success', 'Attendance updated successfully!');
    }

    /**
     * Delete attendance record
     */
    public function destroy($id)
    {
        $attendance = StudentAttendance::findOrFail($id);
        $classSectionId = $attendance->class_section_id;
        $date = $attendance->date;
        
        $attendance->delete();

        // Regenerate summary
        $this->generateMonthlySummary($classSectionId, $date);

        return redirect()->route('admin.studentattendance.index')
            ->with('success', 'Attendance record deleted successfully!');
    }

    /**
     * Show attendance report
     */
    public function report(Request $request)
    {
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        
        $query = StudentAttendance::with(['student', 'classSection', 'teacher']);
        
        if ($request->filled('class_section_id')) {
            $query->where('class_section_id', $request->class_section_id);
        }
        
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('is_approved')) {
            $query->where('is_approved', $request->is_approved == '1');
        }
        
        $attendances = $query->orderBy('date', 'desc')->paginate(20);
        
        $students = collect();
        if ($request->filled('class_section_id')) {
            $students = Student::where('class_section_id', $request->class_section_id)
                ->orderBy('first_name')
                ->get();
        }
        
        $summary = null;
        if ($request->filled('class_section_id') && $request->filled('from_date') && $request->filled('to_date')) {
            $summary = $this->calculateSummary(
                $request->class_section_id,
                $request->from_date,
                $request->to_date
            );
        }
        
        $statusOptions = StudentAttendance::getStatusOptions();
        
        return view('admin.studentattendance.report', compact(
            'classSections', 
            'students', 
            'attendances', 
            'summary',
            'statusOptions'
        ));
    }

    /**
     * Show individual student attendance report with ALL months
     * FIXED: leave is a reserved keyword, wrapped in backticks
     */
    public function studentReport($studentId, Request $request)
    {
        $student = Student::with(['classSection.class.grade', 'classSection.class.stream'])
            ->findOrFail($studentId);
        
        $month = $request->month ?? now()->format('Y-m');
        $year = (int) substr($month, 0, 4);
        $monthNum = (int) substr($month, 5, 2);
        
        // Get all months stats - FIXED: leave is a reserved keyword
        $monthlyStats = StudentAttendance::where('student_id', $studentId)
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late'),
                DB::raw('SUM(CASE WHEN status = "half_day" THEN 1 ELSE 0 END) as half_day'),
                DB::raw('SUM(CASE WHEN status = "`leave`" THEN 1 ELSE 0 END) as `leave`'), // FIXED: backticks
                DB::raw('SUM(CASE WHEN status = "holiday" THEN 1 ELSE 0 END) as holiday'),
                DB::raw('SUM(CASE WHEN status = "on_duty" THEN 1 ELSE 0 END) as on_duty')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
        
        // Get detailed attendances for selected month
        $attendances = StudentAttendance::where('student_id', $studentId)
            ->with(['classSection', 'teacher'])
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->orderBy('date', 'desc')
            ->paginate(30);
        
        // Get summary for selected month
        $summary = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'half_day' => 0,
            'leave' => 0,
            'holiday' => 0,
            'on_duty' => 0,
        ];
        
        // Calculate summary from monthlyStats for selected month
        $selectedMonthStat = $monthlyStats->firstWhere('month', $month);
        if ($selectedMonthStat) {
            $summary = (array) $selectedMonthStat;
        }
        
        // Get half-day and late reasons for selected month
        $halfDayReasons = StudentAttendance::where('student_id', $studentId)
            ->where('status', 'half_day')
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->pluck('half_day_reason', 'date')
            ->filter()
            ->toArray();
            
        $lateReasons = StudentAttendance::where('student_id', $studentId)
            ->where('status', 'late')
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->pluck('late_reason', 'date')
            ->filter()
            ->toArray();
        
        // Get available months for dropdown
        $availableMonths = StudentAttendance::where('student_id', $studentId)
            ->select(DB::raw('DISTINCT DATE_FORMAT(date, "%Y-%m") as month'))
            ->orderBy('month', 'desc')
            ->pluck('month')
            ->toArray();
        
        // If no months found, add current month
        if (empty($availableMonths)) {
            $availableMonths = [now()->format('Y-m')];
        }
        
        return view('admin.studentattendance.student_report', compact(
            'student', 
            'attendances', 
            'monthlyStats',
            'summary',
            'month',
            'halfDayReasons',
            'lateReasons',
            'availableMonths'
        ));
    }

    /**
     * Export individual student attendance to CSV
     * FIXED: leave is a reserved keyword, wrapped in backticks
     */
    public function exportStudentReport($studentId, Request $request)
    {
        $student = Student::with(['classSection'])->findOrFail($studentId);
        
        $month = $request->month ?? now()->format('Y-m');
        $year = (int) substr($month, 0, 4);
        $monthNum = (int) substr($month, 5, 2);
        
        $attendances = StudentAttendance::where('student_id', $studentId)
            ->with(['classSection', 'teacher'])
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->orderBy('date', 'desc')
            ->get();
        
        // Get summary for the month - FIXED: leave is a reserved keyword
        $summary = StudentAttendance::where('student_id', $studentId)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late'),
                DB::raw('SUM(CASE WHEN status = "half_day" THEN 1 ELSE 0 END) as half_day'),
                DB::raw('SUM(CASE WHEN status = "`leave`" THEN 1 ELSE 0 END) as `leave`'), // FIXED: backticks
                DB::raw('SUM(CASE WHEN status = "holiday" THEN 1 ELSE 0 END) as holiday'),
                DB::raw('SUM(CASE WHEN status = "on_duty" THEN 1 ELSE 0 END) as on_duty')
            )
            ->first();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=student_attendance_{$student->first_name}_{$student->last_name}_{$month}.csv",
        ];

        $callback = function() use ($student, $attendances, $summary, $month) {
            $handle = fopen('php://output', 'w');
            
            fputs($handle, "\xEF\xBB\xBF");
            
            fputcsv($handle, ['Student Attendance Report']);
            fputcsv($handle, ['Student Name:', $student->first_name . ' ' . $student->last_name]);
            fputcsv($handle, ['Class:', $student->classSection->full_name ?? 'N/A']);
            fputcsv($handle, ['Month:', Carbon::parse($month)->format('F Y')]);
            fputcsv($handle, ['Report Date:', now()->format('d M Y H:i:s')]);
            fputcsv($handle, []);
            
            fputcsv($handle, ['SUMMARY STATISTICS']);
            fputcsv($handle, [
                'Total Days',
                'Present',
                'Absent',
                'Late',
                'Half Day',
                'Leave',
                'Holiday',
                'On Duty',
                'Attendance %'
            ]);
            
            $total = $summary->total ?? 0;
            $present = $summary->present ?? 0;
            $attended = $present + ($summary->late ?? 0) + ($summary->half_day ?? 0) + ($summary->on_duty ?? 0);
            $percentage = $total > 0 ? round(($attended / $total) * 100, 2) : 0;
            
            fputcsv($handle, [
                $total,
                $present,
                $summary->absent ?? 0,
                $summary->late ?? 0,
                $summary->half_day ?? 0,
                $summary->leave ?? 0,
                $summary->holiday ?? 0,
                $summary->on_duty ?? 0,
                $percentage . '%'
            ]);
            
            fputcsv($handle, []);
            fputcsv($handle, ['DETAILED ATTENDANCE RECORDS']);
            fputcsv($handle, [
                'Date',
                'Day',
                'Class',
                'Status',
                'Half Day Type',
                'Reason',
                'Check In',
                'Check Out',
                'Approved',
                'Teacher'
            ]);

            foreach ($attendances as $att) {
                $statusLabel = match($att->status) {
                    'present' => 'Present',
                    'absent' => 'Absent',
                    'late' => 'Late',
                    'half_day' => 'Half Day',
                    'leave' => 'Leave',
                    'holiday' => 'Holiday',
                    'on_duty' => 'On Duty',
                    default => $att->status
                };

                $reason = match($att->status) {
                    'half_day' => $att->half_day_reason ?? $att->remarks ?? '',
                    'late' => $att->late_reason ?? $att->remarks ?? '',
                    'leave' => $att->leave_reason ?? $att->remarks ?? '',
                    default => $att->remarks ?? ''
                };

                fputcsv($handle, [
                    $att->date->format('d M Y'),
                    $att->date->format('l'),
                    $att->classSection->full_name ?? 'N/A',
                    $statusLabel,
                    $att->half_day_type ?? '',
                    $reason,
                    $att->check_in ?? '',
                    $att->check_out ?? '',
                    $att->is_approved ? 'Yes' : 'No',
                    $att->teacher->full_name ?? 'Admin'
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export student attendance to PDF (using DomPDF)
     * FIXED: leave is a reserved keyword, wrapped in backticks
     */
    public function exportStudentPDF($studentId, Request $request)
    {
        $student = Student::with(['classSection'])->findOrFail($studentId);
        
        $month = $request->month ?? now()->format('Y-m');
        $year = (int) substr($month, 0, 4);
        $monthNum = (int) substr($month, 5, 2);
        
        $attendances = StudentAttendance::where('student_id', $studentId)
            ->with(['classSection', 'teacher'])
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->orderBy('date', 'desc')
            ->get();
        
        // FIXED: leave is a reserved keyword
        $summary = StudentAttendance::where('student_id', $studentId)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late'),
                DB::raw('SUM(CASE WHEN status = "half_day" THEN 1 ELSE 0 END) as half_day'),
                DB::raw('SUM(CASE WHEN status = "`leave`" THEN 1 ELSE 0 END) as `leave`'), // FIXED: backticks
                DB::raw('SUM(CASE WHEN status = "holiday" THEN 1 ELSE 0 END) as holiday'),
                DB::raw('SUM(CASE WHEN status = "on_duty" THEN 1 ELSE 0 END) as on_duty')
            )
            ->first();
        
        // FIXED: leave is a reserved keyword
        $monthlyStats = StudentAttendance::where('student_id', $studentId)
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late'),
                DB::raw('SUM(CASE WHEN status = "half_day" THEN 1 ELSE 0 END) as half_day'),
                DB::raw('SUM(CASE WHEN status = "`leave`" THEN 1 ELSE 0 END) as `leave`'), // FIXED: backticks
                DB::raw('SUM(CASE WHEN status = "holiday" THEN 1 ELSE 0 END) as holiday'),
                DB::raw('SUM(CASE WHEN status = "on_duty" THEN 1 ELSE 0 END) as on_duty')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.studentattendance.student_report_pdf', compact(
            'student', 'attendances', 'summary', 'monthlyStats', 'month'
        ));
        
        return $pdf->download("student_attendance_{$student->first_name}_{$student->last_name}_{$month}.pdf");
    }

    /**
     * Approve leave request
     */
    public function approveLeave($attendanceId)
    {
        $attendance = StudentAttendance::findOrFail($attendanceId);

        if (!in_array($attendance->status, ['leave', 'half_day'])) {
            return back()->with('error', 'This record is not a leave or half-day request.');
        }

        $attendance->update([
            'is_approved' => true,
            'approved_at' => now(),
            // approved_by is a staff id, so look the staff member up from the logged-in user's email
            'approved_by' => Staff::where('email', auth()->user()?->email)->value('id'),
        ]);

        $type = str_replace('_', ' ', $attendance->status);

        PortalAlert::toStudent(
            $attendance->student_id,
            ucfirst($type) . ' approved',
            'Your ' . $type . ' request for ' . Carbon::parse($attendance->date)->format('d M Y') . ' was approved.',
            PortalAlert::link('student.attendance.index', '/student/attendance'),
            'attendance',
            'success'
        );

        return back()->with('success', ucfirst($type) . ' request approved successfully.');
    }

    /**
     * Reject leave request
     */
    public function rejectLeave($attendanceId)
    {
        $attendance = StudentAttendance::findOrFail($attendanceId);

        if (!in_array($attendance->status, ['leave', 'half_day'])) {
            return back()->with('error', 'This record is not a leave or half-day request.');
        }

        // Remember these before the status is changed to "absent"
        $type = str_replace('_', ' ', $attendance->status);
        $date = Carbon::parse($attendance->date)->format('d M Y');

        $attendance->update([
            'status'            => 'absent',
            'is_approved'       => false,
            'remarks'           => 'Rejected by ' . (auth()->user()->name ?? 'Admin'),
            'half_day_type'     => null,
            'half_day_reason'   => null,
            'half_day_in_time'  => null,
            'half_day_out_time' => null,
            'late_arrival_time' => null,
            'late_minutes'      => 0,
            'late_reason'       => null,
        ]);

        PortalAlert::toStudent(
            $attendance->student_id,
            ucfirst($type) . ' rejected',
            'Your ' . $type . ' request for ' . $date . ' was rejected and marked as absent.',
            PortalAlert::link('student.attendance.index', '/student/attendance'),
            'attendance',
            'warning'
        );

        return back()->with('success', 'Request rejected successfully.');
    }

    /**
     * Get reasons by category (AJAX)
     */
    public function getReasons($category)
    {
        $reasons = AttendanceReason::where('category', $category)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        return response()->json($reasons);
    }

    /**
     * Calculate late minutes
     */
    protected function calculateLateMinutes($arrivalTime, $schoolStartTime = '08:00:00')
    {
        if (!$arrivalTime) return 0;
        
        $arrival = Carbon::parse($arrivalTime);
        $start = Carbon::parse($schoolStartTime);
        
        if ($arrival->gt($start)) {
            return $arrival->diffInMinutes($start);
        }
        
        return 0;
    }

    /**
     * Generate monthly summary for a class section
     */
    protected function generateMonthlySummary($classSectionId, $date)
    {
        $students = Student::where('class_section_id', $classSectionId)->get();
        $year = Carbon::parse($date)->year;
        $monthNum = Carbon::parse($date)->month;
        
        foreach ($students as $student) {
            StudentAttendanceSummary::generateSummary($student->id, $year, $monthNum);
        }
    }

    /**
     * Calculate summary for report
     */
    private function calculateSummary($classSectionId, $fromDate, $toDate)
    {
        $students = Student::where('class_section_id', $classSectionId)->get();
        $attendances = StudentAttendance::where('class_section_id', $classSectionId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->get()
            ->groupBy('student_id');
        
        $summary = [];
        foreach ($students as $student) {
            $records = $attendances->get($student->id, collect());
            $totalDays = $records->count();
            $present = $records->where('status', 'present')->count();
            $absent = $records->where('status', 'absent')->count();
            $late = $records->where('status', 'late')->count();
            $halfDay = $records->where('status', 'half_day')->count();
            $leave = $records->where('status', 'leave')->count();
            $holiday = $records->where('status', 'holiday')->count();
            $onDuty = $records->where('status', 'on_duty')->count();
            
            $attended = $present + $late + $halfDay + $onDuty;
            $percentage = $totalDays > 0 ? round(($attended / $totalDays) * 100, 2) : 0;
            
            $summary[] = [
                'student' => $student,
                'total_days' => $totalDays,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'half_day' => $halfDay,
                'leave' => $leave,
                'holiday' => $holiday,
                'on_duty' => $onDuty,
                'attended' => $attended,
                'percentage' => $percentage,
            ];
        }
        
        usort($summary, function ($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });
        
        return $summary;
    }

    /**
     * Export attendance to CSV (Class-wise)
     */
    public function export(Request $request)
    {
        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $classSection = ClassSection::with(['class.grade', 'class.stream'])->find($request->class_section_id);
        $students = Student::where('class_section_id', $request->class_section_id)->orderBy('first_name')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=attendance_report_{$classSection->full_name}_{$request->from_date}_to_{$request->to_date}.csv",
        ];

        $callback = function() use ($students, $request, $classSection) {
            $handle = fopen('php://output', 'w');
            
            fputcsv($handle, ['Attendance Report']);
            fputcsv($handle, ['Class: ' . $classSection->full_name]);
            fputcsv($handle, ['Period: ' . $request->from_date . ' to ' . $request->to_date]);
            fputcsv($handle, []);
            fputcsv($handle, [
                'Student Name',
                'Roll Number',
                'Total Days',
                'Present',
                'Absent',
                'Late',
                'Late Reasons',
                'Half Day',
                'Half Day Reasons',
                'Leave',
                'Leave Reason',
                'Holiday',
                'On Duty',
                'Attendance %'
            ]);

            foreach ($students as $student) {
                $attendances = StudentAttendance::where('student_id', $student->id)
                    ->whereBetween('date', [$request->from_date, $request->to_date])
                    ->get();

                $totalDays = $attendances->count();
                $present = $attendances->where('status', 'present')->count();
                $absent = $attendances->where('status', 'absent')->count();
                $late = $attendances->where('status', 'late')->count();
                $halfDay = $attendances->where('status', 'half_day')->count();
                $leave = $attendances->where('status', 'leave')->count();
                $holiday = $attendances->where('status', 'holiday')->count();
                $onDuty = $attendances->where('status', 'on_duty')->count();
                
                $attended = $present + $late + $halfDay + $onDuty;
                $percentage = $totalDays > 0 ? round(($attended / $totalDays) * 100, 2) : 0;

                $lateReasons = $attendances->where('status', 'late')
                    ->pluck('late_reason')
                    ->filter()
                    ->implode('; ');

                $halfDayReasons = $attendances->where('status', 'half_day')
                    ->pluck('half_day_reason')
                    ->filter()
                    ->implode('; ');

                $leaveReasons = $attendances->where('status', 'leave')
                    ->pluck('leave_reason')
                    ->filter()
                    ->implode('; ');

                fputcsv($handle, [
                    $student->first_name . ' ' . $student->last_name,
                    $student->roll_number ?? 'N/A',
                    $totalDays,
                    $present,
                    $absent,
                    $late,
                    $lateReasons ?: 'N/A',
                    $halfDay,
                    $halfDayReasons ?: 'N/A',
                    $leave,
                    $leaveReasons ?: 'N/A',
                    $holiday,
                    $onDuty,
                    $percentage . '%'
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Section-wise report
     */
    public function sectionWiseReport(Request $request)
    {
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        
        $fromDate = $request->from_date ?? now()->startOfMonth()->toDateString();
        $toDate = $request->to_date ?? now()->toDateString();
        
        $sectionStats = [];
        $overallStats = [
            'total_students' => 0,
            'total_present' => 0,
            'total_absent' => 0,
            'total_late' => 0,
            'total_half_day' => 0,
            'total_leave' => 0,
            'total_days' => 0,
        ];
        
        foreach ($classSections as $section) {
            $students = Student::where('class_section_id', $section->id)->get();
            $studentIds = $students->pluck('id')->toArray();
            
            if (empty($studentIds)) {
                continue;
            }
            
            $attendances = StudentAttendance::whereIn('student_id', $studentIds)
                ->whereBetween('date', [$fromDate, $toDate])
                ->get();
            
            $totalStudents = $students->count();
            $totalDays = $attendances->groupBy('date')->count();
            $present = $attendances->where('status', 'present')->count();
            $absent = $attendances->where('status', 'absent')->count();
            $late = $attendances->where('status', 'late')->count();
            $halfDay = $attendances->where('status', 'half_day')->count();
            $leave = $attendances->where('status', 'leave')->count();
            $holiday = $attendances->where('status', 'holiday')->count();
            $onDuty = $attendances->where('status', 'on_duty')->count();
            
            $totalRecords = $present + $absent + $late + $halfDay + $leave + $holiday + $onDuty;
            $attended = $present + $late + $halfDay + $onDuty;
            
            $presentPercentage = $totalRecords > 0 ? round(($present / $totalRecords) * 100, 2) : 0;
            $absentPercentage = $totalRecords > 0 ? round(($absent / $totalRecords) * 100, 2) : 0;
            $latePercentage = $totalRecords > 0 ? round(($late / $totalRecords) * 100, 2) : 0;
            $halfDayPercentage = $totalRecords > 0 ? round(($halfDay / $totalRecords) * 100, 2) : 0;
            
            $avgAttendance = 0;
            if ($totalStudents > 0 && $totalDays > 0) {
                $avgAttendance = round(($attended / ($totalStudents * $totalDays)) * 100, 2);
            }
            
            $sectionStats[] = [
                'section' => $section,
                'total_students' => $totalStudents,
                'total_days' => $totalDays,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'half_day' => $halfDay,
                'leave' => $leave,
                'holiday' => $holiday,
                'on_duty' => $onDuty,
                'total_records' => $totalRecords,
                'present_percentage' => $presentPercentage,
                'absent_percentage' => $absentPercentage,
                'late_percentage' => $latePercentage,
                'half_day_percentage' => $halfDayPercentage,
                'avg_attendance' => $avgAttendance,
            ];
            
            $overallStats['total_students'] += $totalStudents;
            $overallStats['total_present'] += $present;
            $overallStats['total_absent'] += $absent;
            $overallStats['total_late'] += $late;
            $overallStats['total_half_day'] += $halfDay;
            $overallStats['total_leave'] += $leave;
            $overallStats['total_days'] += $totalDays;
        }
        
        usort($sectionStats, function($a, $b) {
            return $b['avg_attendance'] <=> $a['avg_attendance'];
        });
        
        $overallTotal = $overallStats['total_present'] + $overallStats['total_absent'] + 
            $overallStats['total_late'] + $overallStats['total_half_day'] + $overallStats['total_leave'];
        
        $overallStats['present_percentage'] = $overallTotal > 0 ? round(($overallStats['total_present'] / $overallTotal) * 100, 2) : 0;
        $overallStats['absent_percentage'] = $overallTotal > 0 ? round(($overallStats['total_absent'] / $overallTotal) * 100, 2) : 0;
        $overallStats['late_percentage'] = $overallTotal > 0 ? round(($overallStats['total_late'] / $overallTotal) * 100, 2) : 0;
        $overallStats['half_day_percentage'] = $overallTotal > 0 ? round(($overallStats['total_half_day'] / $overallTotal) * 100, 2) : 0;
        
        return view('admin.studentattendance.section-wise-report', compact(
            'sectionStats', 
            'overallStats', 
            'fromDate', 
            'toDate', 
            'classSections'
        ));
    }

    // =============================================
    // TEACHER PERMISSIONS
    // =============================================

    public function managePermissions()
    {
        $teachers = Staff::where('is_teacher', true)->get();
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        $subjects = Subject::all();
        
        $permissions = AttendanceTeacherSection::with(['teacher', 'classSection', 'subject', 'assigner'])
            ->get()
            ->groupBy('teacher_id');
        
        return view('admin.studentattendance.permissions', compact('teachers', 'classSections', 'subjects', 'permissions'));
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:staff,id',
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'can_mark_attendance' => 'boolean',
            'can_approve_leave' => 'boolean',
        ]);
        
        $assignedBy = auth()->id() ?? Staff::first()->id ?? null;
        
        AttendanceTeacherSection::updateOrCreate(
            [
                'teacher_id' => $request->teacher_id,
                'class_section_id' => $request->class_section_id,
                'subject_id' => $request->subject_id,
            ],
            [
                'can_mark_attendance' => $request->can_mark_attendance ?? true,
                'can_approve_leave' => $request->can_approve_leave ?? false,
                'assigned_by' => $assignedBy,
            ]
        );
        
        return back()->with('success', 'Permission assigned successfully.');
    }

    public function destroyPermission($id)
    {
        $permission = AttendanceTeacherSection::findOrFail($id);
        $permission->delete();
        
        return back()->with('success', 'Permission removed successfully.');
    }

    /**
     * Bulk assign permissions to all teachers
     */
    public function bulkAssignPermissions(Request $request)
    {
        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'can_mark_attendance' => 'boolean',
            'can_approve_leave' => 'boolean',
        ]);

        $teachers = Staff::where('is_teacher', true)->get();
        $assignedBy = auth()->id() ?? Staff::first()->id ?? null;
        
        $assigned = 0;
        $skipped = 0;

        foreach ($teachers as $teacher) {
            $exists = AttendanceTeacherSection::where('teacher_id', $teacher->id)
                ->where('class_section_id', $request->class_section_id)
                ->where('subject_id', $request->subject_id)
                ->exists();

            if (!$exists) {
                AttendanceTeacherSection::create([
                    'teacher_id' => $teacher->id,
                    'class_section_id' => $request->class_section_id,
                    'subject_id' => $request->subject_id,
                    'can_mark_attendance' => $request->can_mark_attendance ?? true,
                    'can_approve_leave' => $request->can_approve_leave ?? false,
                    'assigned_by' => $assignedBy,
                ]);
                $assigned++;
            } else {
                $skipped++;
            }
        }

        $message = "Permissions assigned to {$assigned} teachers";
        if ($skipped > 0) {
            $message .= " ({$skipped} already had permissions)";
        }

        return back()->with('success', $message);
    }

    /**
     * Bulk remove permissions
     */
    public function bulkRemovePermissions(Request $request)
    {
        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $deleted = AttendanceTeacherSection::where('class_section_id', $request->class_section_id)
            ->when($request->subject_id, function($query, $subjectId) {
                return $query->where('subject_id', $subjectId);
            })
            ->delete();

        return back()->with('success', "Permissions removed for {$deleted} teachers.");
    }

    public function viewTeacherPermissions($teacherId)
    {
        $teacher = Staff::findOrFail($teacherId);
        $permissions = AttendanceTeacherSection::with(['classSection', 'subject', 'assigner'])
            ->where('teacher_id', $teacherId)
            ->get();
        
        return view('admin.studentattendance.teacher-permissions', compact('teacher', 'permissions'));
    }

    // =============================================
    // COMMON SUBJECT CLASSES
    // =============================================

    public function manageCommonClasses(Request $request)
    {
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        $subjects = Subject::all();
        $rooms = Room::where('is_available', true)->get();
        
        $selectedSection = null;
        $selectedSections = [];
        $commonClasses = collect();
        
        if ($request->has('class_section_id') && $request->class_section_id) {
            $selectedSection = ClassSection::with('class.grade', 'class.stream')
                ->find($request->class_section_id);
            
            if ($selectedSection) {
                $selectedSections = ClassSection::where('class_id', $selectedSection->class_id)
                    ->with('class.grade', 'class.stream')
                    ->get();
            }
        }
        
        $commonClassesQuery = CommonSubjectClass::with(['subject', 'room']);
        
        if ($selectedSection) {
            $commonClasses = $commonClassesQuery->get()->filter(function($class) use ($selectedSection) {
                $sectionIds = $class->class_section_ids ?? [];
                return in_array((string)$selectedSection->id, $sectionIds);
            });
        } else {
            $commonClasses = $commonClassesQuery->get();
        }
        
        return view('admin.studentattendance.common-classes', compact(
            'classSections', 'subjects', 'rooms', 'selectedSection', 
            'selectedSections', 'commonClasses'
        ));
    }

    public function storeCommonClass(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'room_id' => 'required|exists:rooms,id',
            'class_section_ids' => 'required|array',
            'class_section_ids.*' => 'exists:class_sections,id',
        ]);
        
        CommonSubjectClass::create([
            'subject_id' => $request->subject_id,
            'name' => $request->name,
            'description' => $request->description,
            'room_id' => $request->room_id,
            'class_section_ids' => $request->class_section_ids,
            'is_active' => true,
        ]);
        
        return back()->with('success', 'Common subject class created successfully.');
    }

    public function editCommonClass($id)
    {
        $commonClass = CommonSubjectClass::with(['subject', 'room'])->findOrFail($id);
        $subjects = Subject::all();
        $rooms = Room::where('is_available', true)->get();
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        
        $selectedSectionIds = $commonClass->class_section_ids ?? [];
        
        return view('admin.studentattendance.common-classes-edit', compact(
            'commonClass', 'subjects', 'rooms', 'classSections', 'selectedSectionIds'
        ));
    }

    public function updateCommonClass(Request $request, $id)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'room_id' => 'required|exists:rooms,id',
            'class_section_ids' => 'required|array',
            'class_section_ids.*' => 'exists:class_sections,id',
        ]);
        
        $commonClass = CommonSubjectClass::findOrFail($id);
        
        $commonClass->update([
            'subject_id' => $request->subject_id,
            'name' => $request->name,
            'description' => $request->description,
            'room_id' => $request->room_id,
            'class_section_ids' => $request->class_section_ids,
            'is_active' => $request->is_active ?? true,
        ]);
        
        return redirect()->route('admin.studentattendance.common-classes')
            ->with('success', 'Common subject class updated successfully.');
    }

    public function destroyCommonClass($id)
    {
        $commonClass = CommonSubjectClass::findOrFail($id);
        $commonClass->delete();
        
        return back()->with('success', 'Common subject class deleted successfully.');
    }

    /**
     * Show pending leave requests
     */
    public function pendingLeaveRequests()
    {
        $pendingLeaves = StudentAttendance::whereIn('status', ['leave', 'half_day'])
            ->where('is_approved', false)
            ->with(['student', 'classSection', 'teacher'])
            ->orderBy('date', 'desc')
            ->get();
        
        $statusOptions = StudentAttendance::getStatusOptions();
        
        return view('admin.studentattendance.pending-leaves', compact('pendingLeaves', 'statusOptions'));
    }
}