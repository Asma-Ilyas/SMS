<?php

namespace App\Http\Controllers\Student;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends BaseStudentController
{
    public function index()
    {
        $s     = $this->student();
        $today = Carbon::today();

        $attendanceMonth   = $this->attendanceSummary($s->id, $today->copy()->startOfMonth()->toDateString(), $today->toDateString());
        $attendanceOverall = $this->attendanceSummary($s->id);

        // Today's classes
        $todayClasses = collect();
        $dayName = strtolower($today->format('l'));
        if ($s->class_section_id && in_array($dayName, self::DAYS, true)) {
            $todayClasses = $this->timetableQuery($s->class_section_id)
                ->where('te.day_of_week', $dayName)
                ->orderBy('ts.sort_order')->get();
        }

        // Upcoming exams
        $upcomingExams = collect();
        if ($s->class_section_id) {
            $upcomingExams = DB::table('exams as e')
                ->leftJoin('exam_types as et', 'et.id', '=', 'e.exam_type_id')
                ->where('e.class_section_id', $s->class_section_id)
                ->whereDate('e.end_date', '>=', $today)
                ->orderBy('e.start_date')->limit(5)
                ->select('e.*', 'et.name as type_name')->get();
        }

        // Latest published results
        $latestResults = DB::table('exam_results as er')
            ->join('exams as e', 'e.id', '=', 'er.exam_id')
            ->where('er.student_id', $s->id)->where('e.is_published', 1)
            ->orderByDesc('e.end_date')->limit(5)
            ->select('er.*', 'e.name as exam_name', 'e.end_date', 'e.passing_percentage')->get();

        // Fees
        $feeSummary = DB::table('invoices')
            ->where('student_id', $s->id)->whereIn('status', ['pending', 'overdue'])
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(net_amount),0) as total, MIN(due_date) as next_due')
            ->first();
        $overdueCount = DB::table('invoices')
            ->where('student_id', $s->id)->whereIn('status', ['pending', 'overdue'])
            ->whereDate('due_date', '<', $today)->count();

        // Transport & hostel (tables come from separate migrations)
        $transport = null;
        if (Schema::hasTable('student_transports')) {
            $transport = DB::table('student_transports as st')
                ->join('transport_routes as r', 'r.id', '=', 'st.route_id')
                ->leftJoin('route_stops as rs', 'rs.id', '=', 'st.route_stop_id')
                ->where('st.student_id', $s->id)->where('st.status', 'active')
                ->select('r.name as route_name', 'rs.stop_name', 'rs.pickup_time')->first();
        }

        $hostel = null;
        if (Schema::hasTable('student_hostel_allocations')) {
            $hostel = DB::table('student_hostel_allocations as a')
                ->join('hostels as h', 'h.id', '=', 'a.hostel_id')
                ->join('hostel_rooms as r', 'r.id', '=', 'a.room_id')
                ->where('a.student_id', $s->id)->where('a.status', 'active')
                ->select('h.name as hostel_name', 'r.room_number', 'a.bed_number')->first();
        }

        return view('student.dashboard', compact(
            's', 'attendanceMonth', 'attendanceOverall', 'todayClasses', 'upcomingExams',
            'latestResults', 'feeSummary', 'overdueCount', 'transport', 'hostel'
        ));
    }
}
