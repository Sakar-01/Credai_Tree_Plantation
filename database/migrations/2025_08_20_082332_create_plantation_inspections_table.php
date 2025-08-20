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
        Schema::create('plantation_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantation_id')->constrained('plantations');
            $table->date('inspection_date');
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->enum('overall_health', ['excellent', 'good', 'average', 'poor', 'critical'])->default('good');
            $table->integer('trees_inspected')->default(0);
            $table->integer('healthy_trees')->default(0);
            $table->integer('unhealthy_trees')->default(0);
            $table->text('recommendations')->nullable();
            $table->foreignId('inspected_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantation_inspections');
    }
};
