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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('grupo')->unsigned();
            $table->bigInteger('materia')->unsigned();
            $table->string('dias');
            $table->string('hora_inicio');
            $table->string('hora_fin');           
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
        Schema::dropIfExists('horarios');
    }
};
