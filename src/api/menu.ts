import { IMenuItem } from '@/typings/menu';
import request from '@/utils/request';
import { AxiosPromise } from 'axios';

//获取全部
export function getMenus(): AxiosPromise<any> {
  return request('menus', 'get', {});
}

export function getAllMenus(): AxiosPromise<any> {
  return request('menu/all', 'get', {});
}

export function addMenu(data: IMenuItem): AxiosPromise<any> {
  return request('menu', 'post', data);
}

export function setMenu(data: IMenuItem): AxiosPromise<any> {
  return request('menu', 'put', data);
}

export function removeMenu(id: number): AxiosPromise<any> {
  return request('menu/' + id, 'delete', {});
}
