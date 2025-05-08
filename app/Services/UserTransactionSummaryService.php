<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\UserPayoutRepository;
use Illuminate\Support\Facades\Cache;
use App\Repositories\TransactionsRepository;
use Illuminate\Validation\ValidationException;

class UserTransactionSummaryService
{
    public const CACHE_SUMMARY_PREFIX = 'cache-summary-user-';

    public function __construct(
        protected TransactionsRepository $transactionsRepository,
        protected UserPayoutRepository $payoutRepository
    ) {
        //
    }


    public function getSummaryForUser(int $userId): array
    {
        $cacheKey = self::CACHE_SUMMARY_PREFIX . $userId;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $totals = $this->transactionsRepository->getTotal($userId);

        $earned = 0;
        $spent = 0;
        $payouts = [
            'requested' => 0,
            'approved' => 0,
            'paid' => 0
        ];

        foreach ($totals as $row) {
            if ($row->type === 'earned') {
                $earned += $row->total;
            } elseif ($row->type === 'spent') {
                $spent += $row->total;
            } elseif ($row->type === 'payout' && isset($payouts[$row->status])) {
                $payouts[$row->status] += $row->total;
            }
        }

        $balance = $earned - $spent - $payouts['approved'];

        $data = [
            'userId' => $userId,
            'balance' => $balance,
            'earned' => $earned,
            'spent' => $spent,
            'payout_requested' => $payouts['requested'],
            'payout_approved' => $payouts['approved'],
            'payout_paid' => $payouts['paid'],
        ];

        Cache::put($cacheKey, $data, now()->addMinutes(2));

        return $data;
    }

    public function requestPayout(int $userId, float $amount): Transaction
    {
        $summary = $this->getSummaryForUser($userId);
        $availableBalance = $summary['balance'];

        if ($amount > $availableBalance) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient balance.'
            ]);
        }

        $transaction = $this->payoutRepository->createRequestedPayout($userId, $amount);

        // Invalidate cached balance
        Cache::forget("cache-summary-user-{$userId}");

        return $transaction;
    }
}

