<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ==================== 1. CREATE PERMISSIONS ====================
        $permissions = [
            // Students
            'view students', 'create students', 'edit students', 'delete students',
            'view own profile',
            'view own documents', 'upload own documents',
            'view own id card', 'download id card',

            // Classes & Sections
            'view classes', 'manage classes',
            'view own class', 'view own section',

            // Subjects
            'view subjects', 'manage subjects',
            'view own subjects',

            // Attendance
            'view attendance', 'mark attendance', 'manage attendance',
            'view own attendance', 'view own attendance summary',
            'apply leave', 'view own leave requests',

            // Exams
            'view exams', 'manage exams', 'enter marks', 'publish results',
            'view own marks', 'view own exam schedule', 'view own results',
            'download report card', 'view own grade',

            // Timetable
            'view timetable', 'manage timetable',
            'view own timetable',

            // Fees
            'view fees', 'manage fees', 'collect fees',
            'view own fees', 'view own fee history', 'view own invoices',
            'download invoice', 'view own due fees', 'view own payment history',

            // Transport
            'view transport', 'manage transport',
            'view own transport', 'view own transport route', 'view own transport fee',

            // Hostel
            'view hostel', 'manage hostel',
            'view own hostel', 'view own hostel room', 'view own hostel fee',

            // Library
            'view library', 'manage library',
            'view own borrowed books', 'view own library history',

            // Homework
            'view homework', 'manage homework',
            'view own homework', 'submit homework', 'view own submissions',

            // Certificates
            'request certificate', 'approve certificate',
            'view own certificates', 'download own certificate',

            // Announcements
            'view announcements', 'manage announcements',
            'view own notifications',

            // Events
            'view events', 'manage events',
            'view own events',

            // Reports
            'view reports',
            'view own reports', 'view own progress report',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ==================== 2. CREATE 3 ROLES ====================
        $admin   = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $teacher = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // ==================== 3. ASSIGN PERMISSIONS ====================

        // ---- ADMIN — everything ----
        $admin->syncPermissions(Permission::all());

        // ---- TEACHER ----
        $teacher->syncPermissions([
            'view students',
            'view classes', 'view subjects',
            'view attendance', 'mark attendance', 'manage attendance',
            'view exams', 'manage exams', 'enter marks', 'publish results',
            'view timetable', 'manage timetable',
            'view homework', 'manage homework',
            'view library', 'manage library',
            'view announcements', 'manage announcements',
            'view events', 'manage events',
            'view reports',
        ]);

        // ---- STUDENT — own data only, no profile edit ----
        $student->syncPermissions([
            // Profile (view only)
            'view own profile',
            'view own documents', 'upload own documents',
            'view own id card', 'download id card',
            'view own class', 'view own section', 'view own subjects',

            // Attendance
            'view own attendance', 'view own attendance summary',
            'apply leave', 'view own leave requests',

            // Exams
            'view own marks', 'view own exam schedule', 'view own results',
            'download report card', 'view own grade',

            // Timetable
            'view own timetable',

            // Fees
            'view own fees', 'view own fee history', 'view own invoices',
            'download invoice', 'view own due fees', 'view own payment history',

            // Transport
            'view own transport', 'view own transport route', 'view own transport fee',

            // Hostel
            'view own hostel', 'view own hostel room', 'view own hostel fee',

            // Library
            'view own borrowed books', 'view own library history',

            // Homework
            'view own homework', 'submit homework', 'view own submissions',

            // Certificates
            'request certificate', 'view own certificates', 'download own certificate',

            // Announcements & Events
            'view announcements', 'view own notifications', 'view own events',

            // Reports
            'view own reports', 'view own progress report',
        ]);
    }
}