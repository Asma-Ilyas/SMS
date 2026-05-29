<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes as ClassModel;
use App\Models\Staff;
use App\Models\ClassSubjectTeacher;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\TimeTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentAttendanceController extends Controller
{
    /**
     * Get default staff ID (admin or first staff).
     */
    private function getDefaultStaffId()
    {
        $staff = Staff::first();
        return $staff ? $staff->id : 1;
    }

    /**
     * Display all classes (for admin).
     */
    public function index()
    {
        // Fetch all classes with grade and stream
        $classes = ClassModel::with(['grade', 'stream'])->orderBy('id')->get();

        foreach ($classes as $class) {
            $gradeName = optional($class->grade)->name ?? 'Class';
            $streamName = optional($class->stream)->name ?? '';
            $section = $class->section ?? '';
            $parts = array_filter([$gradeName, $streamName, $section]);
            $class->class_display = implode(' - ', $parts);
            if (empty($class->class_display)) {
                $class->class_display = 'Class #' . $class->id;
            }
        }

        return view('admin.studentattendance.index', compact('classes'));
    }

    /**
     * Show the attendance marking form for a specific class and date.
     */
    public function create(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date'     => 'nullable|date',
        ]);

        $classId = $request->class_id;
        $date    = $request->date ?? Carbon::today()->toDateString();
        $staffId = $this->getDefaultStaffId();

        // Get class details
        $class = ClassModel::with(['grade', 'stream'])->findOrFail($classId);
        $gradeName = optional($class->grade)->name ?? 'Class';
        $streamName = optional($class->stream)->name ?? '';
        $section = $class->section ?? '';
        $classNameDisplay = trim($gradeName . ' ' . $streamName . ' ' . $section);

        $staff = Staff::find($staffId);
        $staffName = $staff ? $staff->name : 'Admin';

        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $dayOfWeek = $dayOfWeek == 0 ? 7 : $dayOfWeek;

        $firstPeriod = TimeTable::where('class_id', $classId)
                        ->where('day_of_week', $dayOfWeek)
                        ->orderBy('period_number')
                        ->first();

        if (!$firstPeriod) {
            abort(403, "No timetable defined for this class on {$date}.");
        }

        $subjectId = $firstPeriod->subject_id;
        $subject = Subject::find($subjectId);

        $students = Student::where('class_id', $classId)
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->get();

        $attendances = StudentAttendance::where('class_id', $classId)
                        ->where('date', $date)
                        ->get()
                        ->keyBy('student_id');

        return view('admin.studentattendance.mark', compact(
            'classId', 'classNameDisplay', 'section', 'staffName', 'subject',
            'date', 'students', 'attendances', 'firstPeriod'
        ));
    }

    /**
     * Store attendance records.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'date'       => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late,half_day',
        ]);

        $staffId = $this->getDefaultStaffId();
        $classId   = $request->class_id;
        $subjectId = $request->subject_id;
        $date      = $request->date;

        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $dayOfWeek = $dayOfWeek == 0 ? 7 : $dayOfWeek;
        $firstPeriod = TimeTable::where('class_id', $classId)
                        ->where('day_of_week', $dayOfWeek)
                        ->orderBy('period_number')
                        ->first();

        if (!$firstPeriod || $firstPeriod->subject_id != $subjectId) {
            return back()->withErrors(['error' => 'You can only mark attendance for the first period subject of the day.']);
        }

        foreach ($request->attendance as $studentId => $data) {
            StudentAttendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date'       => $date,
                ],
                [
                    'class_id'   => $classId,
                    'subject_id' => $subjectId,
                    'teacher_id' => $staffId,
                    'status'     => $data['status'],
                    'remarks'    => $data['remarks'] ?? null,
                ]
            );
        }

        return redirect()->route('admin.studentattendance.index')
                         ->with('success', 'Attendance saved successfully.');
    }

    /**
     * Show attendance report with filters.
     */
    public function report(Request $request)
    {
        $request->validate([
            'class_id'   => 'nullable|exists:classes,id',
            'student_id' => 'nullable|exists:students,id',
            'from_date'  => 'nullable|date',
            'to_date'    => 'nullable|date|after_or_equal:from_date',
            'status'     => 'nullable|in:present,absent,late,half_day',
        ]);

        // Fix: order by 'id' instead of 'name'
        $classes = ClassModel::with(['grade', 'stream'])->orderBy('id')->get();
        $students = collect();

        $query = StudentAttendance::with(['student', 'class', 'subject', 'staff']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
            $students = Student::where('class_id', $request->class_id)->orderBy('first_name')->get();
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

        $attendances = $query->orderBy('date', 'desc')->paginate(20);

        $summary = null;
        if ($request->filled('class_id') && $request->filled('from_date') && $request->filled('to_date')) {
            $summary = $this->calculateClassAttendanceSummary(
                $request->class_id,
                $request->from_date,
                $request->to_date
            );
        }

        return view('admin.studentattendance.report', compact('classes', 'students', 'attendances', 'summary', 'request'));
    }

    /**
     * Calculate attendance summary for a class over a date range.
     */
    private function calculateClassAttendanceSummary($classId, $fromDate, $toDate)
    {
        $students = Student::where('class_id', $classId)->get();
        $attendances = StudentAttendance::where('class_id', $classId)
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

            $score = $present + ($late * 0.5) + ($halfDay * 0.5);
            $percentage = $totalDays > 0 ? round(($score / $totalDays) * 100, 2) : 0;

            $summary[] = [
                'student'    => $student,
                'total_days' => $totalDays,
                'present'    => $present,
                'absent'     => $absent,
                'late'       => $late,
                'half_day'   => $halfDay,
                'percentage' => $percentage,
            ];
        }

        usort($summary, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        return $summary;
    }

    /**
     * Show individual student attendance report.
     */
    public function studentReport($studentId)
    {
        $student = Student::findOrFail($studentId);
        $attendances = StudentAttendance::where('student_id', $studentId)
                        ->with(['class', 'subject', 'staff'])
                        ->orderBy('date', 'desc')
                        ->paginate(30);

        $monthlyStats = StudentAttendance::where('student_id', $studentId)
                        ->select(DB::raw('DATE_FORMAT(date, "%Y-%m") as month'), 
                                 DB::raw('COUNT(*) as total'),
                                 DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                                 DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                                 DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late'),
                                 DB::raw('SUM(CASE WHEN status = "half_day" THEN 1 ELSE 0 END) as half_day'))
                        ->groupBy('month')
                        ->orderBy('month', 'desc')
                        ->get();

        return view('admin.studentattendance.student_report', compact('student', 'attendances', 'monthlyStats'));
    }
}