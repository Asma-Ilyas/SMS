<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->text('message');
            $table->string('audience', 20);                        // all | admins | teachers | students | section
            $table->unsignedBigInteger('class_section_id')->nullable();
            $table->string('priority', 20)->default('normal');     // normal | important | urgent
            $table->string('link')->nullable();                    // optional in-app path, e.g. /student/fees
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedInteger('recipients_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};