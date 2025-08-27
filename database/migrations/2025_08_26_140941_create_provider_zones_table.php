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
        Schema::create('provider_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_provider_id')->constrained()->onDelete('cascade');
            $table->string('country_name');
            $table->string('country_code', 3)->index();
            $table->integer('zone');
            $table->timestamps();

            $table->unique(['rate_provider_id', 'country_code', 'zone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_zones');
    }
};
