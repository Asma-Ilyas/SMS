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
        Schema::table('invoices', function (Blueprint $table) {
             $table->decimal('discount_amount', 10, 2)->default(0)->after('amount');
            $table->decimal('net_amount', 10, 2)->storedAs('amount - discount_amount')->after('discount_amount');
            $table->foreignId('discount_id')->nullable()->after('net_amount')->constrained()->nullOnDelete();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
};
