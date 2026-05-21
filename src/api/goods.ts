import request from '@/utils/request';

export function getIndexTj(): Promise<any[]> {
  return request('goods/tj', 'get', '');
}

export function getIndexHot(): Promise<any[]> {
  return request('goods/hot', 'get', '');
}

export function getIndexFloor(): Promise<{ chujing: any[]; guonei: any[]; zhoubian: any[] }> {
  return request('goods/floor', 'get', '');
}
