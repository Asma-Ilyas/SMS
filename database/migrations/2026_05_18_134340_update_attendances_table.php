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
         Schema::table('attendances', function (Blueprint $table) {
            // Add late and overtime tracking (optional)
            if (!Schema::hasColumn('attendances', 'late_minutes')) {
                $table->integer('late_minutes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('attendances', 'overtime_minutes')) {
                $table->integer('overtime_minutes')->nullable()->after('late_minutes');
            }
            if (!Schema::hasColumn('attendances', 'work_hours')) {
                $table->decimal('work_hours', 5, 2)->nullable()->after('overtime_minutes');
            }
            // Ensure foreign key is correct (staff_id)
            if (Schema::hasColumn('attendances', 'employee_id') && !Schema::hasColumn('attendances', 'staff_id')) {
                $table->renameColumn('employee_id', 'staff_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['late_minutes', 'overtime_minutes', 'work_hours']);
        });
    }
};
