import axios, { Method } from 'axios';
import { router } from '@/router';
import { useAppStore } from '@/store/main';
import { setting } from '@/config';

export const baseURL = setting.apiRoot + '/a';
const instance = axios.create({
  baseURL,
  timeout: 3000
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
    config.headers!.Authorization = 'bearer ' + store.token;
    return config;
  },
  (err) => {
    return Promise.reject(err);
  }
);

instance.interceptors.response.use(
  (res) => {
    if (res.data.code < 0) {
      return Promise.reject(res.data.message);
    }
    return Promise.resolve(res.data);
  },
  (err) => {
    if (err.response && err.response.status === 401) {
      // 1。清空无效用户信息
      // 2.跳转到登录页
      // 3.传参，当前路由地址
      router.push('login');
      sessionStorage.clear();
      window.location.reload();
    }
    return Promise.reject(err);
  }
);

const request: any = (url: string, method: Method, submitData: any = []) => {
  return instance({
    url,
    method,
    [method.toLowerCase() === 'get' ? 'params' : 'data']: submitData
  });
};

export default request;
