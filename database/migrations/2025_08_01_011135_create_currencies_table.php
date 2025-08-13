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
        Schema::create('currencies_master', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // E.g., 'USD', 'EUR'
            $table->string('name'); // E.g., 'US Dollar', 'Euro'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies_master');
    }
};
