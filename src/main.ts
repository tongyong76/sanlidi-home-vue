import { createApp } from 'vue';
import App from './App.vue';
// import usePinia from '@/store/index';
import { initStore } from './store';
import { initRouter } from '@/router/index';
import SanUI from '@/components';
import ElementPlus from 'element-plus';
import zhCn from 'element-plus/es/locale/lang/zh-cn';
import 'element-plus/dist/index.css';
import 'normalize.css';
import './styles/index.scss';
import '@styles/main.scss';

export const ttt = 'tttlll';

const app = createApp(App);
initRouter(app);
initStore(app);
app.use(SanUI);
app.use(ElementPlus, { locale: zhCn });
app.mount('#app');
