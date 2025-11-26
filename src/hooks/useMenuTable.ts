/*
 ************************************************
 *
 * 菜单menu table - 相关操作
 * Author: GuWenjun
 * Time: 2025-05-23
 *
 * ***********************************************
 */

import request from '@/utils/request';
import { IMenuItem } from '@/typings/menu';

export const useMenuTable = () => {
  //获取全部菜单
  const getMenuList = async (params: any = null) => {
    let res = await request('/menu/table', 'get', params);
    return res.data;
  };

  //新增菜单
  const addMenu = async (data: IMenuItem) => {
    let res = await request('/menu', 'post', data);
    return res.data;
  };

  //修改菜单
  const editMenu = async (id: number, data: IMenuItem) => {
    let res = await request('/menu/' + id, 'put', data);
    return res.data;
  };

  const deleteMenu = (id: number) => {
    return request('/menu/' + id, 'delete');
  };

  return {
    getMenuList,
    addMenu,
    editMenu,
    deleteMenu,
  };
};
