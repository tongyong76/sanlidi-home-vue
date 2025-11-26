import { ref } from 'vue';
import type { Router, RouteLocationNormalized } from 'vue-router';

// 是否已注册动态路由
// const isRouteRegistered = ref(false);

// 是否已获取菜单数据
const isMenuDataFetched = ref(false);

export function setupBeforeEachGuard(router: Router): void {
  router.beforeEach(
    async (
      to: RouteLocationNormalized
      // next: NavigationGuardNext
    ) => {
      try {
        console.log('isMenuDataFetched', isMenuDataFetched.value);
        // 登录页、500 页、404 页不进行权限判断
        const arr_pass = ['Login', '500', '404'];
        if (arr_pass.includes(to.name as string)) {
          return true;
        }
      } catch (error) {
        // 将用户重定向到登录页面
        console.error('路由守卫处理失败:', error);
        if (to.name !== '500') {
          return { name: '500' };
        }
      }
    }
  );

  // 设置后置守卫以关闭 loading 和进度条
  //setupAfterEachGuard(router);
}
