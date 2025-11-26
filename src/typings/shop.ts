export interface IShop {
  id: number;
  name: string;
  address: string;
  latitude?: number;
  longitude?: number;
  gps: string;
  image_shop?: string;
  image_food?: string;
  park?: string;
  phone_shop: string;
  phone_manager: string;
  manager: string;
}

export interface IRoom {
  id: number;
  name: string;
  shop_id: number | null;
}

export interface IShopTree {
  id: number;
  name: string;
  children: IRoom[];
}
