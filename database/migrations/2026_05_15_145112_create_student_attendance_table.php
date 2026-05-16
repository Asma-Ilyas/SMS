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
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->foreignId('class_id')->constrained('classes');
    $table->foreignId('section_id')->constrained('sections');
    $table->foreignId('subject_id')->constrained('subjects'); // the first period subject
    $table->foreignId('teacher_id')->constrained('staff');
    $table->date('date');
    $table->enum('status', ['present', 'absent', 'late', 'half_day']);
    $table->text('remarks')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendance');
    }
};
