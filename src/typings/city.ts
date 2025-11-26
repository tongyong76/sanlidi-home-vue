export interface ICity {
  id: number;
  pid: number;
  pname: string;
  plevel: number;
  pmname: string;
  name: string;
  level: number;
  merger_name?: string;
  lng?: number;
  lat?: number;
  is_enable?: boolean;
  children?: ICity[];
}
