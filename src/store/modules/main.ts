import { router } from '@/router';
import { defineStore } from 'pinia';
import { IMenuTree, IHistoryMenu } from '@/typings/menu';
import { ref, reactive } from 'vue';
import { ICity } from '@/typings/city';
import { IGoods } from '@/typings/goods';

export const useAppStore = defineStore(
  'main',
  () => {
    const admin = reactive({
      nickname: '未知',
      avatar: 'default.jpg'
    });
    const token = ref('');
    const menuTree = ref<IMenuTree[]>([]);
    const rules = ref<string[]>([]);
    const historyMenu = ref<IHistoryMenu[]>([]);
    const loading = ref(false);
    const linkActive = ref('');
    const iconActive = ref(0);
    const urls = ref<any[]>([]);
    const cities = ref<ICity[]>([]);
    const goodsAll = ref<IGoods[]>([]);
    const goodsAllChangeFlag = ref(false);
    const isLogin = ref(false);
    const refresh = ref(false);

    const order = reactive({
      orderSrc: 'all',
      timeRange: {
        start: 0,
        end: 0
      },
      status: 'all',
      searchKey: 'sn',
      searchValue: ''
    });

    // 订单统计
    const statisticOrder = ref({
      all: 0,
      waitPay: 0,
      waitSend: 0,
      waitReceive: 0,
      waitComment: 0,
      done: 0
    });

    /**
     * 设置菜单列表
     * @param list 菜单路由记录数组
     */
    const setMenuList = (list: IMenuTree[]) => {
      menuTree.value = list;
      //setHomePath(homePath.value || getFirstMenuPath(list));
    };

    const logout = () => {
      // 清空用户信息
      admin.avatar = 'default.jpg';
      admin.nickname = '未知';
      // 重置登录状态
      isLogin.value = false;
      // 清空访问令牌
      token.value = '';
      // 清空工作台已打开页面
      // 移除iframe路由缓存
      // 清空主页路径
      // 重置路由状态
      // 跳转到登录页
      router.replace('/login');
    };

    /**
     * 刷新页面
     */
    const reload = () => {
      refresh.value = !refresh.value;
    };

    return {
      admin,
      token,
      menuTree,
      rules,
      historyMenu,
      loading,
      linkActive,
      iconActive,
      urls,
      cities,
      goodsAll,
      goodsAllChangeFlag,
      order,
      statisticOrder,
      isLogin,
      refresh,
      logout,
      setMenuList,
      reload
    };
  },
  {
    persist: {
      key: 'globle',
      storage: localStorage
    }
  }
);
