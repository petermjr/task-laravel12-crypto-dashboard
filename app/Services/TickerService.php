<?php

namespace App\Services;

use App\Contracts\TickerServiceInterface;
use App\DTO\TickerDTO;
use App\Models\Ticker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TickerService
{
    public function __construct(
        public TickerServiceInterface $tickerService
    )
    {
    }

    /**
     * Save ticker data from a DTO to the database
     */
    public function saveTickerData(TickerDTO $ticker): ?Ticker
    {
        $data = $ticker->toDbArray();

        // Use updateOrCreate to handle the unique constraint
        return Ticker::updateOrCreate(
            [
                'symbol' => $data['symbol'],
                'timestamp' => $data['timestamp']
            ],
            $data
        );
    }

    /**
     * Get the latest ticker data for each of the provided symbols
     *
     * @param array $symbols
     * @return Collection<TickerDTO>
     */
    public function getMultipleTickers(array $symbols): Collection
    {
        $result = collect();
        $limit = 60;

        foreach ($symbols as $symbol) {
            $tickers = Ticker::where('symbol', $symbol)
                ->orderBy('timestamp', 'desc')
                ->limit($limit)
                ->get();

            if ($tickers->isNotEmpty()) {
                $latestTicker = $tickers->first();

                // If we have enough data, calculate price change percent from first to last entry
                if ($tickers->count() >= 2) {
                    $result->push(TickerDTO::fromModel($latestTicker, $tickers->last()));
                }else{
                    $result->push(TickerDTO::fromModel($latestTicker));
                }
            }
        }

        return $result;
    }

    /**
     * Get historical ticker data for a specific symbol
     *
     * @param string $symbol Symbol to get history for
     * @return Collection<TickerDTO>
     */
    public function fetchTickerHistory(string $symbol, bool $refreshData = false): Collection
    {
        $limit = 60;
        $interval = '1m';

        $startTime = now()->subHours();
        $historyData = Ticker::newerThan($symbol, $startTime)
            ->orderBy('timestamp', 'asc')
            ->get();

        $historyDTOs = $historyData->map(function ($item) {
            return TickerDTO::fromModel($item);
        });

        if ($refreshData) {
            $freshData = $this->tickerService->getHistoricalData($symbol, $interval, $limit);

            if (!empty($freshData) && count($freshData) == $limit) {
                $historyDTOs = collect();
                foreach ($freshData as $dataPoint) {
                    $historyDTOs->push(TickerDTO::fromModel($this->saveTickerData($dataPoint)));
                }
            } else {
                Log::error('Failed to fetch complete historical data', [
                    'expected' => $limit,
                    'received' => count($freshData)
                ]);
            }
        }

        if ($historyDTOs->count() != $limit) {
            Log::warning('Incomplete historical data for ' . $symbol, [
                'expected' => $limit,
                'actual' => $historyDTOs->count()
            ]);
        }

        return $historyDTOs;
    }
}
