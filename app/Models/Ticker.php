<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Ticker extends Model
{
    protected $fillable = [
        'symbol',
        'last_price',
        'high_price',
        'low_price',
        'volume',
        'timestamp'
    ];

    protected $casts = [
        'last_price' => 'decimal:8',
        'high_price' => 'decimal:8',
        'low_price' => 'decimal:8',
        'volume' => 'decimal:8',
        'timestamp' => 'datetime'
    ];

    /**
     * Scope a query to filter tickers by symbol and timestamp.
     *
     * @param Builder $query
     * @param  string  $symbol
     * @param string|Carbon $timestamp
     * @return Builder
     */
    public function scopeNewerThan(Builder $query, string $symbol, Carbon|string $timestamp): Builder
    {
        return $query->where('symbol', $symbol)
                     ->where('timestamp', '>=', $timestamp);
    }
}
