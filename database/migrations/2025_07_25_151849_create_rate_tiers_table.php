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
        Schema::create('rate_tiers', function (Blueprint $table) {
            $table->id();
            // Relación con la tabla de orígenes
            $table->foreignId('origin_id')->constrained()->onDelete('cascade');
            $table->integer('max_weight'); // Límite superior del peso (e.g., 1000, 2000)
            $table->decimal('rate_per_kg', 10, 2); // Tarifa por KG para ese rango
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_tiers');
    }
};