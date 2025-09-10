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
        Schema::create('origin_transit_mode', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_id')->constrained()->onDelete('cascade');
            $table->foreignId('transit_mode_id')->constrained()->onDelete('cascade');
            $table->integer('days')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('origin_transit_mode');
    }
};
