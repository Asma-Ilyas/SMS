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
        Schema::create('timetable_generation_logs', function (Blueprint $table) {
            $table->id();
    $table->dateTime('generated_at');
    $table->integer('total_entries')->nullable();
    $table->integer('conflicts_resolved')->default(0);
    $table->json('errors')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_generation_logs');
    }
};
