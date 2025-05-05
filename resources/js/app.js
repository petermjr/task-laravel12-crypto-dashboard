import './bootstrap';
import { createApp } from 'vue';
import CryptoTicker from './components/CryptoTicker.vue';

const app = createApp({});

app.component('crypto-ticker', CryptoTicker);

app.mount('#app');
