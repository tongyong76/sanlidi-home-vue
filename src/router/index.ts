import type { App } from 'vue';
import { createRouter, createWebHashHistory, RouteRecordRaw } from 'vue-router';
import { setupBeforeEachGuard } from './guards/beforeEach';
import { setupAfterEachGuard } from './guards/afterEach';

const Login = () => import('@/views/login/index.vue');
const Index = () => import('@/views/Index/index.vue');
const Error500 = () => import('@/views/Exception/500/index.vue');
const Error404 = () => import('@/views/Exception/404/index.vue');

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    redirect: { name: 'Index' }
  },
  {
    path: '/index',
    name: 'Index',
    component: Index
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/500',
    name: '500',
    component: Error500
  },
  {
    path: '/404',
    name: '404',
    component: Error404
  }
];

export const router = createRouter({
  history: createWebHashHistory(),
  routes
});

export function initRouter(app: App<Element>): void {
  setupBeforeEachGuard(router); // 路由前置守卫
  setupAfterEachGuard(router); // 路由后置守卫
  app.use(router);
}
