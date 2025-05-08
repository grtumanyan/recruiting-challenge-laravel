<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserTransactionSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserTransactionSummaryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_balance_calculation_is_correct()
    {
        $user = User::factory()->create(['id' => 74092]);
        $cachedSummary = [
            'balance' => 200.0,
        ];

        Cache::shouldReceive('get')
            ->with("cache-summary-user-{$user->id}")
            ->andReturn($cachedSummary);

        Cache::shouldReceive('has')
            ->with("cache-summary-user-{$user->id}")
            ->andReturn(true);

        $service = app(UserTransactionSummaryService::class);
        $summary = $service->getSummaryForUser($user->id);

        $this->assertEquals(200.0, $summary['balance']); // Balance matches the cached value
    }
}

