<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickers', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->decimal('last_price', 20, 8);
            $table->decimal('high_price', 20, 8);
            $table->decimal('low_price', 20, 8);
            $table->decimal('volume', 20, 8);
            $table->timestamp('timestamp');
            $table->timestamps();

            $table->unique(['symbol', 'timestamp'], 'unique_symbol_timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickers');
    }
};
