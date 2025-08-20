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
        Schema::table('plantations', function (Blueprint $table) {
            // Add missing columns that the Plantation model expects
            if (!Schema::hasColumn('plantations', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable()->after('id');
                $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('plantations', 'landmark_id')) {
                $table->unsignedBigInteger('landmark_id')->nullable()->after('location_id');
                $table->foreign('landmark_id')->references('id')->on('landmarks')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('plantations', 'landmark')) {
                $table->string('landmark')->nullable()->after('landmark_id');
            }
            
            if (!Schema::hasColumn('plantations', 'tree_count')) {
                $table->integer('tree_count')->default(0)->after('plantation_date');
            }
            
            if (!Schema::hasColumn('plantations', 'images')) {
                $table->json('images')->nullable()->after('description');
            }
            
            // Drop photo_path if it exists (replaced by images)
            if (Schema::hasColumn('plantations', 'photo_path')) {
                $table->dropColumn('photo_path');
            }
            
            // Drop next_inspection_date if it exists (moved to individual trees)
            if (Schema::hasColumn('plantations', 'next_inspection_date')) {
                $table->dropColumn('next_inspection_date');
            }
            
            // Drop plantation_survey_file if it exists (not needed for drives)
            if (Schema::hasColumn('plantations', 'plantation_survey_file')) {
                $table->dropColumn('plantation_survey_file');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plantations', function (Blueprint $table) {
            // Remove added columns
            if (Schema::hasColumn('plantations', 'images')) {
                $table->dropColumn('images');
            }
            
            if (Schema::hasColumn('plantations', 'tree_count')) {
                $table->dropColumn('tree_count');
            }
            
            if (Schema::hasColumn('plantations', 'landmark')) {
                $table->dropColumn('landmark');
            }
            
            if (Schema::hasColumn('plantations', 'landmark_id')) {
                $table->dropForeign(['landmark_id']);
                $table->dropColumn('landmark_id');
            }
            
            if (Schema::hasColumn('plantations', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            }
            
            // Add back old columns if needed
            $table->string('photo_path')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->string('plantation_survey_file')->nullable();
        });
    }
};
