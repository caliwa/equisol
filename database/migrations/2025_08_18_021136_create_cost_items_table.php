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
        Schema::create('cost_items', function (Blueprint $table) {
            $table->id();
            
            // Se vincula al maestro general, como 'Costos de Importación'
            $table->foreignId('service_type_id')->constrained()->onDelete('cascade');

            // Columna 'Gasto' en Excel: 'Origen' o 'Destino'
            $table->enum('stage', ['Origen', 'Destino']); 
            
            // Columna 'Concepto' en Excel: 'Traslado', 'Aduana Origen', etc.
            $table->string('concept'); 
            
            // Columnas para los montos
            $table->decimal('fixed_amount', 12, 2)->nullable();   // Monto Fijo
            $table->decimal('variable_rate', 8, 4)->nullable(); // Monto Variable (ej. 0.25% se guarda como 0.0025)
            $table->decimal('minimum_charge', 12, 2)->nullable(); // Mínima

            // Columna 'Moneda'
            $table->foreignId('currency_id')->nullable()->constrained('currencies_master');

            // Para manejar las fórmulas complejas
            // Aquí definimos el TIPO de cálculo que se debe aplicar.
            $table->enum('calculation_type', [
                'FIXED', // Solo usa el monto fijo
                'VARIABLE_ON_CIF', // Usa el monto variable sobre el valor CIF
                'WEIGHT_BASED', // El costo depende del peso (lógica especial)
                'CUSTOM_ARANCEL' // Arancel manual
            ])->default('FIXED');

            // Columna 'Formula' en Excel: una nota para el usuario
            $table->string('formula_notes')->nullable(); 
            
            $table->timestamps();

            // Asegura que no haya conceptos duplicados para el mismo tipo de servicio y etapa
            $table->unique(['service_type_id', 'stage', 'concept']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_items');
    }
};
