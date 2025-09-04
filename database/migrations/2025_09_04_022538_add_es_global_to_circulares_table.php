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
            $table->boolean('es_global')->default(false)->after('seccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('circulares', function (Blueprint $table) {
            $table->dropColumn('es_global');
        });
    }
};
