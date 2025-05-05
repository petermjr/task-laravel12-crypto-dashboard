<?php

namespace App\Services;

use App\Contracts\TickerServiceInterface;
use App\DTO\TickerDTO;
use Illuminate\Support\Facades\Http;

class BinanceService implements TickerServiceInterface
{
    protected string $baseUrl = 'https://api.binance.com/api/v3';

    public function getHistoricalData(string $symbol, string $interval = '1m', int $limit = 60): array
    {
        $response = Http::get("{$this->baseUrl}/klines", [
            'symbol' => $symbol,
            'interval' => $interval,
            'limit' => $limit
        ]);

        if ($response->successful()) {
            return array_map(function ($item) use ($symbol) {
                return TickerDTO::fromArray([
                    'symbol' => $symbol,
                    'lastPrice' => (float)$item[4],
                    'highPrice' => (float)$item[2], // High price
                    'lowPrice' => (float)$item[3], // Low price
                    'volume' => (float)$item[5], // Volume
                    'priceChangePercent' => 0,
                    'timestamp' => now()->timestamp($item[0] / 1000) // Convert from milliseconds to seconds
                ]);
            }, $response->json());
        }

        return [];
    }
}
