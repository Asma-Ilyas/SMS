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
        Schema::table('exam_marks_if_missing', function (Blueprint $table) {
              if (!Schema::hasColumn('exam_marks', 'passing_marks')) {
                $table->integer('passing_marks')->nullable()->after('max_marks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_marks_if_missing', function (Blueprint $table) {
          $table->dropColumn('passing_marks');
        });
    }
};
