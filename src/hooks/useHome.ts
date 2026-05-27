import { ref } from 'vue';
import { getIndexBanner } from '@/api/banner';
import { getIndexTj, getIndexHot, getIndexFloor } from '@/api/goods';
import { getIndexHotCate } from '@/api/index';
import { getIndexNews } from '@/api/article';
import { getAd } from '@/api/ad';
import { IBannerItem } from '@/types/banner';
import { IGoodsCateItem } from '@/types/goods';

export function useHome() {
  const banners = ref<IBannerItem[]>([]);
  const tj = ref<any[]>([]);
  const hotLine = ref<any[]>([]);
  const news = ref<any[]>([]);
  const ads = ref<any>({});
  const floor = ref<{ chujing: any[]; guonei: any[]; zhoubian: any[] }>();
  const hotCate = ref<IGoodsCateItem[]>([]);
  const loading = ref(true);
  const error = ref<unknown>(null);

  const loadData = async () => {
    try {
      const [bannersData, tjData, hotLineData, newsData, adsData, floorData, hotCateData] =
        await Promise.all([
          getIndexBanner().catch((e) => {
            console.warn('Failed to load banners', e);
            return [];
          }),
          getIndexTj().catch((e) => {
            console.warn('Failed to load tj', e);
            return [];
          }),
          getIndexHot().catch((e) => {
            console.warn('Failed to load hotLine', e);
            return [];
          }),
          getIndexNews().catch((e) => {
            console.warn('Failed to load news', e);
            return [];
          }),
          getAd().catch((e) => {
            console.warn('Failed to load ads', e);
            return {};
          }),
          getIndexFloor().catch((e) => {
            console.warn('Failed to load floor data', e);
            return { chujing: [], guonei: [], zhoubian: [] };
          }),
          getIndexHotCate().catch((e) => {
            console.warn('Failed to load hot categories', e);
            return [];
          })
        ]);
      banners.value = bannersData;
      tj.value = tjData;
      hotLine.value = hotLineData;
      news.value = newsData;
      ads.value = adsData;
      floor.value = floorData;
      hotCate.value = hotCateData;
    } catch (err: unknown) {
      error.value = err;
    } finally {
      loading.value = false;
    }
  };

  return { banners, tj, hotLine, news, ads, floor, hotCate, loading, error, loadData };
}
