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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tree_id')->constrained('trees');
            $table->date('inspection_date');
            $table->string('photo_path');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('tree_height_cm')->nullable();
            $table->enum('tree_health', ['good', 'average', 'poor']);
            $table->text('observation_notes')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->foreignId('inspected_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
