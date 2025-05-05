<?php

namespace Tests\Feature\Controllers;

use App\DTO\TickerDTO;
use App\Models\Ticker;
use App\Services\TickerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class TickerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_returns_multiple_tickers()
    {
        // Create mock data
        $dto1 = new TickerDTO(
            symbol: 'BTCUSDT',
            lastPrice: 50000.00,
            priceChangePercent: 2.5,
            highPrice: 51000.00,
            lowPrice: 49000.00,
            volume: 1000.00,
            timestamp: new \DateTime('2023-01-01 12:00:00')
        );

        $dto2 = new TickerDTO(
            symbol: 'ETHUSDT',
            lastPrice: 3000.00,
            priceChangePercent: 1.5,
            highPrice: 3100.00,
            lowPrice: 2900.00,
            volume: 500.00,
            timestamp: new \DateTime('2023-01-01 12:00:00')
        );

        // Mock the service
        $this->mock(TickerService::class, function ($mock) use ($dto1, $dto2) {
            $mock->shouldReceive('getMultipleTickers')
                ->once()
                ->with(['BTCUSDT', 'ETHUSDT'])
                ->andReturn(collect([$dto1, $dto2]));
        });

        // Make the request
        $response = $this->getJson('/api/tickers/list?symbols[]=BTCUSDT&symbols[]=ETHUSDT');

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonPath('0.symbol', 'BTCUSDT')
            ->assertJsonPath('0.lastPrice', 50000)
            ->assertJsonPath('1.symbol', 'ETHUSDT')
            ->assertJsonPath('1.lastPrice', 3000);
    }

    /** @test */
    public function it_returns_ticker_history()
    {
        // Create ticker history DTOs
        $historyDTOs = [];
        for ($i = 0; $i < 10; $i++) {
            $historyDTOs[] = new TickerDTO(
                symbol: 'BTCUSDT',
                lastPrice: 50000.00 + ($i * 100),
                priceChangePercent: 0.1 * $i,
                highPrice: 51000.00 + ($i * 100),
                lowPrice: 49000.00 + ($i * 100),
                volume: 1000.00,
                timestamp: (new \DateTime())->modify("-{$i} minutes")
            );
        }

        // Mock the service
        $this->mock(TickerService::class, function ($mock) use ($historyDTOs) {
            $mock->shouldReceive('fetchTickerHistory')
                ->once()
                ->with('BTCUSDT')
                ->andReturn(collect($historyDTOs));
        });

        // Make the request
        $response = $this->getJson('/api/tickers/history?symbol=BTCUSDT');

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonCount(10)
            ->assertJsonPath('0.symbol', 'BTCUSDT');
    }
}
