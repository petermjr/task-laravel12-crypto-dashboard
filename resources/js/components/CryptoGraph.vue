<template>
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">{{ symbol }}</h3>
            <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="h-[400px]">
            <Line :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>

<script>
import { Line } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend } from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend)

export default {
    name: 'CryptoGraph',
    components: { Line },
    props: {
        symbol: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            chartData: {
                labels: [],
                datasets: [{
                    label: 'Price',
                    data: [],
                    borderColor: '#3B82F6',
                    tension: 0.1
                }]
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        }
    },
    watch: {
        symbol: {
            immediate: true,
            handler() {
                this.fetchHistoricalData();
            }
        }
    },
    methods: {
        async fetchHistoricalData() {
            try {
                console.log('Fetching data for symbol:', this.symbol);
                const response = await fetch(`/api/tickers/history?symbol=${this.symbol}`);
                const data = await response.json();
                console.log('Received data:', data);

                this.chartData = {
                    labels: data.map(item => new Date(item.timestamp).toLocaleTimeString()),
                    datasets: [{
                        label: 'Price',
                        data: data.map(item => parseFloat(item.lastPrice)),
                        borderColor: '#3B82F6',
                        tension: 0.1
                    }]
                };
            } catch (error) {
                console.error('Error fetching historical data:', error);
            }
        }
    }
}
</script>
