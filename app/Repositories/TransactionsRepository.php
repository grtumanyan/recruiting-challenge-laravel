<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionsRepository
{
    public function getTotal(int $userId)
    {
        return DB::table('transactions')
            ->select('type', 'status', DB::raw('SUM(amount) as total'))
            ->where('user_id', $userId)
            ->groupBy('type', 'status')
            ->get();
    }

    public function getTotalRequestedPayoutsPerUser(): Collection
    {
        return DB::table('transactions')
            ->select('user_id as userId', DB::raw('SUM(amount) as total_requested'))
            ->where('type', 'payout')
            ->where('status', 'requested')
            ->groupBy('user_id')
            ->get();
    }
}
