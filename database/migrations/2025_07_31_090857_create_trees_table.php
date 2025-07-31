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
        Schema::create('trees', function (Blueprint $table) {
            $table->id();
            $table->string('tree_id')->unique();
            $table->string('species');
            $table->string('location_description');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->date('plantation_date');
            $table->date('next_inspection_date');
            $table->string('photo_path');
            $table->text('description')->nullable();
            $table->string('plantation_survey_file')->nullable();
            $table->foreignId('planted_by')->constrained('users');
            $table->enum('status', ['planted', 'under_inspection', 'healthy', 'needs_attention', 'dead'])->default('planted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trees');
    }
};
