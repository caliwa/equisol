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
        Schema::create('weight_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_type_id')->after('id')->constrained()->onDelete('cascade');
            $table->string('label'); // E.g., '<45', '100', '250', '≥1000'
            $table->integer('min_weight')->default(0);
            $table->integer('max_weight');
            $table->integer('display_order')->default(0); // Para ordenar las filas correctamente
            $table->timestamps();

            $table->unique(['service_type_id', 'label'], 'unique_label_per_service_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weight_tiers');
    }
};
