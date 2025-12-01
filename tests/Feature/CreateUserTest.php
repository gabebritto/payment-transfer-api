<?php

namespace Tests\Feature;

use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user_with_wallet()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'document' => '12345678901',
            'phone_number' => '123456789',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'document',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'document' => '12345678901',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user->wallet);
        $this->assertEquals(0, $user->wallet->balance);
    }

    public function test_cannot_create_user_with_duplicate_email()
    {
        User::factory()->create(['email' => 'john@example.com']);

        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'document' => '98765432100',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_cannot_create_user_with_duplicate_document()
    {
        User::factory()->create(['document' => '12345678901']);

        $payload = [
            'name' => 'John Doe',
            'email' => 'jane@example.com',
            'document' => '12345678901',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['document']);
    }
}
