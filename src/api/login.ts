import { AxiosPromise } from 'axios';
import request from '@/utils/request';
import { IPhoneLogin, IPhoneLoginResponse } from '@/typings/login';

// 管理员登录(手机号登录)
export function loginByPhone(
  data: IPhoneLogin,
): AxiosPromise<IPhoneLoginResponse> {
  return request('login', 'post', data);
}

//初始化登录信息
export function initLoginInfo(): AxiosPromise<IPhoneLoginResponse> {
  return request('start', 'get', '');
}
