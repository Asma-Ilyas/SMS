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
        Schema::create('salaries', function (Blueprint $table) {
              $table->id();
   $table->foreignId('staff_id')->constrained('staff');
    $table->string('month'); // YYYY-MM
    $table->integer('total_working_days')->nullable(); // days in month
    $table->integer('days_present')->nullable();
    $table->integer('days_absent')->nullable();
    $table->integer('days_late')->nullable();
    $table->decimal('basic_salary', 12, 2);
    $table->decimal('daily_rate', 12, 2);
    $table->decimal('attendance_bonus', 12, 2)->default(0);
    $table->decimal('overtime_pay', 12, 2)->default(0);
    $table->decimal('allowances', 12, 2)->default(0);
    $table->decimal('deductions', 12, 2)->default(0);  // includes absent/late fines
    $table->decimal('tax', 12, 2)->default(0);
    $table->decimal('pf_employee', 12, 2)->default(0);
    $table->decimal('pf_employer', 12, 2)->default(0);
    $table->decimal('net_salary', 12, 2);
    $table->date('payment_date');
    $table->enum('payment_method', ['bank','cash'])->default('bank');
    $table->string('transaction_ref')->nullable();
    $table->timestamps();
    $table->unique(['staff_id','month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
