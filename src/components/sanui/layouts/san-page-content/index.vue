<template>
  <div class="layout-page-content">
    <router-view v-if="isPageShow" />
  </div>
</template>

<script setup lang="ts">
  import { ref, watch, nextTick } from 'vue';
  import { useAppStore } from '@/store/main';
  import { storeToRefs } from 'pinia';
  const store = useAppStore();
  const { refresh } = storeToRefs(store);

  const isPageShow = ref(true);

  const reload = () => {
    isPageShow.value = false;
    nextTick(() => {
      isPageShow.value = true;
    });
  };

  watch(refresh, reload, { flush: 'post' });
</script>

<style lang="scss" scoped>
  .layout-page-content {
    box-sizing: border-box;
    width: calc(100% - 40px);
    margin-top: 20px !important;
    margin: auto;
  }
</style>
