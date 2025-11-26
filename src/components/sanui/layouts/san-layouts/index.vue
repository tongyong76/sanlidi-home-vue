<template>
  <div class="layouts" :style="layoutStyle">
    <slot></slot>
  </div>
</template>

<script setup lang="ts">
  import { watchEffect, computed } from 'vue';

  // 菜单宽度变化
  watchEffect(() => {
    // const isOpen = menuOpen.value;
    // const width = isOpen ? settingStore.getMenuOpenWidth : MenuWidth.CLOSE;
    // menuStore.setMenuWidth(width);
  });

  // 布局样式
  const layoutStyle = computed(() => ({
    paddingLeft: paddingLeft.value,
    paddingTop: paddingTop.value
  }));

  // 左侧距离
  const paddingLeft = computed(() => {
    const isOpen = true;
    const width = isOpen ? `240px` : `80px`;

    return `calc(${width} + 80px)`;
  });

  // 顶部距离
  const paddingTop = computed(() => {
    return `106px`;
  });
</script>

<style lang="scss" scoped>
  .layouts {
    background: var(--bg-gray);
    transition: padding 0.3s ease-in-out;
    width: 100%;
    height: 100vh;
    overflow: hidden;
  }
</style>
