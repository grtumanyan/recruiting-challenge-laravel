<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;

class UserTransactionSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_own_transaction_summary()
    {
        Cache::flush();

        $user = User::factory()->create();

        Transaction::factory()->create(['user_id' => $user->id, 'type' => 'earned', 'amount' => 1000]);
        Transaction::factory()->create(['user_id' => $user->id, 'type' => 'spent', 'amount' => 100]);
        Transaction::factory()->create(['user_id' => $user->id, 'type' => 'payout', 'status' => 'requested', 'amount' => 30]);
        Transaction::factory()->create(['user_id' => $user->id, 'type' => 'payout', 'status' => 'approved', 'amount' => 50]);
        Transaction::factory()->create(['user_id' => $user->id, 'type' => 'payout', 'status' => 'paid', 'amount' => 50]);

        $response = $this->actingAs($user)->getJson("/api/v1/users/{$user->id}/summary");

        $response->assertStatus(200)
            ->assertJson([
                'userId' => $user->id,
                'balance' => 850, // 1000 - 100 - 50
                'earned' => 1000,
                'spent' => 100,
                'payout_requested' => 30,
                'payout_approved' => 50,
                'payout_paid' => 50,
            ]);
    }

    public function test_user_summary_cache_expires_after_two_minutes()
    {
        Cache::flush();

        $user = User::factory()->create();

        Transaction::factory()->create([
            'user_id' => $user->id,
            'type' => 'earned',
            'amount' => 100
        ]);

        $this->actingAs($user)->getJson("/api/v1/users/{$user->id}/summary")
            ->assertJson(['earned' => 100]); // Should cache 100

        // Won't count due to cache
        Transaction::factory()->create([
            'user_id' => $user->id,
            'type' => 'earned',
            'amount' => 50
        ]);

        $this->getJson("/api/v1/users/{$user->id}/summary")
            ->assertJson(['earned' => 100]); // Still 100 from cache

        // Time passage
        Carbon::setTestNow(now()->addMinutes(3));

        $this->getJson("/api/v1/users/{$user->id}/summary")
            ->assertJson(['earned' => 150]); // Cache should be expired, and new value should be 150

        Carbon::setTestNow();
    }

    public function test_user_cannot_access_another_users_summary()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1)->getJson("/api/v1/users/{$user2->id}/summary")
            ->assertStatus(403); // Forbidden
    }
}
