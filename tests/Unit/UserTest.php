<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'rol' => 'profesor'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'rol' => 'profesor'
        ]);
    }

    public function test_user_has_soft_deletes()
    {
        $user = User::factory()->create();
        
        $this->assertNull($user->deleted_at);
        
        $user->delete();
        
        $this->assertSoftDeleted('users', [
            'id' => $user->id
        ]);
    }

    public function test_user_can_be_restored()
    {
        $user = User::factory()->create();
        $user->delete();
        
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        
        $user->restore();
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null
        ]);
    }

    public function test_user_has_initial_method()
    {
        $user = User::factory()->create([
            'name' => 'Juan Carlos Pérez'
        ]);

        $this->assertEquals('JCP', $user->initials());
    }

    public function test_user_password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => 'plaintext'
        ]);

        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(password_verify('plaintext', $user->password));
    }
}
