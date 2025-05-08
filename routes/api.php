<?php

use App\Http\Controllers\FetchUserTransactionSummaryController;
use App\Http\Controllers\PayoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/v1'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/users/{id}/summary', [FetchUserTransactionSummaryController::class, 'getUserTransactionSummary']);
        Route::post('/users/{id}/payout', [PayoutController::class, 'requestPayout']);
        Route::patch('/payouts/{id}/approve', [PayoutController::class, 'approvePayout']);
        Route::get('/payouts/requests', [PayoutController::class, 'listRequestedPayouts']);
    });
});
