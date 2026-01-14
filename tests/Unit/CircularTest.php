<?php

namespace Tests\Unit;

use App\Models\Circular;
use App\Models\User;
use App\Models\grupo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CircularTest extends TestCase
{
    use RefreshDatabase;

    public function test_circular_can_be_created()
    {
        $user = User::factory()->create();
        $grupo = grupo::factory()->create();

        $circular = Circular::create([
            'titulo' => 'Circular de Prueba',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria',
            'es_global' => false
        ]);

        $this->assertDatabaseHas('circulares', [
            'titulo' => 'Circular de Prueba',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id
        ]);
    }

    public function test_circular_has_soft_deletes()
    {
        $user = User::factory()->create();
        $grupo = grupo::factory()->create();

        $circular = Circular::create([
            'titulo' => 'Test Circular',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria'
        ]);

        $circular->delete();
        
        $this->assertSoftDeleted('circulares', [
            'id' => $circular->id
        ]);
    }

    public function test_circular_belongs_to_user()
    {
        $user = User::factory()->create();
        $grupo = grupo::factory()->create();

        $circular = Circular::create([
            'titulo' => 'Test Circular',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria'
        ]);

        $this->assertInstanceOf(User::class, $circular->user);
        $this->assertEquals($user->id, $circular->user->id);
    }

    public function test_circular_belongs_to_grupo()
    {
        $user = User::factory()->create();
        $grupo = grupo::factory()->create();

        $circular = Circular::create([
            'titulo' => 'Test Circular',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria'
        ]);

        $this->assertInstanceOf(grupo::class, $circular->grupo);
        $this->assertEquals($grupo->id, $circular->grupo->id);
    }

    public function test_circular_scope_activas()
    {
        $user = User::factory()->create();
        $grupo = grupo::factory()->create();

        // Circular sin expiración
        $circular1 = Circular::create([
            'titulo' => 'Circular Sin Expiración',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria',
            'fecha_expiracion' => null
        ]);

        // Circular con expiración futura
        $circular2 = Circular::create([
            'titulo' => 'Circular Futura',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria',
            'fecha_expiracion' => now()->addDays(7)
        ]);

        // Circular expirada
        $circular3 = Circular::create([
            'titulo' => 'Circular Expirada',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria',
            'fecha_expiracion' => now()->subDays(1)
        ]);

        $activas = Circular::activas()->get();

        $this->assertCount(2, $activas);
        $this->assertTrue($activas->contains($circular1));
        $this->assertTrue($activas->contains($circular2));
        $this->assertFalse($activas->contains($circular3));
    }
}
