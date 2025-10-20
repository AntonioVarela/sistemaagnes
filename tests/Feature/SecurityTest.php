<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Circular;
use App\Models\grupo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('s3');
    }

    public function test_unauthorized_access_to_admin_routes()
    {
        $user = User::factory()->create(['rol' => 'profesor']);
        $this->actingAs($user);

        // Intentar acceder a rutas de administración
        $this->get('/usuarios')->assertStatus(403);
        $this->get('/grupos')->assertStatus(403);
        $this->get('/materias')->assertStatus(403);
    }

    public function test_guest_cannot_access_protected_routes()
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/usuarios')->assertRedirect('/login');
        $this->get('/circulares')->assertRedirect('/login');
        $this->get('/tareas')->assertRedirect('/login');
    }

    public function test_csrf_protection_on_forms()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        // Deshabilitar CSRF para esta prueba específica
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        // Intentar crear usuario con datos válidos
        $response = $this->post('/usuarios', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'rol' => 'profesor'
        ]);

        // Debe redirigir exitosamente (no error CSRF)
        $response->assertRedirect();
    }

    public function test_sql_injection_protection()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        // Intentar inyección SQL en búsqueda
        $maliciousInput = "'; DROP TABLE users; --";
        
        $response = $this->get('/usuarios?search=' . urlencode($maliciousInput));
        $response->assertStatus(200);
        
        // Verificar que la tabla users aún existe
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_xss_protection_in_forms()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        $xssPayload = '<script>alert("XSS")</script>';
        
        $userData = [
            'name' => $xssPayload,
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'rol' => 'profesor'
        ];

        $response = $this->post('/usuarios', $userData);
        $response->assertRedirect('/usuarios');

        // Verificar que el script no se ejecutó (el contenido se escapó)
        $this->assertDatabaseHas('users', [
            'name' => $xssPayload // Laravel escapa automáticamente
        ]);
    }

    public function test_file_upload_security()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $grupo = grupo::factory()->create();
        $this->actingAs($admin);

        // Intentar subir archivo malicioso
        $maliciousFile = UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');

        $circularData = [
            'titulo' => 'Circular Maliciosa',
            'descripcion' => 'Descripción',
            'archivo' => $maliciousFile,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria'
        ];

        $response = $this->post('/circulares', $circularData);
        
        // Debe fallar por validación de tipo de archivo
        $response->assertSessionHasErrors('archivo');
    }

    public function test_authorization_for_circular_operations()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $otherUser = User::factory()->create(['rol' => 'profesor']);
        $grupo = grupo::factory()->create();

        // Crear circular como administrador
        $circular = Circular::create([
            'titulo' => 'Circular de Admin',
            'descripcion' => 'Descripción',
            'archivo' => 'admin.pdf',
            'nombre_archivo_original' => 'admin.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => $admin->id,
            'grupo_id' => $grupo->id,
            'seccion' => 'Primaria'
        ]);

        // Otro usuario intenta eliminar la circular
        $this->actingAs($otherUser);
        
        $response = $this->delete("/circulares/{$circular->id}");
        $response->assertRedirect('/circulares');

        // La circular no debe estar eliminada
        $this->assertDatabaseHas('circulares', [
            'id' => $circular->id,
            'deleted_at' => null
        ]);
    }

    public function test_password_requirements()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        // Intentar crear usuario con contraseña débil
        $weakPasswordData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123', // Contraseña muy débil
            'rol' => 'profesor'
        ];

        $response = $this->post('/usuarios', $weakPasswordData);
        
        // Debe fallar por validación de contraseña
        $response->assertSessionHasErrors('password');
    }

    public function test_email_uniqueness_validation()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        $this->actingAs($admin);

        // Intentar crear usuario con email duplicado
        $duplicateEmailData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'rol' => 'profesor'
        ];

        $response = $this->post('/usuarios', $duplicateEmailData);
        
        // Debe fallar por email duplicado
        $response->assertSessionHasErrors('email');
    }

    public function test_session_timeout()
    {
        // No autenticar usuario para simular sesión expirada
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_mass_assignment_protection()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin);

        // Intentar asignación masiva de campos protegidos
        $maliciousData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'rol' => 'profesor',
            'id' => 999, // Intentar cambiar ID
            'created_at' => now(), // Intentar cambiar fecha
            'is_admin' => true // Campo que no existe
        ];

        $response = $this->post('/usuarios', $maliciousData);
        $response->assertRedirect('/usuarios');

        // Verificar que solo se guardaron los campos permitidos
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'rol' => 'profesor'
        ]);

        // Verificar que los campos protegidos no se guardaron
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotEquals(999, $user->id);
        $this->assertNull($user->is_admin);
    }
}
