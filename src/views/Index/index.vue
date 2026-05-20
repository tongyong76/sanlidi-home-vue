<template>
  <div class="index-page">
    <!--banner-->
    <div class="index-banner">
      <Banner :items="banners"></Banner>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { router } from '@/router';
  import Banner from '@/views/Components/Banner/index.vue';

  interface BannerItem {
    /** 图片地址（必填） */
    imageUrl: string;
    /** 点击跳转链接（可选） */
    link?: string;
    /** 图片 alt 描述（可选） */
    alt?: string;
  }

  const banners: BannerItem[] = [
    {
      imageUrl: 'http://1235',
      link: '123',
      alt: 'abc'
    },
    {
      imageUrl: 'http://1235',
      link: '123',
      alt: 'abc'
    }
  ];
</script>

<style lang="scss" scoped></style>
