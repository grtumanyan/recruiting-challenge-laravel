<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayoutRequest;
use App\Repositories\TransactionsRepository;
use App\Repositories\UserPayoutRepository;
use App\Services\UserTransactionSummaryService;
use Illuminate\Http\JsonResponse;

class PayoutController extends Controller
{
    public function __construct(
        protected UserTransactionSummaryService $summaryService,
        protected TransactionsRepository $transactionsRepository,
        protected UserPayoutRepository $userPayoutRepository
    ) {
        //
    }

    public function requestPayout(PayoutRequest $request, int $userId): JsonResponse
    {
        if (auth()->id() !== $userId) {
            abort(403, 'UNAUTHORIZED');
        }

        $validated = $request->validated();

        $transaction = $this->summaryService->requestPayout($userId, $validated['amount']);

        return response()->json([
            'message' => 'Payout request submitted',
            'transaction_id' => $transaction->id
        ], 201);
    }

    public function listRequestedPayouts(): JsonResponse
    {
        $payouts = $this->transactionsRepository->getTotalRequestedPayoutsPerUser();
        return response()->json($payouts);
    }

    public function approvePayout(string $id): JsonResponse
    {
        $payout = $this->userPayoutRepository->approvePayout($id);

        return response()->json([
            'message' => 'Payout approved successfully',
            'transaction_id' => $payout->uuid,
            'status' => $payout->status,
        ]);
    }
}
