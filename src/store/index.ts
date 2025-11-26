import type { App } from 'vue';
import { createPinia } from 'pinia';
import piniaPluginPersist from 'pinia-plugin-persistedstate';

export const store = createPinia();

// 配置持久化插件
store.use(piniaPluginPersist);

export function initStore(app: App<Element>): void {
  app.use(store);
}
