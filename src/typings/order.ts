export interface IGoodsItem {
  id: number;
  name: string;
  cost: number;
  count: number;
  pivot: IPivot;
}

interface IPivot {
  count: number;
}

export interface IOrderItem {
  id?: number;
  pt: string; // 平台
  sn: string; // 订单号
  snapshot: string; // 订单快照(json)
  order_date: string; // 下单时间精确到分
  order_price: number; // 订单金额
  order_status: number; // 订单状态 1: 待发货 2: 待收货 3: 已完成 4: 已售后     未支付的单子不用录入
  order_status_text?: string;
  order_cost?: number; // 订单成本
  order_profit?: number; // 订单利润
  order_profit_rate?: string; // 订单毛利率 利润/售价
  order_user?: string;
  order_zone?: string;
  order_address?: string;
  goods: IGoodsItem[]; // 商品列表
}

export interface IOrderDeliver {
  sendOrderTitle?: string;
  sendOrderId?: number;
  sendOrderSn?: string;
  sendOrderDeliveryName: string;
  sendOrderDeliveryId: string;
}
