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
        Schema::create('school_timings', function (Blueprint $table) {
          $table->id();
            $table->string('session_name');          // e.g. "Spring 2025", "Quarter 1"
            $table->date('session_start');
            $table->date('session_end');
            $table->time('school_start');            // e.g. 08:00
            $table->time('school_end');              // e.g. 14:00
            $table->integer('period_duration')->default(45); // minutes
            $table->json('breaks');
            // breaks JSON format:
            // [
            //   {"label":"Short Break","start":"10:15","end":"10:30"},
            //   {"label":"Lunch Break","start":"12:30","end":"13:00"}
            // ]
            $table->boolean('has_activity')->default(false);
            $table->string('activity_label')->nullable(); // e.g. "Assembly", "Sports"
            $table->time('activity_start')->nullable();
            $table->time('activity_end')->nullable();
            $table->boolean('is_active')->default(false); // only one active at a time
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_timings');
    }
};
