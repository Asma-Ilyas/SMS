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
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Default"
            $table->json('grades')->nullable(); // JSON: [{"min":90,"max":100,"grade":"A+"}]
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Insert default grade scale
        DB::table('grade_scales')->insert([
            'name' => 'Default',
            'grades' => json_encode([
                ['min' => 90, 'max' => 100, 'grade' => 'A+'],
                ['min' => 80, 'max' => 89, 'grade' => 'A'],
                ['min' => 70, 'max' => 79, 'grade' => 'B+'],
                ['min' => 60, 'max' => 69, 'grade' => 'B'],
                ['min' => 50, 'max' => 59, 'grade' => 'C'],
                ['min' => 40, 'max' => 49, 'grade' => 'D'],
                ['min' => 0,  'max' => 39, 'grade' => 'F'],
            ]),
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
    }
};
