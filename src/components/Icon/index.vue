<template>
  <i
    class="iconfont"
    :class="'icon-' + name"
    :style="{ fontSize: `${size}px`, color }"
    @click="clickHandle"
  ></i>
</template>

<script setup lang="ts">
const props = defineProps({
  // 图标名称
  name: {
    type: String,
    default: '',
  },
  // 图标颜色
  color: {
    type: String,
    default: '',
  },
  // 图标大小
  size: {
    type: [String, Number],
    default: 20,
  },
});
const emits = defineEmits(['click']);

const clickHandle = () => {
  emits('click', props.name);
};
</script>

<style lang="scss" scoped>
//@import '@/assets/iconfont/iconfont.css';
.iconfont {
  cursor: pointer;
}
</style>
