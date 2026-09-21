<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

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

        // 1. Drivers
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cnic')->unique();
            $table->string('license_number')->unique();
            $table->date('license_expiry')->nullable();
            $table->string('phone');
            $table->string('emergency_contact')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->string('license_document')->nullable();
            $table->string('cnic_document')->nullable();
            $table->date('joining_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('is_active');
            $table->index('phone');
            $table->index(['is_active', 'name'], 'drivers_active_name_idx');
        });

        // 2. Vehicles
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number')->unique();
            $table->enum('type', ['bus', 'van', 'coaster', 'car'])->default('bus');
            $table->string('model')->nullable();
            $table->string('manufacturer')->nullable();
            $table->year('manufacture_year')->nullable();
            $table->unsignedSmallInteger('seating_capacity');
            $table->string('registration_number')->nullable();
            $table->date('registration_expiry')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('fitness_expiry')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->string('photo')->nullable();
            $table->string('registration_document')->nullable();
            $table->string('insurance_document')->nullable();
            $table->string('fitness_document')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('type');
            $table->index(['vehicle_number', 'status'], 'vehicles_num_status_idx');
            $table->index('driver_id');
            $table->index(['status', 'type'], 'vehicles_status_type_idx');
        });

        // 3. Transport Routes
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('start_point')->nullable();
            $table->string('end_point')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->integer('estimated_time_minutes')->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('is_active');
            $table->index('code');
            $table->index('vehicle_id');
            $table->index('driver_id');
            $table->index(['is_active', 'code'], 'routes_active_code_idx');
        });

        // 4. Route Stops
        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('transport_routes')->onDelete('cascade');
            $table->string('stop_name');
            $table->integer('stop_order')->default(0);
            $table->time('pickup_time')->nullable();
            $table->time('drop_time')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
            
            $table->index(['route_id', 'stop_order'], 'rstops_route_order_idx');
            $table->index('stop_name');
            $table->index(['route_id', 'stop_name'], 'rstops_route_name_idx');
        });

        // 5. Transport Fee Types
        Schema::create('transport_fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('route_id')->nullable()->constrained('transport_routes')->onDelete('set null');
            $table->enum('period', ['monthly', 'quarterly', 'annually'])->default('monthly');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('route_id');
            $table->index(['is_active', 'period'], 'tft_active_period_idx');
        });

        // 6. Student Transport Assignments
        Schema::create('student_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('transport_routes')->onDelete('cascade');
            $table->foreignId('route_stop_id')->nullable()->constrained('route_stops')->onDelete('set null');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->foreignId('transport_fee_type_id')->nullable()->constrained('transport_fee_types')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'route_id', 'start_date'], 'st_transport_unique');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->index(['student_id', 'status'], 'st_student_status_idx');
            $table->index(['route_id', 'status'], 'st_route_status_idx');
            $table->index('transport_fee_type_id');
            $table->index('vehicle_id');
        });

        // 7. Student Transport Fee Payments
        Schema::create('student_transport_fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_transport_id')->constrained('student_transports')->onDelete('cascade');
            $table->string('month');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->string('receipt_number')->nullable()->unique();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['student_transport_id', 'month'], 'stf_month_unique');
            $table->index('status');
            $table->index('month');
            $table->index('due_date');
            $table->index('payment_date');
            $table->index(['student_transport_id', 'status'], 'stf_transport_status_idx');
            $table->index(['month', 'status'], 'stf_month_status_idx');
        });

        // 8. Vehicle Trip Logs
        Schema::create('vehicle_trip_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('route_id')->nullable()->constrained('transport_routes')->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->date('trip_date');
            $table->enum('trip_type', ['pickup', 'drop', 'both'])->default('both');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('start_latitude', 10, 7)->nullable();
            $table->decimal('start_longitude', 10, 7)->nullable();
            $table->decimal('end_latitude', 10, 7)->nullable();
            $table->decimal('end_longitude', 10, 7)->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['vehicle_id', 'trip_date'], 'vtl_vehicle_date_idx');
            $table->index('status');
            $table->index('trip_type');
            $table->index('trip_date');
            $table->index(['route_id', 'trip_date'], 'vtl_route_date_idx');
            $table->index(['driver_id', 'trip_date'], 'vtl_driver_date_idx');
            $table->index(['status', 'trip_date'], 'vtl_status_date_idx');
        });

        // 9. Vehicle Maintenance Logs
        Schema::create('vehicle_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->date('maintenance_date');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->date('next_due_date')->nullable();
            $table->timestamps();
            
            $table->index(['vehicle_id', 'maintenance_date'], 'vml_vehicle_date_idx');
            $table->index('maintenance_date');
            $table->index('type');
            $table->index(['type', 'maintenance_date'], 'vml_type_date_idx');
        });

        // 10. Vehicle Fuel Logs
        Schema::create('vehicle_fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->date('fuel_date');
            $table->decimal('liters', 10, 2);
            $table->decimal('cost_per_liter', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->integer('odometer_reading')->nullable();
            $table->string('station_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['vehicle_id', 'fuel_date'], 'vfl_vehicle_date_idx');
            $table->index('fuel_date');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        Schema::dropIfExists('vehicle_fuel_logs');
        Schema::dropIfExists('vehicle_maintenance_logs');
        Schema::dropIfExists('vehicle_trip_logs');
        Schema::dropIfExists('student_transport_fee_payments');
        Schema::dropIfExists('student_transports');
        Schema::dropIfExists('transport_fee_types');
        Schema::dropIfExists('route_stops');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('drivers');
        
        if (Schema::hasTable('students')) {
            Schema::dropIfExists('students');
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};