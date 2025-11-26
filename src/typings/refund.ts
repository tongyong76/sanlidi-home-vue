export interface IRefundItem {
  id?: number;
  order_sn: string;
  order_id: number;
  user_id: number;
  refund_date: string;
  refund_price: number;
  refund_type: string;
  refund_status?: string;
  refund_delivery_name: string;
  refund_delivery_id: string;
  info: string;
}

export interface IRefundValidate {
  refund_date: string;
  refund_status: string;
}
