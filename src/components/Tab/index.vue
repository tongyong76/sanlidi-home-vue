<template>
  <div class="san-tab">
    <i class="iconfont icon-arrow-left"></i>
    <slot></slot>
    <i class="iconfont icon-arrow-right"></i>
  </div>
</template>

<script setup lang="ts"></script>

<style lang="scss" scoped>
.san-tab {
  width: calc(100% - 40px);
  height: 100%;
  padding-left: 20px;
  padding-right: 40px;
  color: #fff;
  display: flex;
  align-items: center;
  overflow: hidden;
  .iconfont {
    font-size: 20px;
    color: #909399;
    position: absolute;
    background-color: #f5f7fa;
    cursor: pointer;
    // display: none;
    &.icon-arrow-left {
      left: 10px;
    }
    &.icon-arrow-right {
      right: 82px;
    }
  }
  .tab-items {
    display: flex;
  }
}
</style>
