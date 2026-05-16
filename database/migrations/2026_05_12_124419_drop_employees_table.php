<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('employees');
    }

    public function down(): void
    {
        // Recreate employees table if you ever rollback
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
    $table->string('employee_id')->unique(); // manual or auto
    $table->string('name');
    $table->string('email')->unique();
    $table->string('phone');
    $table->date('hire_date');
    $table->decimal('basic_salary', 12, 2);
    $table->string('position');
    $table->string('department');
    $table->string('bank_account')->nullable();
    $table->string('pan_number')->nullable();
    $table->string('pf_number')->nullable();
    $table->timestamps();
            // add any other columns you originally had
        });
    }
};