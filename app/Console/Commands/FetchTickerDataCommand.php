<?php

namespace App\Console\Commands;

use App\Jobs\FetchTickerDataJob;
use Illuminate\Console\Command;

class FetchTickerDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-ticker-data {--symbols=* : Symbols to fetch data for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch ticker data from cryptocurrency exchanges';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $symbols = $this->option('symbols');

        if (empty($symbols)) {
            $symbols = ['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'ADAUSDT', 'SOLUSDT'];
        }

        $this->info('Fetching ticker data for: ' . implode(', ', $symbols));

        // Dispatch the job to run immediately
        FetchTickerDataJob::dispatchSync($symbols);

        $this->info('Job dispatched successfully!');
    }
}
