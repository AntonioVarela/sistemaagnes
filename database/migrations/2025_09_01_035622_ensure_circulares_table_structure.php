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
        if (!Schema::hasTable('circulares')) {
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
                $table->softDeletes();
                
                $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
            });
        } else {
            // Verificar y agregar columnas faltantes
            Schema::table('circulares', function (Blueprint $table) {
                if (!Schema::hasColumn('circulares', 'nombre_archivo_original')) {
                    $table->string('nombre_archivo_original')->after('archivo');
                }
                if (!Schema::hasColumn('circulares', 'tipo_archivo')) {
                    $table->string('tipo_archivo')->after('nombre_archivo_original');
                }
                if (!Schema::hasColumn('circulares', 'usuario_id')) {
                    $table->unsignedBigInteger('usuario_id')->after('tipo_archivo');
                    $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
                }
                if (!Schema::hasColumn('circulares', 'grupo_id')) {
                    $table->unsignedBigInteger('grupo_id')->after('usuario_id');
                    $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
                }
                if (!Schema::hasColumn('circulares', 'seccion')) {
                    $table->string('seccion')->after('grupo_id');
                }
                if (!Schema::hasColumn('circulares', 'fecha_expiracion')) {
                    $table->date('fecha_expiracion')->nullable()->after('seccion');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circulares');
    }
};
