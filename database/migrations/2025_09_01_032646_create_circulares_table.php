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
        Schema::create('circulares', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('archivo');
            $table->string('nombre_archivo_original');
            $table->string('tipo_archivo');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('grupo_id');
            $table->string('seccion');
            $table->date('fecha_expiracion')->nullable();
            $table->timestamps();
            
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circulares');
    }
};
