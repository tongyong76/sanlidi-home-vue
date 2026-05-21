import request from '@/utils/request';

export function getIndexNews(): Promise<any[]> {
  return request('article/index', 'get', '');
}
