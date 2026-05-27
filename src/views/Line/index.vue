<template>
  <div>Line</div>
</template>

<script setup lang="ts">
  import { useRoute } from 'vue-router';

  const route = useRoute();

  // 获取 params 参数
  const id = route.params.id;

  console.log('线路详情页 ID:', id);
</script>

<style lang="scss" scoped></style>
