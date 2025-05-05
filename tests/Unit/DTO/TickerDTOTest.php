<?php

namespace Tests\Unit\DTO;

use App\DTO\TickerDTO;
use App\Models\Ticker;
use Tests\TestCase;

class TickerDTOTest extends TestCase
{
    /** @test */
    public function it_can_be_created_from_array()
    {
        $data = [
            'symbol' => 'BTCUSDT',
            'lastPrice' => 50000.00,
            'priceChangePercent' => 2.5,
            'highPrice' => 51000.00,
            'lowPrice' => 49000.00,
            'volume' => 1000.00,
            'timestamp' => '2023-01-01 12:00:00'
        ];

        $dto = TickerDTO::fromArray($data);

        $this->assertEquals('BTCUSDT', $dto->symbol);
        $this->assertEquals(50000.00, $dto->lastPrice);
        $this->assertEquals(2.5, $dto->priceChangePercent);
        $this->assertEquals(51000.00, $dto->highPrice);
        $this->assertEquals(49000.00, $dto->lowPrice);
        $this->assertEquals(1000.00, $dto->volume);
        $this->assertInstanceOf(\DateTime::class, $dto->timestamp);
    }

    /** @test */
    public function it_can_calculate_price_change_percent_from_oldest_ticker_array()
    {
        $data = [
            'symbol' => 'BTCUSDT',
            'lastPrice' => 50000.00,
            'highPrice' => 51000.00,
            'lowPrice' => 49000.00,
            'volume' => 1000.00,
            'timestamp' => '2023-01-01 12:00:00'
        ];

        $oldestTicker = [
            'lastPrice' => 40000.00
        ];

        $dto = TickerDTO::fromArray($data, $oldestTicker);

        // (50000 - 40000) / 40000 * 100 = 25%
        $this->assertEquals(25.0, $dto->priceChangePercent);
    }

    /** @test */
    public function it_can_be_created_from_model()
    {
        $ticker = new Ticker();
        $ticker->symbol = 'ETHUSDT';
        $ticker->last_price = 3000.00;
        $ticker->price_change_percent = 1.5;
        $ticker->high_price = 3100.00;
        $ticker->low_price = 2900.00;
        $ticker->volume = 500.00;
        $ticker->timestamp = now();

        $dto = TickerDTO::fromModel($ticker);

        $this->assertEquals('ETHUSDT', $dto->symbol);
        $this->assertEquals(3000.00, $dto->lastPrice);
        $this->assertEquals(1.5, $dto->priceChangePercent);
        $this->assertEquals(3100.00, $dto->highPrice);
        $this->assertEquals(2900.00, $dto->lowPrice);
        $this->assertEquals(500.00, $dto->volume);
    }

    /** @test */
    public function it_can_calculate_price_change_percent_from_oldest_ticker_model()
    {
        $ticker = new Ticker();
        $ticker->symbol = 'ETHUSDT';
        $ticker->last_price = 3000.00;
        $ticker->high_price = 3100.00;
        $ticker->low_price = 2900.00;
        $ticker->volume = 500.00;
        $ticker->timestamp = now();

        $oldestTicker = new Ticker();
        $oldestTicker->last_price = 2400.00;

        $dto = TickerDTO::fromModel($ticker, $oldestTicker);

        // (3000 - 2400) / 2400 * 100 = 25%
        $this->assertEquals(25.0, $dto->priceChangePercent);
    }

    /** @test */
    public function it_can_convert_to_db_array()
    {
        $dto = new TickerDTO(
            symbol: 'BTCUSDT',
            lastPrice: 50000.00,
            priceChangePercent: 2.5,
            highPrice: 51000.00,
            lowPrice: 49000.00,
            volume: 1000.00,
            timestamp: new \DateTime('2023-01-01 12:00:00')
        );

        $dbArray = $dto->toDbArray();

        $this->assertIsArray($dbArray);
        $this->assertEquals('BTCUSDT', $dbArray['symbol']);
        $this->assertEquals(50000.00, $dbArray['last_price']);
        $this->assertEquals(2.5, $dbArray['price_change_percent']);
        $this->assertEquals(51000.00, $dbArray['high_price']);
        $this->assertEquals(49000.00, $dbArray['low_price']);
    }

    /** @test */
    public function it_can_convert_to_array()
    {
        $dto = new TickerDTO(
            symbol: 'BTCUSDT',
            lastPrice: 50000.00,
            priceChangePercent: 2.5,
            highPrice: 51000.00,
            lowPrice: 49000.00,
            volume: 1000.00,
            timestamp: new \DateTime('2023-01-01 12:00:00')
        );

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('BTCUSDT', $array['symbol']);
        $this->assertEquals(50000.00, $array['lastPrice']);
        $this->assertEquals(2.5, $array['priceChangePercent']);
        $this->assertEquals(51000.00, $array['highPrice']);
        $this->assertEquals(49000.00, $array['lowPrice']);
    }
}