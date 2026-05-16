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
       Schema::create('attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
    $table->date('date');
    $table->time('check_in')->nullable();
    $table->time('check_out')->nullable();
    $table->time('late_duration')->nullable();
    $table->time('overtime')->nullable();
    $table->enum('status', ['present','absent','late','half_day','holiday','leave'])->default('present');
    $table->boolean('is_approved')->default(true);
    $table->text('remarks')->nullable();
    $table->timestamps();
    $table->unique(['staff_id','date']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
