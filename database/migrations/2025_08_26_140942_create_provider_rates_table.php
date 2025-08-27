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
        Schema::create('provider_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_provider_id')->constrained()->onDelete('cascade');
            $table->decimal('weight_kg', 8, 2);
            $table->integer('zone');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_rates');
    }
};
