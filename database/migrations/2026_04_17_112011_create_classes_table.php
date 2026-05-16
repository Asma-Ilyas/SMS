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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
    $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
    $table->foreignId('grade_id')->constrained()->onDelete('cascade');
    $table->foreignId('stream_id')->constrained()->onDelete('cascade');
    $table->foreignId('elective_track_id')->nullable()->constrained()->onDelete('set null');
    $table->string('section', 10)->nullable();
    $table->unsignedSmallInteger('capacity')->nullable();
    $table->timestamps();

    $table->unique([
        'academic_session_id',
        'grade_id',
        'stream_id',
        'elective_track_id',
        'section'
    ], 'unique_class_combination');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
