<?php

namespace App\Http\Controllers;

use App\Services\UserTransactionSummaryService;
use Illuminate\Http\JsonResponse;

class FetchUserTransactionSummaryController extends Controller
{

    public function __construct(protected UserTransactionSummaryService $summaryService)
    {
        //
    }

    public function getUserTransactionSummary(int $userId): JsonResponse
    {
        if (auth()->id() !== $userId) {
            abort(403, 'UNAUTHORIZED');
        }

        $data = $this->summaryService->getSummaryForUser($userId);

        return response()->json($data);
    }
}
