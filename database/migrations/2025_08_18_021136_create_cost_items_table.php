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
            
            $table->foreignId('service_type_id')->constrained()->onDelete('cascade');
            $table->string('stage'); 
            $table->string('concept');             

            $table->foreignId('currency_id')->nullable()->constrained('currencies_master');

            $table->string('formula_notes')->nullable();
            $table->json('formula')->nullable();
            
            $table->timestamps();

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
