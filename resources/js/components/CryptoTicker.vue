<template>
    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div v-for="ticker in tickers" :key="ticker.symbol"
                 @click="showGraph(ticker.symbol)"
                 class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition-shadow cursor-pointer">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold">{{ ticker.symbol }}</h3>
                        <p class="text-2xl font-bold">${{ formatPrice(ticker.lastPrice) }}</p>
                    </div>
                    <div :class="['text-sm font-medium', getPriceChangeClass(ticker.priceChangePercent)]">
                        {{ formatPercent(ticker.priceChangePercent) }}
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-500">
                    <div>24h High: ${{ formatPrice(ticker.highPrice) }}</div>
                    <div>24h Low: ${{ formatPrice(ticker.lowPrice) }}</div>
                </div>
            </div>
        </div>

        <div v-if="selectedSymbol" class="mt-8">
            <crypto-graph
                :symbol="selectedSymbol"
                @close="selectedSymbol = null"
            />
        </div>
    </div>
</template>

<script>
import CryptoGraph from './CryptoGraph.vue';

export default {
    name: 'CryptoTicker',
    components: {
        CryptoGraph
    },
    data() {
        return {
            tickers: [],
            symbols: ['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'ADAUSDT', 'SOLUSDT'],
            selectedSymbol: null
        }
    },
    mounted() {
        this.fetchTickers();
        // Refresh every 30 seconds
        setInterval(this.fetchTickers, 30000);
    },
    methods: {
        showGraph(symbol) {
            this.selectedSymbol = symbol;
        },
        async fetchTickers() {
            try {
                const response = await fetch(`/api/tickers/list?symbols[]=${this.symbols.join('&symbols[]=')}`);
                const data = await response.json();
                this.tickers = data;
            } catch (error) {
                console.error('Error fetching tickers:', error);
            }
        },
        formatPrice(price) {
            return parseFloat(price).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },
        formatPercent(percent) {
            return `${parseFloat(percent).toFixed(2)}%`;
        },
        getPriceChangeClass(percent) {
            return parseFloat(percent) >= 0 ? 'text-green-600' : 'text-red-600';
        }
    }
}
</script>
