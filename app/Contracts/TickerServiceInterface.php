<?php

namespace App\Contracts;

use App\DTO\TickerDTO;

interface TickerServiceInterface
{
    /**
     * Get historical price data for a symbol
     *
     * @param string $symbol
     * @param string $interval
     * @param int $limit
     * @return array<TickerDTO>
     */
    public function getHistoricalData(string $symbol, string $interval = '1m', int $limit = 60): array;
}
