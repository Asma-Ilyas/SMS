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
        Schema::create('section_subject_frequencies', function (Blueprint $table) {
           $table->id();
    $table->foreignId('section_id')->constrained('sections');
    $table->foreignId('subject_id')->constrained('subjects');
    $table->integer('periods_per_week'); // e.g., 5 for math
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_subject_frequencies');
    }
};
