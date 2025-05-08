<?php

namespace Tests\Feature;

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Illuminate\Support\Str;

class ApprovePayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_a_payout()
    {
        // Admin approval logic is not implemented.
        // This can be handled by checking the user's role or admin flag.
        // For instance, you can use an `is_admin` boolean field or a role-based system
        $admin = User::factory()->create();
        $user = User::factory()->create();

        $payout = Transaction::create([
            'user_id' => $user->id,
            'type' => 'payout',
            'status' => 'requested',
            'amount' => 50.0,
            'uuid' => (string) Str::uuid(),
        ]);

        Cache::shouldReceive('forget')
            ->with("cache-summary-user-{$user->id}")
            ->once();

        $response = $this->actingAs($admin)->patchJson("/api/v1/payouts/{$payout->id}/approve");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Payout approved successfully',
                'transaction_id' => $payout->uuid,
                'status' => 'approved',
            ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $payout->id,
            'status' => 'approved',
        ]);
    }
}
