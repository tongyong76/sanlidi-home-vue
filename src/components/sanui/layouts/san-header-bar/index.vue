<template>
  <div class="layout-header-bar" :style="{ width: 'calc(100% - 320px)' }">
    <div class="top-menu">
      <div class="left">
        <div class="btn-box">
          <i class="iconfont icon-menu"></i>
        </div>
        <div class="btn-box">
          <i class="iconfont icon-refresh" @click="reload()"></i>
        </div>
      </div>
      <div class="right">
        <div class="btn-box">
          <i class="iconfont icon-refresh" @click="reload()"></i>
        </div>
        <div class="btn-box">
          <i class="iconfont icon-full" @click="toggleFullScreen()"></i>
        </div>
        <san-ucenter />
      </div>
    </div>
    <SanWorkTab />
  </div>
</template>

<script setup lang="ts">
  import { useAppStore } from '@/store/main';
  import { useFullscreen } from '@vueuse/core';

  const store = useAppStore();
  const { toggle } = useFullscreen();

  /**
   * 切换全屏状态
   */
  const toggleFullScreen = (): void => {
    toggle();
  };

  /**
   * 刷新页面
   * @param {number} time - 延迟时间，默认为0毫秒
   */
  const reload = (time: number = 0): void => {
    setTimeout(() => {
      store.reload();
    }, time);
  };
</script>

<style lang="scss" scoped>
  @use './style';
</style>
