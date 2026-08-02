<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Staff;
use App\Models\Classes;
use App\Models\Exam;
use App\Models\StudentAttendance;
use App\Models\Invoice;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        // =============================================
        // STUDENT STATS
        // =============================================
        try {
            $totalStudents = Student::count();
        } catch (\Exception $e) {
            $totalStudents = 0;
        }

        try {
            $newStudents = Student::whereMonth('created_at', now()->month)->count();
        } catch (\Exception $e) {
            $newStudents = 0;
        }

        try {
            $newStudentsToday = Student::whereDate('created_at', today())->count();
        } catch (\Exception $e) {
            $newStudentsToday = 0;
        }

        // =============================================
        // STAFF/TEACHER STATS
        // =============================================
        try {
            $totalTeachers = Staff::where('is_teacher', true)->count();
        } catch (\Exception $e) {
            $totalTeachers = 0;
        }

        try {
            $activeTeachers = Staff::where('is_teacher', true)->where('is_active', true)->count();
        } catch (\Exception $e) {
            $activeTeachers = 0;
        }

        // =============================================
        // CLASS STATS
        // =============================================
        try {
            $totalClasses = Classes::count();
        } catch (\Exception $e) {
            $totalClasses = 0;
        }

        try {
            $activeClasses = Classes::where('is_active', true)->count();
        } catch (\Exception $e) {
            $activeClasses = 0;
        }

        // =============================================
        // EXAM STATS
        // =============================================
        try {
            $totalExams = Exam::count();
        } catch (\Exception $e) {
            $totalExams = 0;
        }

        try {
            $upcomingExams = Exam::where('start_date', '>=', now())->count();
        } catch (\Exception $e) {
            $upcomingExams = 0;
        }

        try {
            $examsToday = Exam::whereDate('start_date', today())->count();
        } catch (\Exception $e) {
            $examsToday = 0;
        }

        // =============================================
        // ATTENDANCE STATS
        // =============================================
        try {
            $totalAttendance = StudentAttendance::whereDate('date', today())->count();
            $presentAttendance = StudentAttendance::whereDate('date', today())->where('status', 'present')->count();
            $attendanceRate = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100, 1) : 0;
        } catch (\Exception $e) {
            $attendanceRate = 0;
        }

        // =============================================
        // TIMETABLE STATS
        // =============================================
        try {
            $classesToday = TimetableEntry::where('day_of_week', strtolower(now()->format('l')))->count();
        } catch (\Exception $e) {
            $classesToday = 0;
        }

        // =============================================
        // REVENUE STATS
        // =============================================
        try {
            $totalRevenue = Invoice::where('status', 'paid')->sum('amount') ?? 0;
        } catch (\Exception $e) {
            $totalRevenue = 0;
        }

        try {
            $revenueToday = Invoice::where('status', 'paid')
                ->whereDate('paid_at', today())
                ->sum('amount') ?? 0;
        } catch (\Exception $e) {
            $revenueToday = 0;
        }

        // =============================================
        // RECENT ACTIVITIES (Static for now)
        // =============================================
        $recentActivities = [
            [
                'title' => 'Welcome to Admin Panel',
                'description' => 'Your school management system is ready',
                'time' => 'Just now',
                'color' => '#4f46e5'
            ]
        ];

        // =============================================
        // UPCOMING EVENTS (Static for now)
        // =============================================
        $upcomingEvents = [
            [
                'title' => 'Start Managing Your School',
                'date' => now()->addDays(1),
                'time' => '09:00 AM'
            ]
        ];

        // =============================================
        // COMPILE DATA
        // =============================================
        $data = [
            'totalStudents' => $totalStudents,
            'newStudents' => $newStudents,
            'totalTeachers' => $totalTeachers,
            'activeTeachers' => $activeTeachers,
            'totalClasses' => $totalClasses,
            'activeClasses' => $activeClasses,
            'totalExams' => $totalExams,
            'upcomingExams' => $upcomingExams,
            'attendanceRate' => $attendanceRate,
            'totalRevenue' => $totalRevenue,
            'revenueGrowth' => 0,
            'classesToday' => $classesToday,
            'attendanceToday' => $attendanceRate,
            'examsToday' => $examsToday,
            'newStudentsToday' => $newStudentsToday,
            'revenueToday' => $revenueToday,
            'recentActivities' => $recentActivities,
            'upcomingEvents' => $upcomingEvents,
        ];

        return view('admin.dashboard', $data);
    }
}