/*
 ************************************************
 *
 * 商品goods - 相关操作
 * Author: GuWenjun
 * Time: 2025-06-26
 *
 * ***********************************************
 */
import request from '@/utils/request';
import { IGoods } from '@/typings/goods';

export const useGoods = () => {
  const getGoodsList = async (params: any = null) => {
    let res = await request('/goods/list', 'get', params);
    return res.data;
  };

  const getGoodsAll = async () => {
    let res = await request('/goods/all', 'get');
    return res;
  };

  // 新增
  const addGoods = async (data: IGoods) => {
    let res = await request('/goods', 'post', data);
    return res.data;
  };

  // 修改
  const editGoods = async (id: number, data: IGoods) => {
    let res = await request('/goods/' + id, 'put', data);
    return res.data;
  };

  // 删除
  const deleteGoods = (id: number) => {
    return request('/goods/' + id, 'delete');
  };

  return {
    getGoodsList,
    getGoodsAll,
    addGoods,
    editGoods,
    deleteGoods,
  };
};
