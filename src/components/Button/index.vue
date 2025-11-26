<template>
  <div class="san-button" :class="[size ? size : '', type ? type : '']">
    <i class="iconfont" :class="icon ? icon : ''"></i>
    <span><slot></slot></span>
  </div>
</template>

<script setup lang="ts">
defineProps({
  icon: {
    type: String,
    default: '',
  },
  size: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: '',
  },
});
</script>

<style lang="scss" scoped>
.san-button {
  display: inline-block;
  padding: 8px 15px;
  background-color: #fff;
  border: 1px solid #dedfe6;
  border-radius: 4px;
  line-height: 1;
  font-size: 14px;
  cursor: pointer;
  ~ :last-child {
    margin-right: 0;
  }
  &.large {
    padding: 12px 19px;
    height: 40px;
  }
  &.small {
    padding: 5px 11px;
    height: 24px;
    font-size: 12px;
  }
  &:hover {
    color: #409eff;
    border-color: #c6e2ff;
    background-color: #ecf5ff;
  }
  &:active {
    border-color: #409eff;
    background-color: #ecf5ff;
  }
  &.primary {
    color: #fff;
    border-color: #409eff;
    background-color: #409eff;
    &:hover {
      border-color: #66b1ff;
      background-color: #66b1ff;
    }
    &:active {
      border-color: #3a8ee6;
      background-color: #3a8ee6;
    }
  }
  &.danger {
    color: #fff;
    border-color: #f56c6c;
    background-color: #f56c6c;
    &:hover {
      border-color: #f78989;
      background-color: #f78989;
    }
    &:active {
      border-color: #d36161;
      background-color: #d36161;
    }
  }
  &.warning {
    color: #fff;
    border-color: #e6a23c;
    background-color: #e6a23c;
    &:hover {
      border-color: #ebb563;
      background-color: #ebb563;
    }
    &:active {
      border-color: #cf9236;
      background-color: #cf9236;
    }
  }
  &.success {
    color: #fff;
    border-color: #67c23a;
    background-color: #67c23a;
    &:hover {
      border-color: #85ce61;
      background-color: #85ce61;
    }
    &:active {
      border-color: #5daf34;
      background-color: #5daf34;
    }
  }
  &.info {
    color: #fff;
    border-color: #909399;
    background-color: #909399;
    &:hover {
      border-color: #a6a9ad;
      background-color: #a6a9ad;
    }
    &:active {
      border-color: #82848a;
      background-color: #82848a;
    }
  }
  .iconfont {
    font-size: 12px;
  }
  span {
    margin-left: 3px;
  }
}
</style>
