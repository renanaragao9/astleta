import type { App as VueApp } from 'vue';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createHead } from '@vueuse/head';

import App from './App.vue';
import router from './router/index';

import Aura from '@primeuix/themes/aura';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import ptBR from './locales/pt-BR';

import '@/assets/styles.scss';

const app: VueApp = createApp(App);

app.use(createPinia());
app.use(router);
app.use(PrimeVue, {
    locale: ptBR,
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: '.app-dark'
        }
    }
});
app.use(ToastService);
app.use(ConfirmationService);

const head = createHead();
app.use(head);

app.mount('#app');
