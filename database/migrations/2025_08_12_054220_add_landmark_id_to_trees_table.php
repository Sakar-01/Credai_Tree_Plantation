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
            $table->unsignedBigInteger('landmark_id')->nullable()->after('location_id');
            $table->foreign('landmark_id')->references('id')->on('landmarks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->dropForeign(['landmark_id']);
            $table->dropColumn('landmark_id');
        });
    }
};
