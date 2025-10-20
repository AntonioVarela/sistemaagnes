<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar storage fake para pruebas
        Storage::fake('s3');
        
        // Configurar base de datos MySQL para pruebas
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => 'sistema_agnes_test']);
    }

    /**
     * Helper para crear usuario administrador
     */
    protected function createAdmin()
    {
        return \App\Models\User::factory()->create([
            'rol' => 'administrador',
            'email' => 'admin@test.com'
        ]);
    }

    /**
     * Helper para crear usuario profesor
     */
    protected function createProfesor()
    {
        return \App\Models\User::factory()->create([
            'rol' => 'profesor',
            'email' => 'profesor@test.com'
        ]);
    }

    /**
     * Helper para crear grupo de prueba
     */
    protected function createGrupo($titular = null)
    {
        if (!$titular) {
            $titular = $this->createProfesor();
        }

        return \App\Models\grupo::factory()->create([
            'titular' => $titular->id
        ]);
    }

    /**
     * Helper para crear circular de prueba
     */
    protected function createCircular($user = null, $grupo = null)
    {
        if (!$user) {
            $user = $this->createAdmin();
        }
        if (!$grupo) {
            $grupo = $this->createGrupo();
        }

        return \App\Models\Circular::create([
            'titulo' => 'Circular de Prueba',
            'descripcion' => 'Descripción de prueba',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria',
            'es_global' => false
        ]);
    }
}