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
        Schema::create('class_subject_teacher', function (Blueprint $table) {
           $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('academic_session_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('max_weekly_periods')->default(0);
            $table->enum('term', ['first', 'second', 'third', 'full_year'])->default('full_year');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['class_id', 'subject_id', 'teacher_id', 'academic_session_id', 'term'], 'unique_assign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subject_teacher');
    }
};
