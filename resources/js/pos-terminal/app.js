import { createApp } from 'vue';
import CheckoutPage from './pages/Checkout.vue';

const app = createApp({});

app.component('checkout-page', CheckoutPage);

app.mount('#app');