/*
 ************************************************
 *
 * 订单统计
 * Author: GuWenjun
 * Time: 2025-08-26
 *
 * ***********************************************
 */

import request from '@/utils/request';

export const useStatistic = () => {
  // 获取订单统计卡片
  const getOrderBasic = async (params: any = null) => {
    let res = await request('/statistic/order/basic', 'get', params);
    return res.data;
  };

  const getOrderTrend = async (params: any = null) => {
    let res = await request('/statistic/order/trend', 'get', params);
    return res.data;
  };

  const getOrderAccumulate = async (params: any = null) => {
    let res = await request('/statistic/order/accumulate', 'get', params);
    return res.data;
  };

  const getGoodsRank = async (params: any = null) => {
    let res = await request('/statistic/goods/rank', 'get', params);
    return res.data;
  };

  return {
    getOrderBasic,
    getOrderTrend,
    getOrderAccumulate,
    getGoodsRank
  };
};
