<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PayoutRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_valid_amount()
    {
        $user = User::factory()->create(['id' => 74092]);
        Sanctum::actingAs($user);

        // < 0
        $this->postJson('/api/v1/users/' . $user->id . '/payout', ['amount' => -10 ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);

        // > 100
        $this->postJson('/api/v1/users/' . $user->id . '/payout', [
            'amount' => 200 ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);

        // non-numeric
        $response = $this->postJson('/api/v1/users/' . $user->id . '/payout', [
            'amount' => 'not-a-number'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_it_creates_payout_request()
    {
        $user = User::factory()->create(['id' => 74092]);

        Cache::shouldReceive('get')
            ->with("cache-summary-user-{$user->id}")
            ->andReturn([
                'balance' => 200.0,
            ]);

        Cache::shouldReceive('has')
            ->with("cache-summary-user-{$user->id}")
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->with("cache-summary-user-{$user->id}")
            ->once();

        $response = $this->actingAs($user)->postJson('/api/v1/users/' . $user->id . '/payout', [
            'amount' => 50.0
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Payout request submitted']);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'payout',
            'status' => 'requested',
            'amount' => 50.0
        ]);

        // Cache was invalidated
        Cache::shouldHaveReceived('forget')
            ->with("cache-summary-user-{$user->id}");
    }
}
