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
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('descripcion');
            $table->date('fecha_entrega');
            $table->time('hora_entrega');
            $table->string('archivo')->nullable();
            $table->bigInteger('grupo')->unsigned(); // Corregido
            $table->bigInteger('materia')->unsigned(); // Corregido
            $table->foreign('grupo')->references('id')->on('grupos')->onDelete('cascade');
            $table->foreign('materia')->references('id')->on('materias')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
