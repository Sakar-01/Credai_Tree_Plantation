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
            // Convert existing height values from meters to centimeters
            \DB::statement('UPDATE trees SET height = height * 100 WHERE height IS NOT NULL');
            
            // Modify column to support larger values (up to 99999.9 cm)
            $table->decimal('height', 6, 1)->nullable()->change()->comment('Height in centimeters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            // Convert back to meters
            \DB::statement('UPDATE trees SET height = height / 100 WHERE height IS NOT NULL');
            
            // Revert to original column size
            $table->decimal('height', 5, 2)->nullable()->change()->comment('Height in meters');
        });
    }
};
