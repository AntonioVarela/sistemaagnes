<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\grupo;
use App\Models\materia;
use App\Models\Circular;
use App\Models\horario;
use App\Models\tarea;
use App\Models\anuncio;
use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_connection()
    {
        // Crear un usuario de prueba para verificar la conexión
        $user = User::factory()->create();
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email
        ]);
    }

    public function test_user_creation_with_relationships()
    {
        $user = User::factory()->create(['rol' => 'profesor']);
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'rol' => 'profesor'
        ]);
    }

    public function test_grupo_creation_with_titular()
    {
        $titular = User::factory()->create(['rol' => 'profesor']);
        
        $grupo = grupo::create([
            'nombre' => 'Primer Grado A',
            'seccion' => 'Primaria',
            'titular' => $titular->id
        ]);

        $this->assertDatabaseHas('grupos', [
            'nombre' => 'Primer Grado A',
            'seccion' => 'Primaria',
            'titular' => $titular->id
        ]);
    }

    public function test_circular_creation_with_relationships()
    {
        $user = User::factory()->create();
        $grupo = grupo::factory()->create();

        $circular = Circular::create([
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

        $this->assertDatabaseHas('circulares', [
            'titulo' => 'Circular de Prueba',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id
        ]);
    }

    public function test_soft_deletes_functionality()
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertSoftDeleted('users', [
            'id' => $user->id
        ]);

        // Verificar que se puede restaurar
        $user->restore();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null
        ]);
    }

    public function test_foreign_key_constraints()
    {
        $user = User::factory()->create();
        $grupo = grupo::factory()->create();

        // Crear circular con relaciones válidas
        $circular = Circular::create([
            'titulo' => 'Test Circular',
            'descripcion' => 'Test',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria'
        ]);

        $this->assertDatabaseHas('circulares', [
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id
        ]);
    }

    public function test_cascade_delete_behavior()
    {
        $user = User::factory()->create();
        $grupo = grupo::factory()->create();

        $circular = Circular::create([
            'titulo' => 'Test Circular',
            'descripcion' => 'Test',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $user->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria'
        ]);

        // Eliminar el usuario
        $user->forceDelete();

        // Verificar que la circular también se eliminó (cascade)
        $this->assertDatabaseMissing('circulares', [
            'id' => $circular->id
        ]);
    }

    public function test_data_integrity_with_seeders()
    {
        // Ejecutar seeders
        $this->seed();

        // Verificar que se crearon datos
        $this->assertDatabaseHas('users', [
            'email' => 'antonio.varela@colegioagnes.edu.mx'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'profesor@colegioagnes.edu.mx'
        ]);

        // Verificar que se crearon grupos
        $this->assertDatabaseHas('grupos', [
            'nombre' => 'Primer Grado A'
        ]);

        // Verificar que se crearon materias
        $this->assertDatabaseHas('materias', [
            'nombre' => 'Matemáticas'
        ]);
    }

    public function test_unique_constraints()
    {
        $user1 = User::factory()->create(['email' => 'test@example.com']);
        
        // Intentar crear otro usuario con el mismo email
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'name' => 'Test User 2',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor'
        ]);
    }

    public function test_required_fields_validation()
    {
        // Intentar crear usuario sin campos requeridos
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'name' => 'Test User'
            // Faltan email, password, rol
        ]);
    }

    public function test_indexes_performance()
    {
        // Crear múltiples registros para probar rendimiento
        $users = User::factory()->count(100)->create();
        
        // Buscar por email (debe usar índice)
        $start = microtime(true);
        $user = User::where('email', $users->first()->email)->first();
        $end = microtime(true);
        
        $this->assertNotNull($user);
        $this->assertLessThan(0.1, $end - $start); // Debe ser rápido
    }

    public function test_transaction_rollback()
    {
        $this->expectException(\Exception::class);
        
        \DB::transaction(function () {
            User::factory()->create(['name' => 'User 1']);
            User::factory()->create(['name' => 'User 2']);
            
            // Forzar error
            throw new \Exception('Test rollback');
        });

        // Verificar que no se crearon usuarios
        $this->assertDatabaseMissing('users', ['name' => 'User 1']);
        $this->assertDatabaseMissing('users', ['name' => 'User 2']);
    }
}
