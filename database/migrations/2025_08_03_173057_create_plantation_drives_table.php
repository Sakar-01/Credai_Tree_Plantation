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
        Schema::create('plantation_drives', function (Blueprint $table) {
            $table->id();
            $table->string('drive_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('location_id');
            $table->integer('number_of_trees');
            $table->json('images')->nullable();
            $table->date('plantation_date');
            $table->date('next_inspection_date');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('plantation_survey_file')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('completed');
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantation_drives');
    }
};
