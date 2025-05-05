<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TickerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TickerController extends Controller
{
    public function __construct(protected TickerService $tickerService)
    {
    }

    public function getMultipleTickers(Request $request): JsonResponse
    {
        $symbols = $request->get('symbols');
        $tickers = $this->tickerService->getMultipleTickers($symbols);

        return response()->json($tickers);
    }

    public function getHistory(Request $request): JsonResponse
    {
        $symbol = $request->get('symbol');
        $history = $this->tickerService->fetchTickerHistory($symbol);

        return response()->json($history);
    }
}
