import request from '@/utils/request';

export function getIndexHotCate(): Promise<any[]> {
  return request('cate/hot', 'get', '');
}
