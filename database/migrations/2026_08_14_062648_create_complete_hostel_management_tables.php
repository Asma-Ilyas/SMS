<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Check if staff table exists
        if (!Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('designation')->nullable();
                $table->date('joining_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Check if students table exists
        if (!Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 1. Hostels
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['boys', 'girls', 'mixed'])->default('boys');
            $table->text('address')->nullable();
            $table->foreignId('warden_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->integer('total_capacity')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('code');
            $table->index('type');
            $table->index('is_active');
            $table->index(['is_active', 'type'], 'hostels_active_type_idx');
            $table->index('warden_id');
        });

        // 2. Hostel Staff Assignments
        Schema::create('hostel_staff_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->enum('role', ['warden', 'deputy_warden', 'caretaker', 'security', 'other'])->default('caretaker');
            $table->date('assigned_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['hostel_id', 'staff_id', 'role'], 'hsa_hostel_staff_role_unique');
            $table->index('role');
            $table->index('is_active');
            $table->index(['hostel_id', 'is_active'], 'hsa_hostel_active_idx');
            $table->index(['staff_id', 'role'], 'hsa_staff_role_idx');
        });

        // 3. Hostel Room Types
        Schema::create('hostel_room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('default_capacity')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('name');
        });

        // 4. Hostel Rooms
        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
            $table->foreignId('room_type_id')->nullable()->constrained('hostel_room_types')->onDelete('set null');
            $table->string('room_number');
            $table->string('floor')->nullable();
            $table->integer('capacity')->default(1);
            $table->integer('current_occupancy')->default(0);
            $table->enum('status', ['available', 'full', 'maintenance', 'reserved'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['hostel_id', 'room_number'], 'hostel_room_unique');
            $table->index('room_number');
            $table->index('floor');
            $table->index('status');
            $table->index(['hostel_id', 'status'], 'hostel_rooms_hs_status_idx');
            $table->index(['hostel_id', 'room_type_id'], 'hostel_rooms_hs_type_idx');
            $table->index(['status', 'capacity'], 'hostel_rooms_status_cap_idx');
        });

        // 5. Hostel Fee Types
        Schema::create('hostel_fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('hostel_id')->nullable()->constrained('hostels')->onDelete('set null');
            $table->enum('period', ['monthly', 'quarterly', 'annually', 'semester'])->default('monthly');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('name');
            $table->index('is_active');
            $table->index('period');
            $table->index(['hostel_id', 'is_active'], 'hft_hostel_active_idx');
            $table->index(['period', 'amount'], 'hft_period_amount_idx');
        });

        // 6. Student Hostel Allocations
        Schema::create('student_hostel_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('hostel_rooms')->onDelete('cascade');
            $table->foreignId('hostel_fee_type_id')->nullable()->constrained('hostel_fee_types')->onDelete('set null');
            $table->string('bed_number')->nullable();
            $table->date('allocation_date');
            $table->date('vacate_date')->nullable();
            $table->enum('status', ['active', 'vacated', 'pending'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'status'], 'sha_student_status_unique');
            $table->index(['student_id', 'status'], 'sha_student_status_idx');
            $table->index(['room_id', 'status'], 'sha_room_status_idx');
            $table->index(['hostel_id', 'status'], 'sha_hostel_status_idx');
            $table->index('allocation_date');
            $table->index('vacate_date');
            $table->index('status');
            $table->index('hostel_fee_type_id');
        });

        // 7. Student Hostel Fee Payments
        Schema::create('student_hostel_fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_hostel_allocation_id')->constrained('student_hostel_allocations')->onDelete('cascade');
            $table->string('month');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->string('receipt_number')->nullable()->unique();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['student_hostel_allocation_id', 'month'], 'shfp_allocation_month_unique');
            $table->index('month');
            $table->index('status');
            $table->index('due_date');
            $table->index('payment_date');
            $table->index(['student_hostel_allocation_id', 'status'], 'shfp_allocation_status_idx');
            $table->index(['month', 'status'], 'shfp_month_status_idx');
            $table->index('receipt_number');
        });

        // 8. Hostel Attendance
        Schema::create('hostel_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_hostel_allocation_id')->constrained('student_hostel_allocations')->onDelete('cascade');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'excused', 'leave'])->default('present');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['student_hostel_allocation_id', 'attendance_date'], 'ha_allocation_date_unique');
            $table->index(['attendance_date', 'status'], 'ha_date_status_idx');
            $table->index('status');
            $table->index('attendance_date');
        });

        // 9. Hostel Room Maintenance
        Schema::create('hostel_room_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('hostel_rooms')->onDelete('cascade');
            $table->foreignId('reported_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->string('issue_type');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['reported', 'in_progress', 'completed', 'cancelled'])->default('reported');
            $table->date('reported_date');
            $table->date('completed_date')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('priority');
            $table->index('reported_date');
            $table->index(['room_id', 'status'], 'hrm_room_status_idx');
            $table->index(['status', 'priority'], 'hrm_status_priority_idx');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        Schema::dropIfExists('hostel_room_maintenance');
        Schema::dropIfExists('hostel_attendance');
        Schema::dropIfExists('student_hostel_fee_payments');
        Schema::dropIfExists('student_hostel_allocations');
        Schema::dropIfExists('hostel_fee_types');
        Schema::dropIfExists('hostel_rooms');
        Schema::dropIfExists('hostel_room_types');
        Schema::dropIfExists('hostel_staff_assignments');
        Schema::dropIfExists('hostels');
        
        if (Schema::hasTable('staff')) {
            Schema::dropIfExists('staff');
        }
        
        if (Schema::hasTable('students')) {
            Schema::dropIfExists('students');
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};