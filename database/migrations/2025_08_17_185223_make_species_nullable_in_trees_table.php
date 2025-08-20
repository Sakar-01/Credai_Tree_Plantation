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
            // Make species field nullable so plantation drive trees can be created without species
            $table->string('species')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            // Revert species field back to not nullable (only if needed)
            $table->string('species')->nullable(false)->change();
        });
    }
};
