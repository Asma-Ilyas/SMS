<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixAttendanceStatusSeeder extends Seeder
{
    public function run()
    {
        // Update statuses without changing column structure
        // Convert 'late' to 'absent'
        DB::table('student_attendance')
            ->where('status', 'late')
            ->update(['status' => 'absent']);
        
        // Convert 'half_day' to 'absent' (since we can't use 'leave' without changing ENUM)
        DB::table('student_attendance')
            ->where('status', 'half_day')
            ->update(['status' => 'absent']);
        
        // Convert 'half' to 'absent'
        DB::table('student_attendance')
            ->where('status', 'half')
            ->update(['status' => 'absent']);

        // Add a new column for leave reason (optional)
        Schema::table('student_attendance', function ($table) {
            if (!Schema::hasColumn('student_attendance', 'leave_reason')) {
                $table->string('leave_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('student_attendance', 'is_approved')) {
                $table->boolean('is_approved')->default(false)->after('leave_reason');
            }
        });
    }
}