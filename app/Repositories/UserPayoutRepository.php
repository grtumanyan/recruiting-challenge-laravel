<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;

class UserPayoutRepository
{
    public function createRequestedPayout(int $userId, float $amount): Transaction
    {
        return Transaction::create([
            'user_id' => $userId,
            'type' => 'payout',
            'status' => 'requested',
            'amount' => $amount
        ]);
    }

    public function approvePayout($id): Transaction
    {
        $payout = Transaction::where('id', $id)
            ->where('type', 'payout')
            ->where('status', 'requested')
            ->firstOrFail();

        $payout->status = 'approved';
        $payout->save();

        // Invalidate user balance cache
        Cache::forget("cache-summary-user-{$payout->user_id}");

        return $payout;
    }
}
