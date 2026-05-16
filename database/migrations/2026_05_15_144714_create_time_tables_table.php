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
        Schema::create('time_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->tinyInteger('day_of_week')->unsigned(); // 1=Monday, 2=Tuesday ... 7=Sunday
            $table->integer('period_number'); // 1, 2, 3 ...
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('teacher_id')->constrained('staff');
            $table->timestamps();

            // Unique: one subject per period per class-section per day
            $table->unique(['class_id', 'day_of_week', 'period_number'], 'unique_timetable_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_tables');
    }
};
