<template>
  <div class="index-page">
    <!--banner-->
    <div class="index-banner">
      <Banner :items="banners"></Banner>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import { router } from '@/router';
  import Banner from '@/views/Components/Banner/index.vue';
  import { getIndexBanner } from '@/api/banner';
  import { IBannerItem } from '@/types/banner';

  const banners = ref<IBannerItem[]>([]);

  onMounted(async () => {
    console.log('111111111');
    banners.value = await getIndexBanner();
    // banners.value = banner;
  });
</script>

<style lang="scss" scoped></style>
