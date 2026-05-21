import request from '@/utils/request';

export function getAd(): Promise<any[]> {
  return request('ad/getAll', 'get', '');
}
