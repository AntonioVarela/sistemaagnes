<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\grupo;
use App\Models\materia;
use App\Models\Circular;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdministradorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('s3');
    }

    public function test_administrador_can_access_dashboard()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_profesor_can_access_dashboard()
    {
        $profesor = User::factory()->create(['rol' => 'profesor']);
        $this->actingAs($profesor);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_administrador_can_view_usuarios()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        $response = $this->get('/usuarios');
        $response->assertStatus(200);
    }

    public function test_administrador_can_create_usuario()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        $userData = [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'Password123!',
            'rol' => 'profesor'
        ];

        $response = $this->post('/usuarios', $userData);
        $response->assertRedirect('/usuarios');

        $this->assertDatabaseHas('users', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'rol' => 'profesor'
        ]);
    }

    public function test_administrador_can_update_usuario()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        $user = User::factory()->create(['rol' => 'profesor']);

        $updateData = [
            'name' => 'Usuario Actualizado',
            'email' => $user->email,
            'rol' => 'profesor'
        ];

        $response = $this->put("/usuarios/{$user->id}", $updateData);
        $response->assertRedirect('/usuarios');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Usuario Actualizado'
        ]);
    }

    public function test_administrador_can_delete_usuario()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        $user = User::factory()->create(['rol' => 'profesor']);

        $response = $this->delete("/usuarios/{$user->id}");
        $response->assertRedirect('/usuarios');

        $this->assertSoftDeleted('users', [
            'id' => $user->id
        ]);
    }

    public function test_user_cannot_delete_themselves()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        $response = $this->delete("/usuarios/{$admin->id}");
        $response->assertRedirect('/usuarios');

        // El usuario no debe estar eliminado
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'deleted_at' => null
        ]);
    }

    public function test_administrador_can_view_circulares()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        $response = $this->get('/circulares');
        $response->assertStatus(200);
    }

    public function test_administrador_can_create_circular()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $grupo = grupo::factory()->create();
        $this->actingAs($admin);

        $file = UploadedFile::fake()->create('circular.pdf', 100);

        $circularData = [
            'titulo' => 'Circular de Prueba',
            'descripcion' => 'Descripción de prueba',
            'archivo' => $file,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria',
            'fecha_expiracion' => now()->addDays(7)->format('Y-m-d')
        ];

        $response = $this->post('/circulares', $circularData);
        $response->assertRedirect('/circulares');

        $this->assertDatabaseHas('circulares', [
            'titulo' => 'Circular de Prueba',
            'usuario_id' => $admin->id
        ]);
    }

    public function test_administrador_can_update_circular()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $grupo = grupo::factory()->create();
        $this->actingAs($admin);

        $circular = Circular::create([
            'titulo' => 'Circular Original',
            'descripcion' => 'Descripción original',
            'archivo' => 'original.pdf',
            'nombre_archivo_original' => 'original.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $admin->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria'
        ]);

        $updateData = [
            'titulo' => 'Circular Actualizada',
            'descripcion' => 'Descripción actualizada',
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria'
        ];

        $response = $this->put("/circulares/{$circular->id}", $updateData);
        $response->assertRedirect('/circulares');

        $this->assertDatabaseHas('circulares', [
            'id' => $circular->id,
            'titulo' => 'Circular Actualizada'
        ]);
    }

    public function test_administrador_can_delete_circular()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $grupo = grupo::factory()->create();
        $this->actingAs($admin);

        $circular = Circular::create([
            'titulo' => 'Circular a Eliminar',
            'descripcion' => 'Descripción',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $admin->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria'
        ]);

        $response = $this->delete("/circulares/{$circular->id}");
        $response->assertRedirect('/circulares');

        $this->assertSoftDeleted('circulares', [
            'id' => $circular->id
        ]);
    }

    public function test_user_cannot_delete_other_users_circular()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $otherUser = User::factory()->create(['rol' => 'profesor']);
        $grupo = grupo::factory()->create();
        $this->actingAs($admin);

        $circular = Circular::create([
            'titulo' => 'Circular de Otro Usuario',
            'descripcion' => 'Descripción',
            'archivo' => 'test.pdf',
            'nombre_archivo_original' => 'test.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $otherUser->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria'
        ]);

        // Cambiar a un tercer usuario que no es el creador ni administrador
        $thirdUser = User::factory()->create(['rol' => 'profesor']);
        $this->actingAs($thirdUser);

        $response = $this->delete("/circulares/{$circular->id}");
        $response->assertRedirect('/circulares');

        // La circular no debe estar eliminada
        $this->assertDatabaseHas('circulares', [
            'id' => $circular->id,
            'deleted_at' => null
        ]);
    }
}
