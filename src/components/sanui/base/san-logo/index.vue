<template>
  <div class="san-logo">
    <img :style="logoStyle" src="@images/common/logo.webp" alt="logo" />
  </div>
</template>

<script setup lang="ts">
  import { computed } from 'vue';

  const props = defineProps({
    size: {
      type: Number,
      default: 36
    }
  });

  const logoStyle = computed(() => ({ width: `${props.size}px` }));
</script>

<style lang="scss" scoped>
  .san-logo {
    display: flex;
    align-items: center;
    justify-content: center;

    img {
      width: 100%;
      height: 100%;
    }
  }
</style>
