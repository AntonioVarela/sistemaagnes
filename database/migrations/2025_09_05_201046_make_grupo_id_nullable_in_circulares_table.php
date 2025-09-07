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
        Schema::table('circulares', function (Blueprint $table) {
            // Hacer grupo_id nullable para permitir circulares globales
            $table->unsignedBigInteger('grupo_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('circulares', function (Blueprint $table) {
            // Revertir grupo_id a NOT NULL
            $table->unsignedBigInteger('grupo_id')->nullable(false)->change();
        });
    }
};
