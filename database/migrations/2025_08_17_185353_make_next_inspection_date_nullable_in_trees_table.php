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
        Schema::table('trees', function (Blueprint $table) {
            // Make next_inspection_date field nullable so plantation drive trees can be created without inspection dates
            $table->date('next_inspection_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            // Revert next_inspection_date field back to not nullable (only if needed)
            $table->date('next_inspection_date')->nullable(false)->change();
        });
    }
};
