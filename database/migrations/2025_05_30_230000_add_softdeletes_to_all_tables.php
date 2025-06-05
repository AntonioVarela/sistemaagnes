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
        Schema::table('materias', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('grupos', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('horarios', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('anuncios', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('tareas', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('anuncios', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}; 