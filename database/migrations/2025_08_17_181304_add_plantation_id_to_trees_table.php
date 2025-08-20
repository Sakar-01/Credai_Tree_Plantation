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
            $table->unsignedBigInteger('plantation_id')->nullable()->after('id');
            $table->foreign('plantation_id')->references('id')->on('plantations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->dropForeign(['plantation_id']);
            $table->dropColumn('plantation_id');
        });
    }
};
