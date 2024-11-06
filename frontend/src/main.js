import axios from 'axios';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

axios.defaults.baseURL = 'http://localhost:8000';
axios.defaults.headers.common['Accept'] = 'application/json';

createApp(App).use(router).mount('#app');
