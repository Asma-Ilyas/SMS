<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubjectTeacher;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\TimeTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentAttendanceController extends Controller
{
    private function getTeacherId()
    {
        // Hardcoded teacher ID – change to a valid teacher from your staff table
        return 1;
    }

    public function index()
    {
        $teacherId = $this->getTeacherId();

        $assignments = ClassSubjectTeacher::with(['class', 'subject'])
            ->where('teacher_id', $teacherId)
            ->get();

        foreach ($assignments as $assignment) {
            $class = $assignment->class;
            if ($class) {
                $gradeName = optional($class->grade)->name ?? '?';
                $streamName = optional($class->stream)->name ?? '';
                $section = $class->section ?? '?';
                $assignment->class_display = trim($gradeName . ' ' . $streamName . ' - ' . $section);
            } else {
                $assignment->class_display = 'Unknown Class';
            }
        }

        return view('admin.studentattendance.index', compact('assignments'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date'     => 'nullable|date',
        ]);

        $teacherId = $this->getTeacherId();
        $classId   = $request->class_id;
        $date      = $request->date ?? Carbon::today()->toDateString();

        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $dayOfWeek = $dayOfWeek == 0 ? 7 : $dayOfWeek;

        // Weekend check (optional)
        // if (in_array($dayOfWeek, [6, 7])) {
        //     abort(403, 'Attendance cannot be marked on weekends (Saturday/Sunday).');
        // }

        $firstPeriod = TimeTable::where('class_id', $classId)
                        ->where('day_of_week', $dayOfWeek)
                        ->orderBy('period_number')
                        ->first();

        if (!$firstPeriod) {
            abort(403, "No timetable defined for this class on {$date}.");
        }

        $subjectId = $firstPeriod->subject_id;

        // Verify teacher is assigned to this class and the first period subject
        // $assignment = ClassSubjectTeacher::where('class_id', $classId)
        //                 ->where('subject_id', $subjectId)
        //                 ->where('teacher_id', $teacherId)
        //                 ->first();

        // if (!$assignment) {
        //     abort(403, 'You are not assigned to teach the first period subject for this class.');
        // }

        $students = Student::where('class_id', $classId)
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->get();

        $attendances = StudentAttendance::where('class_id', $classId)
                        ->where('date', $date)
                        ->get()
                        ->keyBy('student_id');

        return view('admin.studentattendance.mark', compact(
            'classId', 'subjectId', 'date', 'students', 'attendances', 'firstPeriod'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'date'       => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late,half_day',
        ]);

        $teacherId = $this->getTeacherId();
        $classId   = $request->class_id;
        $subjectId = $request->subject_id;
        $date      = $request->date;

        // Re‑validate first period rule for safety
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $dayOfWeek = $dayOfWeek == 0 ? 7 : $dayOfWeek;
        $firstPeriod = TimeTable::where('class_id', $classId)
                        ->where('day_of_week', $dayOfWeek)
                        ->orderBy('period_number')
                        ->first();

        if (!$firstPeriod || $firstPeriod->subject_id != $subjectId) {
            return back()->withErrors(['error' => 'You can only mark attendance for the first period subject of the day.']);
        }

        $assignment = ClassSubjectTeacher::where('class_id', $classId)
                        ->where('subject_id', $subjectId)
                        ->where('teacher_id', $teacherId)
                        ->exists();

        if (!$assignment) {
            abort(403, 'You are not authorised to mark attendance for this class/subject.');
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
                    'teacher_id' => $teacherId,
                    'status'     => $data['status'],
                    'remarks'    => $data['remarks'] ?? null,
                ]
            );
        }

        return redirect()->route('admin.studentattendance.index')
                         ->with('success', 'Attendance saved successfully.');
    }
}