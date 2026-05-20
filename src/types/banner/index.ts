export interface IBannerItem {
  /** 图片地址（必填） */
  imgurl: string;
  /** 点击跳转链接（可选） */
  link?: string;
  /** 图片 alt 描述（可选） */
  name?: string;
  title?: string;
  children?: IBannerItem[];
}
