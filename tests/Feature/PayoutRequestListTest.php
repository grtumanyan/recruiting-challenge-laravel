<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PayoutRequestListTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_total_requested_payouts_per_user()
    {
        $admin = User::factory()->create(); // Admin user

        $user1 = User::factory()->create(['id' => 74092]);
        $user2 = User::factory()->create(['id' => 823019]);

        // Transactions for user1
        Transaction::factory()->create([
            'user_id' => $user1->id,
            'type' => 'payout',
            'status' => 'requested',
            'amount' => 30.0
        ]);

        Transaction::factory()->create([
            'user_id' => $user1->id,
            'type' => 'payout',
            'status' => 'requested',
            'amount' => 19.5
        ]);

        // Transactions for user2
        Transaction::factory()->create([
            'user_id' => $user2->id,
            'type' => 'payout',
            'status' => 'requested',
            'amount' => 15.0
        ]);

        // Non-requested payouts (should be ignored!)
        Transaction::factory()->create([
            'user_id' => $user1->id,
            'type' => 'payout',
            'status' => 'approved',
            'amount' => 999.0
        ]);

        $response = $this->actingAs($admin)->getJson('/api/v1/payouts/requests');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'userId' => $user1->id,
                'total_requested' => 49.5
            ])
            ->assertJsonFragment([
                'userId' => $user2->id,
                'total_requested' => 15.0
            ])
            ->assertJsonCount(2);
    }
}
