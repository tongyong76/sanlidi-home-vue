/*
 ************************************************
 *
 * 菜单city table - 相关操作
 * Author: GuWenjun
 * Time: 2025-06-23
 *
 * ***********************************************
 */

import request from '@/utils/request';
import { ICity } from '@/typings/city';

export const useCity = () => {
  // 获取city树
  const getCityList = async () => {
    let res = await request('/city/table', 'get', '');
    return res.data;
  };

  // 根据id获取city信息
  const getCityInfo = async (id: number): Promise<ICity> => {
    let res = await request('/city/' + id, 'get');
    return res.data;
  };

  // 新增
  const addCity = async (data: ICity) => {
    let res = await request('/city', 'post', data);
    return res.data;
  };

  // 修改
  const editCity = async (id: number, data: ICity) => {
    let res = await request('/city/' + id, 'put', data);
    return res.data;
  };

  // 删除
  const deleteCity = (id: number) => {
    return request('/city/' + id, 'delete');
  };

  return {
    getCityList,
    getCityInfo,
    addCity,
    editCity,
    deleteCity,
  };
};
