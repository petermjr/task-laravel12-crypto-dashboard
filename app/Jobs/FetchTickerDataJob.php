<?php

namespace App\Jobs;

use App\Services\TickerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchTickerDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The symbols to fetch data for
     */
    protected array $symbols;

    /**
     * Create a new job instance.
     */
    public function __construct(array $symbols = ['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'ADAUSDT', 'SOLUSDT'])
    {
        $this->symbols = $symbols;
    }

    /**
     * Execute the job.
     */
    public function handle(TickerService $tickerService): void
    {
        Log::info('Starting ticker data fetch job', ['symbols' => $this->symbols]);

        try {
            foreach ($this->symbols as $symbol) {
                $tickerService->fetchTickerHistory($symbol, true);
                Log::info('Fetched ticker data for symbol: ' . $symbol);
            }
        } catch (\Exception $e) {
            Log::error('Error in ticker data fetch job', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
