import request from '@/utils/request';
import { IOrderItem, IOrderDeliver } from '@/typings/order';

export const useOrderTable = () => {
  const getOrderList = async (params: any = null) => {
    let res = await request('/order/list', 'get', params);
    return res.data;
  };

  const addOrder = async (data: IOrderItem) => {
    let res = await request('/order', 'post', data);
    return res.data;
  };

  const editOrder = async (id: number, data: IOrderItem) => {
    let res = await request('/order/' + id, 'put', data);
    return res.data;
  };

  const getGoodsList = async () => {
    let res = await request('/goods/list', 'get');
    return res;
  };

  const deleteOrder = async (id: number) => {
    return request('/order/' + id, 'delete');
  };

  // 订单搜索
  const getOrderFilter = async (data: any) => {
    let res = await request('/order/filter', 'get', data);
    return res.data;
  };

  // 订单发货
  const setOrderDeliver = async (id: number, data: IOrderDeliver) => {
    return request('/order/' + id + '/deliver', 'put', data);
  };

  // 获取订单快递信息
  const getOrderDeliver = async (id: number) => {
    const res = await request('/order/' + id + '/deliver', 'get');
    if (res.code === 0) {
      return res.data;
    }
  };

  // 订单签收
  const setOrderDone = async (id: number) => {
    return request('/order/' + id + '/done', 'put');
  };

  // 取消订单
  const setOrderCancel = async (id: number) => {
    return request('/order/' + id + '/cancel', 'put');
  };

  return {
    getOrderList,
    addOrder,
    editOrder,
    getGoodsList,
    deleteOrder,
    getOrderFilter,
    setOrderDeliver,
    getOrderDeliver,
    setOrderDone,
    setOrderCancel,
  };
};
