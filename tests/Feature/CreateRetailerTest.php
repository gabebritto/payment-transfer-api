<?php

namespace Tests\Feature;

use App\Modules\User\Models\Retailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateRetailerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_retailer_with_wallet()
    {
        $payload = [
            'name' => 'Shop Inc',
            'email' => 'shop@example.com',
            'document' => '12345678000199',
            'phone_number' => '987654321',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/retailers', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'document',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('retailers', [
            'email' => 'shop@example.com',
            'document' => '12345678000199',
        ]);

        $retailer = Retailer::where('email', 'shop@example.com')->first();
        $this->assertNotNull($retailer->wallet);
        $this->assertEquals(0, $retailer->wallet->balance);
    }

    public function test_cannot_create_retailer_with_duplicate_email()
    {
        Retailer::factory()->create(['email' => 'shop@example.com']);

        $payload = [
            'name' => 'Shop Inc',
            'email' => 'shop@example.com',
            'document' => '98765432000100',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/retailers', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_cannot_create_retailer_with_duplicate_document()
    {
        Retailer::factory()->create(['document' => '12345678000199']);

        $payload = [
            'name' => 'Shop Inc',
            'email' => 'other@example.com',
            'document' => '12345678000199',
            'phone_number' => '123456789',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/retailers', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['document']);
    }
}
