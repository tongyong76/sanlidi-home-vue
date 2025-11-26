export interface IGoods {
  id?: number;
  cate_id: number;
  name: string;
  imgurl: string;
  cost: number;
  sort: number;
  is_enable: boolean;
  count?: number;
  pivot?: {
    count: number;
  };
}
