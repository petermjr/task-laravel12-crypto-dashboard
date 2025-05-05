<?php

use App\Http\Controllers\Api\TickerController;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('tickers')->group(function () {
    Route::get('/list', [TickerController::class, 'getMultipleTickers']);
    Route::get('/history', [TickerController::class, 'getHistory']);
});
