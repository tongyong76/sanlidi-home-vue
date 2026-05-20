import axios, { AxiosResponse, Method } from 'axios';
import { router } from '@/router';
import { useAppStore } from '@/store/modules/main';
import { API_URL } from '@/config/index';

const instance = axios.create({
  baseURL: API_URL,
  timeout: 5000
});

instance.interceptors.request.use(
  (config) => {
    if (config.url !== 'login' && config.url !== 'auth') {
      //token失效
      // if (localStorage.getItem('admin') && diffTokenTime()) {
      //     router.push('login')
      //     ElMessage.warning('token 失效了,请重新登录');
      //     return Promise.reject(new Error('token 失效了'));
      // }
    }
    const store = useAppStore();
    // add timing metadata for performance analysis
    (config as any).metadata = { startTime: Date.now() };
    config.headers!.Authorization = 'bearer ' + store.token;
    return config;
  },
  (err) => {
    return Promise.reject(err);
  }
);

instance.interceptors.response.use(
  (res: AxiosResponse) => {
    try {
      const start = (res.config as any)?.metadata?.startTime;
      const duration = start ? Date.now() - start : -1;
      const size =
        res.headers && res.headers['content-length']
          ? res.headers['content-length']
          : typeof res.data === 'string'
            ? res.data.length
            : JSON.stringify(res.data).length;
      console.info(
        `[HTTP] ${res.config.method?.toUpperCase()} ${res.config.url} ${res.status} ${duration}ms size:${size}B`
      );
    } catch (e) {
      /* ignore logging errors */
    }

    if (res.data.code == 0) {
      return Promise.resolve(res.data.data);
    } else {
      return Promise.reject(res.data.message);
    }
  },
  (err) => {
    try {
      const config = err.config || {};
      const start = (config as any)?.metadata?.startTime;
      const duration = start ? Date.now() - start : -1;
      console.warn(
        `[HTTP-ERR] ${config.method?.toUpperCase()} ${config.url} ${err?.response?.status || ''} ${duration}ms`,
        err.message
      );
    } catch (e) {}

    if (err.response && err.response.status === 401) {
      // 1. 清空无效用户信息
      // 2. 跳转到首页，避免重复重定向
      // 3. 传参，当前路由地址（可根据需求自行实现）
      const currentPath = router.currentRoute.value?.path || '';
      if (currentPath !== '/login') {
        // 使用 replace 避免在历史记录中产生多余条目
        router.replace('/login');
      }
      sessionStorage.clear();
      window.location.reload();
    }
    const errorMsg = err.response?.data?.message || err.message || '网络请求失败';
    return Promise.reject(errorMsg);
  }
);

const request = async <T = any>(url: string, method: Method, submitData: any = {}): Promise<T> => {
  try {
    const res = await instance({
      url,
      method,
      [method.toLowerCase() === 'get' ? 'params' : 'data']: submitData
    });

    return res as T;
  } catch (error: any) {
    router.replace('/login');
    //Message.error(error);
    throw error;
  }
};

export default request;
