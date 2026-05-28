export interface IGoodsItem {}

export interface IGoodsCateItem {
  id: number;
  pid: number;
  name: string;
  pinyin: string;
  imgurl?: string;
  children?: IGoodsCateItem[];
}
