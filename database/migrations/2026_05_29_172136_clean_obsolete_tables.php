<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::dropIfExists('sections');               // the old school sections table
        Schema::dropIfExists('section_subject_frequencies');
        
        // Drop duplicate or conflicting timetable tables
        Schema::dropIfExists('time_tables');            // old timetable (replace with timetable_entries)
        Schema::dropIfExists('class_subject_teacher');  // will be recreated properly
        
        // Drop old student_attendance if it has old foreign keys
        Schema::dropIfExists('student_attendance');
        
        // Drop any other old tables that reference class_id + section
        Schema::dropIfExists('exam_results');
        
        // Keep the new tables? We'll recreate them cleanly
        // So drop them too:
        Schema::dropIfExists('timetable_entries');
        Schema::dropIfExists('subject_assignments');
        Schema::dropIfExists('class_sections');
        Schema::dropIfExists('teacher_availabilities');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('timetable_generation_logs');
        
        // Also drop the old attendances table if it uses employee_id (we have a new one)
        // But we will keep staff attendance as is (attendances table)
        // No need to drop attendances.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
