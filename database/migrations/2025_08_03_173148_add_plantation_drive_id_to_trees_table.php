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
            $table->unsignedBigInteger('plantation_drive_id')->nullable()->after('location_id');
            $table->string('height')->nullable();
            $table->text('tree_description')->nullable();
            $table->foreign('plantation_drive_id')->references('id')->on('plantation_drives')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->dropForeign(['plantation_drive_id']);
            $table->dropColumn(['plantation_drive_id', 'height', 'tree_description']);
        });
    }
};
