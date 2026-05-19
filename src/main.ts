import { createApp } from 'vue';
import App from './App.vue';
// import usePinia from '@/store/index';
import { initStore } from './store';
import { initRouter } from '@/router/index';
import 'normalize.css';
import './assets/styles/index.scss';

const app = createApp(App);
initRouter(app);
initStore(app);
app.mount('#app');
