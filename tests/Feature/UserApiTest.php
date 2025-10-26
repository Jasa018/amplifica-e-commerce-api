<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_can_list_users()
    {
        $user = User::factory()->create();
        User::factory()->count(3)->create();
        
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'name', 'email', 'created_at', 'updated_at']
                    ]
                ]);
    }

    public function test_can_create_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => ['id', 'name', 'email', 'created_at', 'updated_at']
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
    }

    public function test_can_show_user()
    {
        $authUser = User::factory()->create();
        $targetUser = User::factory()->create();
        
        Sanctum::actingAs($authUser);

        $response = $this->getJson("/api/users/{$targetUser->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => ['id', 'name', 'email', 'created_at', 'updated_at']
                ])
                ->assertJson([
                    'data' => [
                        'id' => $targetUser->id,
                        'name' => $targetUser->name,
                        'email' => $targetUser->email
                    ]
                ]);
    }

    public function test_can_update_user()
    {
        $authUser = User::factory()->create();
        $targetUser = User::factory()->create();
        
        Sanctum::actingAs($authUser);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ];

        $response = $this->putJson("/api/users/{$targetUser->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $targetUser->id,
                        'name' => 'Updated Name',
                        'email' => 'updated@example.com'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    public function test_can_delete_user()
    {
        $authUser = User::factory()->create();
        $targetUser = User::factory()->create();
        
        Sanctum::actingAs($authUser);

        $response = $this->deleteJson("/api/users/{$targetUser->id}");

        $response->assertStatus(200)
                ->assertJson(['message' => 'Usuario eliminado exitosamente']);

        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_cannot_delete_own_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(403)
                ->assertJson(['error' => 'No puedes eliminar tu propio usuario']);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/users');
        $response->assertStatus(401);
    }

    public function test_validates_user_creation()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/users', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_validates_unique_email()
    {
        $user = User::factory()->create();
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }
}