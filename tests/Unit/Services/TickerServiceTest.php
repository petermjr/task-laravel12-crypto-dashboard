<?php

namespace Tests\Unit\Services;

use App\Contracts\TickerServiceInterface;
use App\DTO\TickerDTO;
use App\Models\Ticker;
use App\Services\TickerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TickerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TickerService $tickerService;
    protected TickerServiceInterface $tickerServiceInterface;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tickerServiceInterface = Mockery::mock(TickerServiceInterface::class);
        $this->tickerService = new TickerService($this->tickerServiceInterface);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_saves_ticker_data()
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

        $ticker = $this->tickerService->saveTickerData($dto);

        $this->assertInstanceOf(Ticker::class, $ticker);
        $this->assertEquals('BTCUSDT', $ticker->symbol);
        $this->assertEquals(50000.00, $ticker->last_price);
        $this->assertEquals(51000.00, $ticker->high_price);
        $this->assertEquals(49000.00, $ticker->low_price);

        $this->assertDatabaseHas('tickers', [
            'symbol' => 'BTCUSDT',
            'last_price' => 50000.00
        ]);
    }

    /** @test */
    public function it_gets_multiple_tickers()
    {
        // Create some ticker data
        Ticker::create([
            'symbol' => 'BTCUSDT',
            'last_price' => 50000.00,
            'high_price' => 51000.00,
            'low_price' => 49000.00,
            'volume' => 1000.00,
            'timestamp' => now()
        ]);

        Ticker::create([
            'symbol' => 'BTCUSDT',
            'last_price' => 48000.00,
            'high_price' => 49000.00,
            'low_price' => 47000.00,
            'volume' => 900.00,
            'timestamp' => now()->subHour()
        ]);

        Ticker::create([
            'symbol' => 'ETHUSDT',
            'last_price' => 3000.00,
            'high_price' => 3100.00,
            'low_price' => 2900.00,
            'volume' => 500.00,
            'timestamp' => now()
        ]);

        $result = $this->tickerService->getMultipleTickers(['BTCUSDT', 'ETHUSDT']);

        $this->assertEquals(2, $result->count());
        $this->assertEquals('BTCUSDT', $result[0]->symbol);
        $this->assertEquals('ETHUSDT', $result[1]->symbol);

        // Check price change percent calculation for BTC (should be around 4.17%)
        // (50000 - 48000) / 48000 * 100 = 4.17%
        $this->assertEqualsWithDelta(4.17, $result[0]->priceChangePercent, 0.01);
    }

    /** @test */
    public function it_fetches_ticker_history_from_database()
    {
        // Create some ticker history
        for ($i = 0; $i < 10; $i++) {
            Ticker::create([
                'symbol' => 'BTCUSDT',
                'last_price' => 50000.00 + ($i * 100),
                'high_price' => 51000.00 + ($i * 100),
                'low_price' => 49000.00 + ($i * 100),
                'volume' => 1000.00,
                'timestamp' => now()->subMinutes($i * 5)
            ]);
        }

        $result = $this->tickerService->fetchTickerHistory('BTCUSDT');

        $this->assertCount(10, $result);
        $this->assertEquals('BTCUSDT', $result[0]->symbol);
    }

    /** @test */
    public function it_refreshes_ticker_history_from_service()
    {
        // Mock external service response
        $mockedData = [];
        for ($i = 0; $i < 60; $i++) {
            $mockedData[] = new TickerDTO(
                symbol: 'BTCUSDT',
                lastPrice: 50000.00 + ($i * 10),
                priceChangePercent: 0.5,
                highPrice: 51000.00 + ($i * 10),
                lowPrice: 49000.00 + ($i * 10),
                volume: 1000.00,
                timestamp: (new \DateTime())->modify("-{$i} minutes")
            );
        }

        $this->tickerServiceInterface
            ->shouldReceive('getHistoricalData')
            ->once()
            ->with('BTCUSDT', '1m', 60)
            ->andReturn($mockedData);

        $result = $this->tickerService->fetchTickerHistory('BTCUSDT', true);

        $this->assertCount(60, $result);
        $this->assertEquals('BTCUSDT', $result[0]->symbol);

        // Verify data was saved to database
        $this->assertDatabaseCount('tickers', 60);
    }
}
