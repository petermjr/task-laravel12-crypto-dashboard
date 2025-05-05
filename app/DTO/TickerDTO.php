<?php

namespace App\DTO;

use App\Models\Ticker;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Log;
use JsonSerializable;

readonly class TickerDTO implements Arrayable, JsonSerializable
{
    public function __construct(
        public string    $symbol,
        public float     $lastPrice,
        public float     $priceChangePercent,
        public float     $highPrice,
        public float     $lowPrice,
        public float     $volume,
        public \DateTime $timestamp
    ) {
    }

    /**
     * Create a DTO from raw data array (from API)
     */
    public static function fromArray(array $data, ?array $oldestTicker = null): self
    {
        $priceChangePercent = (float) ($data['priceChangePercent'] ?? 0);

        // Calculate price change percent if oldest ticker is provided
        if ($oldestTicker && isset($data['lastPrice']) && $oldestTicker['lastPrice'] > 0) {
            $currentPrice = (float) $data['lastPrice'];
            $priceChangePercent = (($currentPrice - $oldestTicker['lastPrice']) / $oldestTicker['lastPrice']) * 100;
        }

        return new self(
            symbol: $data['symbol'],
            lastPrice: (float) ($data['lastPrice'] ?? 0),
            priceChangePercent: $priceChangePercent,
            highPrice: (float) ($data['highPrice'] ?? $data['lastPrice'] ?? 0),
            lowPrice: (float) ($data['lowPrice'] ?? $data['lastPrice'] ?? 0),
            volume: (float) ($data['volume'] ?? 0),
            timestamp: $data['timestamp'] instanceof \DateTime
                ? $data['timestamp']
                : new \DateTime($data['timestamp'] ?? 'now')
        );
    }

    /**
     * Create a DTO from a CryptoTicker model
     */
    public static function fromModel(Ticker $ticker, ?Ticker $oldestTicker = null): self
    {
        $priceChangePercent = (float) $ticker->price_change_percent;

        // Calculate price change percent if oldest ticker is provided
        if ($oldestTicker && $oldestTicker->last_price > 0) {
            $priceChangePercent = (($ticker->last_price - $oldestTicker->last_price) / $oldestTicker->last_price) * 100;
        }

        return new self(
            symbol: $ticker->symbol,
            lastPrice: (float) $ticker->last_price,
            priceChangePercent: $priceChangePercent,
            highPrice: (float) $ticker->high_price,
            lowPrice: (float) $ticker->low_price,
            volume: (float) $ticker->volume,
            timestamp: $ticker->timestamp
        );
    }

    /**
     * Convert to array for database storage (snake_case keys)
     */
    public function toDbArray(): array
    {
        return [
            'symbol' => $this->symbol,
            'last_price' => $this->lastPrice,
            'price_change_percent' => $this->priceChangePercent,
            'high_price' => $this->highPrice,
            'low_price' => $this->lowPrice,
            'volume' => $this->volume,
            'timestamp' => $this->timestamp
        ];
    }

    /**
     * Convert to array for JSON response (camelCase keys)
     */
    public function toArray(): array
    {
        return [
            'symbol' => $this->symbol,
            'lastPrice' => $this->lastPrice,
            'priceChangePercent' => $this->priceChangePercent,
            'highPrice' => $this->highPrice,
            'lowPrice' => $this->lowPrice,
            'volume' => $this->volume,
            'timestamp' => $this->timestamp
        ];
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
