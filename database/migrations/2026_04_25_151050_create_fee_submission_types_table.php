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
        Schema::create('fee_submission_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // e.g., Tuition Fee, Sports Fee
            $table->enum('period', ['monthly', 'quarterly', 'annually', 'one_time']);
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_submission_types');
    }
};
