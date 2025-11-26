<template>
  <i
    class="iconfont"
    :class="props.name"
    :style="{ fontSize: `${props.size}px`, color: `${props.color ? props.color : undefined}` }"
  ></i>
</template>

<script setup lang="ts">
  const props = defineProps({
    name: {
      type: String,
      default: ''
    },
    size: {
      type: Number,
      default: 16
    },
    color: {
      type: String,
      default: ''
    }
  });
</script>

<style lang="scss" scoped></style>
