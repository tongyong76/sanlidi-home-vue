import request from '@/utils/request';
import { IBannerItem } from '@/types/banner';

export function getIndexBanner(): Promise<IBannerItem[]> {
  return request('banner/index', 'get', '');
}
