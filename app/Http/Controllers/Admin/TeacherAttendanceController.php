<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\TimeSlot;
use App\Models\TimetableEntry;
use App\Models\TeacherClassAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeacherAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? now()->toDateString();
        $teachers = Staff::where('is_teacher', true)
                        ->active()
                        ->with(['attendances' => function($q) use ($date) {
                            $q->where('date', $date);
                        }])
                        ->orderBy('first_name')
                        ->get();

        $classSections = ClassSection::with(['class.grade'])->get();
        $subjects = Subject::where('is_active', true)->get();
        $timeSlots = TimeSlot::where('is_active', true)->where('type', 'period')->get();

        return view('admin.teacher-attendance.index', compact(
            'teachers', 'classSections', 'subjects', 'timeSlots', 'date'
        ));
    }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:staff,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,leave,on_duty',
            'arrival_time' => 'nullable',
            'departure_time' => 'nullable',
            'class_section_id' => 'nullable|exists:class_sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'time_slot_id' => 'nullable|exists:time_slots,id',
        ]);

        $teacher = Staff::findOrFail($request->teacher_id);

        $attendance = StaffAttendance::updateOrCreate(
            [
                'staff_id' => $request->teacher_id,
                'date' => $request->date,
            ],
            [
                'status' => $request->status,
                'arrival_time' => $request->arrival_time,
                'departure_time' => $request->departure_time,
                'class_section_id' => $request->class_section_id,
                'subject_id' => $request->subject_id,
                'time_slot_id' => $request->time_slot_id,
                'class_taken' => $request->status == 'present' ? true : false,
                'remarks' => $request->remarks,
                'is_approved' => true,
                'marked_by' => auth()->id(),
            ]
        );

        // Update teacher class attendance
        if ($request->class_section_id && $request->subject_id && $request->time_slot_id) {
            TeacherClassAttendance::updateOrCreate(
                [
                    'teacher_id' => $request->teacher_id,
                    'class_section_id' => $request->class_section_id,
                    'subject_id' => $request->subject_id,
                    'time_slot_id' => $request->time_slot_id,
                    'date' => $request->date,
                ],
                [
                    'status' => $request->status,
                    'class_taken' => $request->status == 'present' ? true : false,
                    'remarks' => $request->remarks,
                ]
            );
        }

        return redirect()->back()->with('success', "Attendance marked for {$teacher->full_name}");
    }

    public function classWiseReport(Request $request)
    {
        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'date' => 'nullable|date',
        ]);

        $classSection = ClassSection::with(['class.grade'])->findOrFail($request->class_section_id);
        $date = $request->date ?? now()->toDateString();
        $startDate = $request->start_date ?? now()->startOfWeek()->toDateString();
        $endDate = $request->end_date ?? now()->endOfWeek()->toDateString();

        $timetable = TimetableEntry::with(['teacher', 'subject', 'timeSlot'])
            ->where('class_section_id', $classSection->id)
            ->where('day_of_week', strtolower(Carbon::parse($date)->format('l')))
            ->get();

        $teacherIds = $timetable->pluck('teacher_id')->unique()->toArray();
        $attendances = StaffAttendance::with(['staff'])
            ->whereIn('staff_id', $teacherIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('staff_id');

        $weeklyReport = $this->getWeeklyClassReport($classSection->id, $startDate, $endDate);

        return view('admin.teacher-attendance.class-wise', compact(
            'classSection', 'date', 'startDate', 'endDate',
            'timetable', 'attendances', 'weeklyReport'
        ));
    }

    public function perClassAttendance(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:staff,id',
            'date' => 'required|date',
        ]);

        $teacher = Staff::findOrFail($request->teacher_id);
        $date = $request->date;
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

        $timetable = TimetableEntry::with(['classSection.class.grade', 'subject', 'timeSlot'])
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('time_slot_id')
            ->get();

        $classAttendance = [];
        foreach ($timetable as $entry) {
            $attendance = StaffAttendance::where('staff_id', $teacher->id)
                ->where('date', $date)
                ->where('class_section_id', $entry->class_section_id)
                ->first();

            $classAttendance[] = [
                'timetable' => $entry,
                'attendance' => $attendance,
                'status' => $attendance ? $attendance->status : 'not_marked',
                'arrival_time' => $attendance ? $attendance->arrival_time : null,
                'departure_time' => $attendance ? $attendance->departure_time : null,
            ];
        }

        return view('admin.teacher-attendance.per-class', compact('teacher', 'date', 'classAttendance'));
    }

    public function arrivalDepartureReport(Request $request)
    {
        $request->validate([
            'teacher_id' => 'nullable|exists:staff,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $fromDate = $request->from_date ?? now()->startOfMonth()->toDateString();
        $toDate = $request->to_date ?? now()->toDateString();

        $query = StaffAttendance::with(['staff'])
            ->whereBetween('date', [$fromDate, $toDate])
            ->whereNotNull('arrival_time');

        if ($request->filled('teacher_id')) {
            $query->where('staff_id', $request->teacher_id);
        }

        $attendances = $query->orderBy('date', 'desc')
                            ->orderBy('staff_id')
                            ->get()
                            ->groupBy('staff_id');

        $teachers = Staff::where('is_teacher', true)->active()->get();

        return view('admin.teacher-attendance.arrival-departure', compact(
            'attendances', 'teachers', 'fromDate', 'toDate'
        ));
    }

    public function summaryReport(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $startDate = Carbon::parse($month . '-01');
        $endDate = $startDate->copy()->endOfMonth();

        $teachers = Staff::where('is_teacher', true)
                        ->active()
                        ->with(['attendances' => function($q) use ($startDate, $endDate) {
                            $q->whereBetween('date', [$startDate, $endDate]);
                        }])
                        ->get();

        $summary = [];
        foreach ($teachers as $teacher) {
            $attendances = $teacher->attendances;
            $totalDays = $attendances->count();
            $present = $attendances->where('status', 'present')->count();
            $absent = $attendances->where('status', 'absent')->count();
            $late = $attendances->where('status', 'late')->count();
            $leave = $attendances->where('status', 'leave')->count();
            $classesTaken = $attendances->where('class_taken', true)->count();

            $summary[] = [
                'teacher' => $teacher,
                'total_days' => $totalDays,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'leave' => $leave,
                'classes_taken' => $classesTaken,
                'percentage' => $totalDays > 0 ? round(($present / $totalDays) * 100, 2) : 0,
            ];
        }

        return view('admin.teacher-attendance.summary', compact('summary', 'month', 'startDate', 'endDate'));
    }

    // ========== PRIVATE METHODS ==========

    private function getWeeklyClassReport($classSectionId, $startDate, $endDate)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $report = [];

        foreach ($days as $day) {
            $date = Carbon::parse($startDate)->next($day);
            if ($date->gt(Carbon::parse($endDate))) continue;

            $timetable = TimetableEntry::with(['teacher', 'subject', 'timeSlot'])
                ->where('class_section_id', $classSectionId)
                ->where('day_of_week', $day)
                ->get();

            $dayReport = [
                'date' => $date->format('Y-m-d'),
                'day' => ucfirst($day),
                'periods' => [],
            ];

            foreach ($timetable as $entry) {
                $attendance = StaffAttendance::where('staff_id', $entry->teacher_id)
                    ->where('date', $date->format('Y-m-d'))
                    ->where('class_section_id', $classSectionId)
                    ->first();

                $dayReport['periods'][] = [
                    'time' => $entry->timeSlot->label ?? 'N/A',
                    'subject' => $entry->subject->name ?? 'N/A',
                    'teacher' => $entry->teacher->full_name ?? 'N/A',
                    'status' => $attendance ? $attendance->status : 'not_marked',
                    'arrival_time' => $attendance ? $attendance->arrival_time : null,
                    'departure_time' => $attendance ? $attendance->departure_time : null,
                ];
            }

            $report[] = $dayReport;
        }

        return $report;
    }
}