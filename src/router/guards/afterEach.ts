//import { useSettingStore } from '@/store/modules/setting';
import { Router } from 'vue-router';

/** 路由全局后置守卫 */
export function setupAfterEachGuard(router: Router) {
  router.afterEach(() => {
    //if (useSettingStore().showNprogress) NProgress.done();
  });
}
