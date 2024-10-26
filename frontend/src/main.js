import axios from 'axios';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

// Configuração do Axios para a API
axios.defaults.baseURL = 'http://localhost:8000'; // URL do backend Laravel
axios.defaults.headers.common['Accept'] = 'application/json';

createApp(App).use(router).mount('#app');
