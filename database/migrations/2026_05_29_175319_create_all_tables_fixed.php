<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // =============================================
        // 1. Core independent tables
        // =============================================
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('section_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->foreignId('section_type_id')->constrained('section_types')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('position')->nullable();
            $table->string('alignment')->nullable();
            $table->json('style')->nullable();
            $table->string('background_color')->nullable();
            $table->string('text_color')->nullable();
            $table->timestamps();
        });

        Schema::create('section_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_type_id')->constrained('section_types')->cascadeOnDelete();
            $table->string('name');
            $table->string('field_type');
            $table->json('options')->nullable();
            $table->timestamps();
        });

        Schema::create('section_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('cms_sections')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('section_fields')->cascadeOnDelete();
            $table->longText('value')->nullable();
            $table->timestamps();
            $table->unique(['section_id', 'field_id'], 'sec_field_val_unique');
        });

        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('email');
            $table->string('phone');
            $table->date('dob')->nullable();
            $table->string('city_campus')->nullable();
            $table->string('class')->nullable();
            $table->timestamps();
        });

        // =============================================
        // 1a. AUTHENTICATION TABLES
        // =============================================
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // =============================================
        // 2. Academic core
        // =============================================
        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10)->unique();
            $table->unsignedTinyInteger('numeric_value');
            $table->timestamps();
        });

        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->unique();
            $table->timestamps();
        });

        Schema::create('elective_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stream_id')->constrained('streams')->onDelete('cascade');
            $table->string('name', 50);
            $table->timestamps();
            $table->unique(['stream_id', 'name'], 'elec_tracks_unique');
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->foreignId('grade_id')->constrained('grades')->onDelete('cascade');
            $table->foreignId('stream_id')->constrained('streams')->onDelete('cascade');
            $table->foreignId('elective_track_id')->nullable()->constrained('elective_tracks')->onDelete('set null');
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->timestamps();
            $table->unique(['academic_session_id', 'grade_id', 'stream_id', 'elective_track_id'], 'class_comb_unique');
        });

        // =============================================
        // 3. Class sections
        // =============================================
        Schema::create('class_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('section_name');
            $table->integer('capacity')->nullable();
            $table->integer('student_count')->default(0);
            $table->integer('student_strength')->default(0);
            $table->timestamps();
            $table->unique(['class_id', 'section_name'], 'class_sections_unique');
        });

        // =============================================
        // 4. Subjects and staff
        // =============================================
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['theory', 'practical', 'elective'])->default('theory');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->time('arrival_time')->nullable();
            $table->time('departure_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('cnic')->unique();
            $table->string('passport_number')->nullable()->unique();
            $table->string('nationality')->default('Pakistani');
            $table->string('religion')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Pakistan');
            $table->string('employee_id')->unique();
            $table->string('designation')->nullable();
            $table->string('department')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern', 'visiting'])->default('full_time');
            $table->date('joining_date')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->date('resignation_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->text('qualification')->nullable();
            $table->text('experience')->nullable();
            $table->string('specialization')->nullable();
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->enum('salary_type', ['monthly', 'daily', 'hourly'])->default('monthly');
            $table->decimal('hourly_rate', 12, 2)->default(0);
            $table->decimal('daily_rate', 12, 2)->default(0);
            $table->boolean('attendance_required')->default(true);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('medical_allowance', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('pf_percentage', 5, 2)->default(5);
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_iban')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('cnic_front_image')->nullable();
            $table->string('cnic_back_image')->nullable();
            $table->string('resume')->nullable();
            $table->string('degree_certificate')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->integer('max_periods_per_day')->default(6);
            $table->integer('max_periods_per_week')->default(30);
            $table->foreignId('category_id')->nullable()->constrained('employee_categories')->onDelete('set null');
            $table->boolean('is_teacher')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('preference_level')->default(1);
            $table->timestamps();
            $table->unique(['teacher_id', 'subject_id'], 'teacher_subj_unique');
        });

        // =============================================
        // 5. Blocks & Floors
        // =============================================
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->integer('level')->default(0);
            $table->foreignId('block_id')->nullable()->constrained('blocks')->nullOnDelete();
            $table->timestamps();
        });

        // =============================================
        // 6. Rooms & time management
        // =============================================
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('room_number')->unique();
            $table->enum('type', ['classroom', 'lab', 'library', 'auditorium', 'activity', 'other'])->default('classroom');
            $table->integer('capacity');
            $table->boolean('has_projector')->default(false);
            $table->boolean('has_ac')->default(false);
            $table->boolean('is_available')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('block_id')->nullable()->constrained('blocks')->nullOnDelete();
            $table->foreignId('floor_id')->nullable()->constrained('floors')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('school_timings', function (Blueprint $table) {
            $table->id();
            $table->string('session_name');
            $table->date('session_start');
            $table->date('session_end');
            $table->time('school_start');
            $table->time('school_end');
            $table->integer('period_duration')->default(45);
            $table->json('breaks')->nullable();
            $table->boolean('has_activity')->default(false);
            $table->string('activity_label')->nullable();
            $table->time('activity_start')->nullable();
            $table->time('activity_end')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_timing_id')->nullable()->constrained('school_timings')->onDelete('cascade');
            $table->integer('sort_order');
            $table->string('label');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('type', ['period', 'break', 'activity'])->default('period');
            $table->integer('period_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['school_timing_id', 'sort_order'], 'ts_school_sort_idx');
        });

        // =============================================
        // 6a. TEACHER AVAILABILITIES
        // =============================================
        Schema::create('teacher_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('staff')->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('cascade');
            $table->boolean('is_available')->default(true);
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->unique(['teacher_id', 'day_of_week', 'time_slot_id'], 'teacher_avail_unique');
        });

        // =============================================
        // 7. SECTION ROOM ASSIGNMENTS
        // =============================================
        Schema::create('section_room_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('class_sections')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('school_timing_id')->constrained('school_timings')->onDelete('cascade');
            $table->string('assignment_type')->default('classroom');
            $table->integer('section_strength')->nullable();
            $table->integer('room_capacity')->nullable();
            $table->enum('fit_status', ['perfect', 'comfortable', 'tight', 'over_capacity'])->default('perfect');
            $table->foreignId('suggested_room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('section_id', 'sra_section_idx');
            $table->index('room_id', 'sra_room_idx');
            $table->index('school_timing_id', 'sra_timing_idx');
            $table->index('fit_status', 'sra_fit_idx');
            $table->unique(['section_id', 'room_id', 'school_timing_id'], 'sra_unique');
        });

        // =============================================
        // 8. Subject assignments
        // =============================================
        Schema::create('subject_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->integer('weekly_frequency')->default(5);
            $table->boolean('is_elective')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['class_section_id', 'subject_id'], 'subj_assign_unique');
        });

        // =============================================
        // 9. Timetable entries
        // =============================================
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('teacher_id')->constrained('staff');
            $table->foreignId('room_id')->constrained('rooms');
            $table->foreignId('time_slot_id')->constrained('time_slots');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->timestamps();

            $table->unique(['teacher_id', 'day_of_week', 'time_slot_id'], 'tt_teacher_slot');
            $table->unique(['room_id', 'day_of_week', 'time_slot_id'], 'tt_room_slot');
            $table->unique(['class_section_id', 'day_of_week', 'time_slot_id'], 'tt_class_section_slot');
        });

        Schema::create('timetable_generation_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('generated_at');
            $table->integer('total_entries')->nullable();
            $table->integer('conflicts_resolved')->default(0);
            $table->json('errors')->nullable();
            $table->timestamps();
        });

        // =============================================
        // 10. Students
        // =============================================
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();          
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['Male', 'Female']);
            $table->date('date_of_birth');
            $table->string('dob_in_words')->nullable();
            $table->string('religion')->nullable();
            $table->string('caste_subcaste')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->text('extra_note')->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('previous_school_name')->nullable();
            $table->string('previous_school_address')->nullable();
            $table->string('previous_class')->nullable();
            $table->string('passout_year')->nullable();
            $table->string('previous_category')->nullable();
            $table->date('admission_date');
            $table->string('student_type');
            $table->string('admission_number')->unique();
            $table->string('roll_number')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('father_name');
            $table->string('father_phone')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('parent_id_proof')->nullable();
            $table->string('parent_signature')->nullable();
            $table->string('assigned_concession')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->date('suspension_start_date')->nullable();
            $table->date('suspension_end_date')->nullable();
            $table->text('suspension_message')->nullable();
            $table->foreignId('class_section_id')->nullable()->constrained('class_sections')->onDelete('set null');
            $table->timestamps();
        });

        // =============================================
        // 11. STUDENT ATTENDANCE
        // =============================================
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('teacher_id')->constrained('staff');
            $table->date('date');
            $table->enum('status', ['present','absent','late','half_day','leave','holiday','on_duty'])->default('present');
            $table->enum('half_day_type', ['morning','afternoon','custom'])->nullable();
            $table->time('half_day_in_time')->nullable();
            $table->time('half_day_out_time')->nullable();
            $table->text('half_day_reason')->nullable();
            $table->time('late_arrival_time')->nullable();
            $table->integer('late_minutes')->default(0);
            $table->text('late_reason')->nullable();
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->text('remarks')->nullable();
            $table->string('leave_reason')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamps();
            $table->unique(['student_id', 'date', 'subject_id'], 'student_att_unique');
            $table->index(['date', 'status'], 'sa_date_status_idx');
            $table->index(['student_id', 'date'], 'sa_student_date_idx');
            $table->index(['class_section_id', 'date'], 'sa_section_date_idx');
        });

        // =============================================
        // 12. STUDENT ATTENDANCE SUMMARY
        // =============================================
        Schema::create('student_attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('month');
            $table->integer('total_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->integer('half_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->integer('holiday_days')->default(0);
            $table->integer('on_duty_days')->default(0);
            $table->decimal('attendance_percentage', 5, 2)->default(0);
            $table->json('half_day_reasons')->nullable();
            $table->json('late_reasons')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'month'], 'student_att_summary_unique');
            $table->index(['month'], 'sas_month_idx');
        });

        // =============================================
        // 13. ATTENDANCE REASONS
        // =============================================
        Schema::create('attendance_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('reason');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['category', 'is_active'], 'ar_cat_active_idx');
        });

        // Insert attendance reasons
        $reasons = [
            ['category' => 'half_day', 'reason' => 'Medical Appointment', 'sort_order' => 1],
            ['category' => 'half_day', 'reason' => 'Family Emergency', 'sort_order' => 2],
            ['category' => 'half_day', 'reason' => 'Personal Work', 'sort_order' => 3],
            ['category' => 'half_day', 'reason' => 'Sick', 'sort_order' => 4],
            ['category' => 'half_day', 'reason' => 'Exam/Test', 'sort_order' => 5],
            ['category' => 'half_day', 'reason' => 'Religious Event', 'sort_order' => 6],
            ['category' => 'half_day', 'reason' => 'Sports/Activity', 'sort_order' => 7],
            ['category' => 'half_day', 'reason' => 'School Event', 'sort_order' => 8],
            ['category' => 'half_day', 'reason' => 'Travel', 'sort_order' => 9],
            ['category' => 'half_day', 'reason' => 'Other', 'sort_order' => 10],
            ['category' => 'late', 'reason' => 'Traffic', 'sort_order' => 1],
            ['category' => 'late', 'reason' => 'Transportation Delay', 'sort_order' => 2],
            ['category' => 'late', 'reason' => 'Slept Late', 'sort_order' => 3],
            ['category' => 'late', 'reason' => 'Medical Emergency', 'sort_order' => 4],
            ['category' => 'late', 'reason' => 'Family Emergency', 'sort_order' => 5],
            ['category' => 'late', 'reason' => 'Weather', 'sort_order' => 6],
            ['category' => 'late', 'reason' => 'Vehicle Issue', 'sort_order' => 7],
            ['category' => 'late', 'reason' => 'Heavy Rain', 'sort_order' => 8],
            ['category' => 'late', 'reason' => 'Public Transport Strike', 'sort_order' => 9],
            ['category' => 'late', 'reason' => 'Other', 'sort_order' => 10],
            ['category' => 'absent', 'reason' => 'Sick', 'sort_order' => 1],
            ['category' => 'absent', 'reason' => 'Family Emergency', 'sort_order' => 2],
            ['category' => 'absent', 'reason' => 'Travel', 'sort_order' => 3],
            ['category' => 'absent', 'reason' => 'Unwell', 'sort_order' => 4],
        ];

        foreach ($reasons as $reason) {
            DB::table('attendance_reasons')->insert([
                'category' => $reason['category'],
                'reason' => $reason['reason'],
                'description' => $reason['reason'],
                'is_active' => true,
                'sort_order' => $reason['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // =============================================
        // 14. ATTENDANCE TEACHER SECTION (PERMISSIONS)
        // =============================================
        Schema::create('attendance_teacher_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->boolean('can_mark_attendance')->default(true);
            $table->boolean('can_approve_leave')->default(false);
            $table->foreignId('assigned_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamps();
            $table->unique(['teacher_id', 'class_section_id', 'subject_id'], 'att_teacher_section_unique');
        });

        // =============================================
        // 15. COMMON SUBJECT CLASSES
        // =============================================
        Schema::create('common_subject_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->json('class_section_ids')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // =============================================
        // 16. Fee management
        // =============================================
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fee_submission_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('period', ['monthly', 'quarterly', 'annually', 'one_time']);
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_fee_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('fee_submission_type_id')->constrained('fee_submission_types')->onDelete('restrict');
            $table->integer('installment_number');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('receipt_number')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'fee_submission_type_id', 'installment_number'], 'fee_installment_unique');
        });

        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('branch_name');
            $table->string('account_title');
            $table->string('account_number');
            $table->string('iban')->nullable();
            $table->string('routing_number')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->foreignId('student_fee_submission_id')->nullable()->constrained('student_fee_submissions')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->storedAs('amount - discount_amount');
            $table->date('due_date');
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->string('challan_file')->nullable();
            $table->string('payment_proof_file')->nullable();
            $table->text('payment_remarks')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('student_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('discount_id')->constrained('discounts')->onDelete('cascade');
            $table->foreignId('fee_submission_type_id')->nullable()->constrained('fee_submission_types')->onDelete('cascade');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['bank', 'mobile_wallet', 'cash']);
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->string('account_number')->nullable();
            $table->string('qr_code_url')->nullable();
            $table->string('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // =============================================
        // 17. Exam management
        // =============================================
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('exam_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_type_id')->constrained('exam_types')->onDelete('cascade');
            $table->foreignId('exam_group_id')->nullable()->constrained('exam_groups')->onDelete('set null');
            $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->string('exam_center')->nullable();
            $table->string('time_table')->nullable();
            $table->integer('passing_percentage')->default(40);
            $table->timestamps();
        });

        Schema::create('exam_subject_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->integer('max_marks')->default(100);
            $table->integer('passing_marks')->default(40);
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['exam_id', 'sort_order'], 'ess_exam_order_idx');
        });

        Schema::create('exam_subject_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('marks_obtained')->nullable();
            $table->integer('max_marks')->default(100);
            $table->integer('passing_marks')->default(40);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['exam_id', 'student_id', 'subject_id'], 'exam_subject_marks_unique');
            $table->index(['exam_id', 'student_id'], 'esm_exam_student_idx');
        });

        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('marks_obtained')->nullable();
            $table->integer('max_marks')->nullable();
            $table->integer('passing_marks')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['exam_id', 'student_id', 'subject_id'], 'exam_marks_unique');
        });

        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->decimal('total_max_marks', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->string('remarks')->nullable();
            $table->integer('rank_in_class')->nullable();
            $table->timestamps();
            $table->unique(['exam_id', 'student_id'], 'exam_results_unique');
        });

        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->json('grades')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        DB::table('grade_scales')->insert([
            'name' => 'Default',
            'grades' => json_encode([
                ['min' => 90, 'max' => 100, 'grade' => 'A+'],
                ['min' => 80, 'max' => 89, 'grade' => 'A'],
                ['min' => 70, 'max' => 79, 'grade' => 'B+'],
                ['min' => 60, 'max' => 69, 'grade' => 'B'],
                ['min' => 50, 'max' => 59, 'grade' => 'C'],
                ['min' => 40, 'max' => 49, 'grade' => 'D'],
                ['min' => 0,  'max' => 39, 'grade' => 'F'],
            ]),
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // =============================================
        // 18. STAFF ATTENDANCE
        // =============================================
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present','absent','late','half_day','holiday','leave','on_duty','weekend','training'])->default('present');
            $table->enum('half_day_type', ['morning','afternoon','custom'])->nullable();
            $table->time('half_day_in_time')->nullable();
            $table->time('half_day_out_time')->nullable();
            $table->text('half_day_reason')->nullable();
            $table->time('late_arrival_time')->nullable();
            $table->integer('late_minutes')->nullable();
            $table->text('late_reason')->nullable();
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->time('arrival_time')->nullable();
            $table->time('departure_time')->nullable();
            $table->integer('overtime_minutes')->nullable();
            $table->decimal('work_hours', 5, 2)->nullable();
            $table->integer('total_minutes')->nullable();
            $table->foreignId('class_section_id')->nullable()->constrained('class_sections')->onDelete('set null');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->foreignId('time_slot_id')->nullable()->constrained('time_slots')->onDelete('set null');
            $table->integer('duration_minutes')->nullable();
            $table->boolean('class_taken')->default(false);
            $table->text('remarks')->nullable();
            $table->text('early_departure_reason')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            $table->unique(['staff_id', 'date'], 'staff_att_unique');
            $table->index(['date', 'status'], 'sa_date_status_idx2');
            $table->index(['staff_id', 'date'], 'sa_staff_date_idx');
        });

        // =============================================
        // 19. Remaining tables
        // =============================================
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type', ['sick', 'casual', 'annual']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason');
            $table->timestamps();
        });

        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('month');
            $table->integer('total_working_days')->nullable();
            $table->integer('days_present')->nullable();
            $table->integer('days_absent')->nullable();
            $table->integer('days_late')->nullable();
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('daily_rate', 12, 2);
            $table->decimal('attendance_bonus', 12, 2)->default(0);
            $table->decimal('overtime_pay', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('commission', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('penalties', 12, 2)->default(0);
            $table->decimal('leave_deductions', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('pf_employee', 12, 2)->default(0);
            $table->decimal('pf_employer', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2);
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['bank', 'cash', 'cheque'])->default('bank');
            $table->enum('payment_status', ['paid', 'pending'])->default('pending');
            $table->string('transaction_ref')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['staff_id', 'month'], 'salaries_unique');
        });

        Schema::create('salary_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('medical_allowance', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('pf_percentage', 5, 2)->default(5);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert salary templates
        DB::table('salary_templates')->insert([
            [
                'name' => 'Teacher Basic',
                'description' => 'Basic salary template for teachers',
                'basic_salary' => 50000,
                'allowances' => 10000,
                'medical_allowance' => 5000,
                'transport_allowance' => 3000,
                'pf_percentage' => 5.00,
                'tax_percentage' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Senior Teacher',
                'description' => 'Salary template for senior teachers',
                'basic_salary' => 75000,
                'allowances' => 15000,
                'medical_allowance' => 7000,
                'transport_allowance' => 5000,
                'pf_percentage' => 5.00,
                'tax_percentage' => 2.5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin Staff',
                'description' => 'Salary template for administrative staff',
                'basic_salary' => 40000,
                'allowances' => 8000,
                'medical_allowance' => 4000,
                'transport_allowance' => 2000,
                'pf_percentage' => 5.00,
                'tax_percentage' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Support Staff',
                'description' => 'Salary template for support staff',
                'basic_salary' => 25000,
                'allowances' => 5000,
                'medical_allowance' => 2000,
                'transport_allowance' => 1500,
                'pf_percentage' => 0,
                'tax_percentage' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('month');
            $table->integer('total_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->integer('holiday_days')->default(0);
            $table->integer('classes_taken')->default(0);
            $table->integer('total_classes')->default(0);
            $table->decimal('attendance_percentage', 5, 2)->default(0);
            $table->decimal('class_taken_percentage', 5, 2)->default(0);
            $table->integer('total_hours')->default(0);
            $table->integer('overtime_hours')->default(0);
            $table->timestamps();
            $table->unique(['staff_id', 'month'], 'att_summary_unique');
        });

        Schema::create('teacher_class_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'cancelled', 'substitute'])->default('present');
            $table->boolean('class_taken')->default(false);
            $table->integer('duration_minutes')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('substitute_teacher_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamps();
            $table->unique(['teacher_id', 'class_section_id', 'subject_id', 'time_slot_id', 'date'], 'teacher_class_att_unique');
        });

        Schema::create('payroll_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->string('month');
            $table->date('run_date');
            $table->enum('status', ['draft', 'processed', 'paid'])->default('draft');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->integer('total_employees')->default(0);
            $table->foreignId('processed_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_batch_id')->constrained('payroll_batches')->onDelete('cascade');
            $table->foreignId('salary_id')->constrained('salaries')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'processed', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['payroll_batch_id', 'salary_id'], 'payroll_item_unique');
        });

        // =============================================
        // 20. Transfers and certificates
        // =============================================
        Schema::create('student_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('transfer_type', ['outgoing', 'incoming']);
            $table->string('from_school')->nullable();
            $table->string('to_school')->nullable();
            $table->date('transfer_date');
            $table->text('reason')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });

        Schema::create('transfer_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->json('certificate_types');
            $table->enum('student_status_after', ['active', 'inactive'])->default('inactive');
            $table->text('remarks')->nullable();
            $table->date('issued_date');
            $table->timestamps();
        });

        Schema::create('certificate_types', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->text('description')->nullable();
            $table->string('template_file')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('certificate_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_type_id')->constrained('certificate_types')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('issue_date');
            $table->text('remarks')->nullable();
            $table->string('certificate_file')->nullable();
            $table->timestamps();
        });

        // =============================================
        // 21. Teacher availability logs
        // =============================================
        Schema::create('teacher_availability_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('staff')->onDelete('cascade');
            $table->date('date');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('cascade');
            $table->boolean('is_available')->default(true);
            $table->text('reason')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamps();
            
            // FIXED: Shorter index names
            $table->index(['teacher_id', 'date'], 'tal_teacher_date_idx');
            $table->index(['teacher_id', 'day_of_week', 'time_slot_id'], 'tal_teacher_day_slot_idx');
        });

        // =============================================
        // 22. Fee Reports
        // =============================================
        Schema::create('fee_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('month');
            $table->decimal('total_fee', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);
            $table->enum('status', ['paid', 'partial', 'unpaid'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'month'], 'fee_report_unique');
            $table->index(['month', 'status'], 'fr_month_status_idx');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Drop tables in reverse order
        Schema::dropIfExists('fee_reports');
        Schema::dropIfExists('teacher_availability_logs');
        Schema::dropIfExists('certificate_distributions');
        Schema::dropIfExists('certificate_types');
        Schema::dropIfExists('transfer_certificates');
        Schema::dropIfExists('student_transfers');
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_batches');
        Schema::dropIfExists('teacher_class_attendance');
        Schema::dropIfExists('attendance_summaries');
        Schema::dropIfExists('salary_templates');
        Schema::dropIfExists('salaries');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('staff_attendances');
        Schema::dropIfExists('grade_scales');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exam_subject_marks');
        Schema::dropIfExists('exam_subject_schedules');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_groups');
        Schema::dropIfExists('exam_types');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('student_discounts');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('banks');
        Schema::dropIfExists('student_fee_submissions');
        Schema::dropIfExists('fee_submission_types');
        Schema::dropIfExists('fee_types');
        Schema::dropIfExists('common_subject_classes');
        Schema::dropIfExists('attendance_teacher_section');
        Schema::dropIfExists('attendance_reasons');
        Schema::dropIfExists('student_attendance_summaries');
        Schema::dropIfExists('student_attendance');
        Schema::dropIfExists('students');
        Schema::dropIfExists('timetable_generation_logs');
        Schema::dropIfExists('timetable_entries');
        Schema::dropIfExists('subject_assignments');
        Schema::dropIfExists('section_room_assignments');
        Schema::dropIfExists('teacher_availabilities');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('school_timings');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('floors');
        Schema::dropIfExists('blocks');
        Schema::dropIfExists('teacher_subjects');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('employee_categories');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('class_sections');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('elective_tracks');
        Schema::dropIfExists('streams');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('academic_sessions');
        Schema::dropIfExists('admission_applications');
        Schema::dropIfExists('section_field_values');
        Schema::dropIfExists('section_fields');
        Schema::dropIfExists('cms_sections');
        Schema::dropIfExists('section_types');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('schools');
        
        // Auth tables (added above academic core)
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};