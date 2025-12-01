<?php

namespace Tests\Feature;

use App\Modules\User\Models\User;
use App\Modules\Wallet\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_wallet()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['owner_id' => $user->id, 'owner_type' => User::class]);

        $response = $this->getJson("/api/wallet/{$wallet->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $wallet->id,
                'balance' => $wallet->balance,
            ]);
    }

    public function test_cache_is_created_on_view()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['owner_id' => $user->id, 'owner_type' => User::class]);

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with("wallet_{$wallet->id}", \Closure::class)
            ->andReturn($wallet);

        $this->getJson("/api/wallet/{$wallet->id}");
    }

    public function test_cache_is_invalidated_on_balance_update()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['owner_id' => $user->id, 'owner_type' => User::class]);

        // Simulate cache creation
        Cache::put("wallet_{$wallet->id}", $wallet);

        // Update balance
        $wallet->balance += 100;
        $wallet->save();

        // Assert cache is missing
        $this->assertFalse(Cache::has("wallet_{$wallet->id}"));
    }
}
