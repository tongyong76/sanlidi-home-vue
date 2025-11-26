<template>
  <div
    ref="sliderCheck"
    class="slider-check"
    v-if="!reset"
    @mousemove="handleMove"
    @mouseup="handleFinish"
    @mouseleave="handleFinish"
  >
    <div ref="sliderCheckText" class="slider-check-text" :class="isOk ? 'success' : ''">{{
      isOk ? successText : startText
    }}</div>
    <div ref="sliderCheckBg" class="slider-check-bg"></div>
    <i
      @mousedown="handleStart"
      ref="sliderCheckIcon"
      class="iconfont"
      :class="isOk ? successIcon : startIcon"
    ></i>
  </div>
</template>

<script setup lang="ts">
  import { ref, nextTick } from 'vue';

  defineProps({
    //成功图标
    successIcon: {
      type: String,
      default: 'icon-done'
    },
    //成功文字
    successText: {
      type: String,
      default: '验证成功'
    },
    //开始的图标
    startIcon: {
      type: String,
      default: 'icon-arrow-right-double'
    },
    //开始的文字
    startText: {
      type: String,
      default: '请拖动滑块验证'
    },
    //或者用值来进行监听
    loginStatus: {
      type: Boolean,
      default: false
    }
  });

  const emit = defineEmits(['sliderCheckSuccess', 'sliderCheckFail']);

  const reset = ref(false);
  const disX = ref(0);
  const rangeStatus = ref(false);

  // DOM
  const sliderCheck = ref();
  const sliderCheckIcon = ref();
  const sliderCheckText = ref();
  const sliderCheckBg = ref();

  // 初始化状态
  const isMoveing = ref(false);
  const isOk = ref(false);
  const positionX = ref(0);
  const maxX = ref(0);
  const startX = ref(0);

  // 触摸事件变量 - 用于禁止页面滑动

  // 处理移动事件
  const handleMove = (e: MouseEvent): void => {
    if (!isOk.value && isMoveing.value) {
      positionX.value = e.clientX;
      disX.value = positionX.value - startX.value;
      if (disX.value <= 0) {
        disX.value = 0;
      }
      if (disX.value >= maxX.value) {
        //减去滑块的宽度,体验效果更好
        disX.value = maxX.value;
      }
      sliderCheckIcon.value.style.transition = '0s all';
      sliderCheckIcon.value.style.transform = 'translateX(' + disX.value + 'px)';
      sliderCheckBg.value.style.transition = '0s all';
      sliderCheckBg.value.style.width = disX.value + 'px';
    }
    e.preventDefault();
  };

  const handleFinish = (e: MouseEvent | TouchEvent) => {
    if (!isOk.value) {
      isMoveing.value = false;
      if (maxX.value && positionX.value - startX.value >= maxX.value) {
        isOk.value = true;
        emit('sliderCheckSuccess');
      } else {
        sliderCheckIcon.value.style.transition = '.5s all';
        sliderCheckIcon.value.style.transform = 'translateX(0)';
        sliderCheckBg.value.style.transition = '.5s all';
        sliderCheckBg.value.style.width = 0 + 'px';
        positionX.value = 0;
        emit('sliderCheckFail');
      }
    }
    e.preventDefault();
  };

  // 滑动开始
  const handleStart = (e: MouseEvent): void => {
    if (positionX.value) {
      return;
    }
    if (!isOk.value) {
      isMoveing.value = true;
      positionX.value = 0;
      sliderCheckIcon.value.style.transition = 'none';
      startX.value = e.clientX;
      maxX.value = sliderCheck.value.offsetWidth - sliderCheckIcon.value.offsetWidth;
    }
  };

  // reset
  const resetFun = () => {
    reset.value = true;
    nextTick(() => {
      //写入操作
      rangeStatus.value = false;
      reset.value = false;
    });
  };

  defineExpose({
    resetFun
  });
</script>

<style lang="scss" scoped>
  .slider-check {
    background-color: #e9e9e9;
    position: relative;
    transition: 1s all;
    user-select: none;
    color: #585858;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    .slider-check-bg {
      z-index: 1;
      position: absolute;
      left: 0;
      width: 0;
      height: 100%;
      background-color: #57d187;
      border: 1px solid #d8d8d8;
    }
    .slider-check-text {
      z-index: 2;
      position: absolute;
      left: 0;
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      &.success {
        color: #ffffff;
      }
    }
    &.success {
      background-color: #1890ff;
      color: #f5f7fa;
      i {
        color: #1890ff;
      }
    }
    i {
      z-index: 3;
      position: absolute;
      left: 0;
      width: 50px;
      height: 100%;
      color: #1890ff;
      background-color: #f5f7fa;
      border: 1px solid #d8d8d8;
      cursor: move;
      font-size: 24px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
  }
</style>
