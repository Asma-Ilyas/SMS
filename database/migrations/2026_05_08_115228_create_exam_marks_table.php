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
        Schema::create('exam_marks', function (Blueprint $table) {
           $table->id();
    $table->foreignId('exam_id')->constrained()->onDelete('cascade');
    $table->foreignId('student_id')->constrained()->onDelete('cascade');
    $table->foreignId('subject_id')->constrained()->onDelete('cascade');
    $table->integer('marks_obtained')->nullable();
    $table->integer('max_marks')->nullable();   // can be per‑subject (e.g., 50, 100)
    $table->integer('passing_marks')->nullable(); // 33% of max if not set
    $table->text('remarks')->nullable();
    $table->timestamps();

    $table->unique(['exam_id', 'student_id', 'subject_id'], 'unique_exam_student_subject');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
    }
};
