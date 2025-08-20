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
            // Make photo_path field nullable so plantation drive trees can be created without individual photos
            $table->string('photo_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            // Revert photo_path field back to not nullable (only if needed)
            $table->string('photo_path')->nullable(false)->change();
        });
    }
};
