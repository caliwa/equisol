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
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropPrimary('role_has_permissions_permission_id_role_id_primary');

            $table->id()->first();

            $table->unique(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropUnique(['permission_id', 'role_id']);
            $table->dropColumn('id');
            $table->primary(['permission_id', 'role_id']);
        });
    }
};