<template>
  <div style="height: 600px; background-color: brown">Index33</div>
  <button @click="goLine">go line</button>
</template>

<script setup lang="ts">
  import { router } from '@/router';

  const goLine = () => {
    router.push('/line');
  };
</script>

<style lang="scss" scoped></style>
