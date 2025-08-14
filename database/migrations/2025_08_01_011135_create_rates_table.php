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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_type_id')->constrained()->onDelete('cascade');
            
            // ACTUALIZACIÓN: La moneda ahora es opcional (nullable) para servicios como 'pickup'
            // que no tienen una moneda asociada directamente en la tabla de tarifas.
            $table->foreignId('currency_id')->nullable()->constrained('currencies_master')->onDelete('set null');
            
            // La tarifa mínima pertenece aquí, ya que es específica del servicio.
            $table->decimal('minimum_charge', 10, 2);

            $table->timestamps();

            // Asegura que no haya servicios duplicados (misma combinación de origen, tipo y moneda).
            // La restricción única funciona con valores nulos.
            $table->unique(['origin_id', 'service_type_id'], 'service_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
