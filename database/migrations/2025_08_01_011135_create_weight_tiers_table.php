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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('weight_tier_id')->constrained();
            
            // Esta es la tarifa por unidad (kg, lb, etc.)
            $table->decimal('rate_value', 10, 3);

            $table->timestamps();

            // Cada servicio solo puede tener una tarifa por nivel de peso
            $table->unique(['service_id', 'weight_tier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
