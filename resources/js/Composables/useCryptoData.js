import { ref } from 'vue'
import axios from 'axios'

export function useCryptoData() {
    const tickers = ref([])
    const marketOverview = ref(null)
    const symbolHistory = ref(null)
    const topGainers = ref([])
    const topLosers = ref([])
    const loading = ref(false)
    const error = ref(null)

    const fetchLatestTickers = async () => {
        try {
            loading.value = true
            const response = await axios.get('/api/crypto/tickers')
            tickers.value = response.data.data
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const fetchMarketOverview = async () => {
        try {
            loading.value = true
            const response = await axios.get('/api/crypto/market-overview')
            marketOverview.value = response.data.data
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const fetchSymbolHistory = async (symbol) => {
        try {
            loading.value = true
            const response = await axios.get(`/api/crypto/symbol/${symbol}/history`)
            symbolHistory.value = response.data.data
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const fetchTopGainers = async () => {
        try {
            loading.value = true
            const response = await axios.get('/api/crypto/top-gainers')
            topGainers.value = response.data.data
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const fetchTopLosers = async () => {
        try {
            loading.value = true
            const response = await axios.get('/api/crypto/top-losers')
            topLosers.value = response.data.data
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    return {
        tickers,
        marketOverview,
        symbolHistory,
        topGainers,
        topLosers,
        loading,
        error,
        fetchLatestTickers,
        fetchMarketOverview,
        fetchSymbolHistory,
        fetchTopGainers,
        fetchTopLosers
    }
} 