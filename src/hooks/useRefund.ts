import request from '@/utils/request';
import { IRefundItem } from '@/typings/refund';

export const useRefund = () => {
  const getRefundList = async (params: any = null) => {
    let res = await request('/refund/list', 'get', params);
    return res.data;
  };

  const newRefund = async (data: IRefundItem) => {
    let res = await request('/refund/add', 'post', data);
    return res.data;
  };

  const updateRefund = async (id: number, data: IRefundItem) => {
    let res = await request('/refund/' + id + '/update', 'put', data);
    return res.data;
  };

  return {
    getRefundList,
    newRefund,
    updateRefund,
  };
};
