<template>
  <div class="work-tab">
    <div class="scroll-view">
      <ul class="tabs">
        <li class="active"> 工作台 </li>
        <li>
          卡片1
          <el-icon>
            <Close />
          </el-icon>
        </li>
        <li>
          卡片2
          <el-icon>
            <Close />
          </el-icon>
        </li>
      </ul>
    </div>
    <div class="right">
      <el-dropdown trigger="click" placement="bottom-start">
        <span class="el-dropdown-link">
          <el-icon class="btn console-box art-custom-card">
            <ArrowDown />
          </el-icon>
        </span>
        <template #dropdown>
          <el-dropdown-menu>
            <el-dropdown-item :icon="ArrowLeft">关闭左侧</el-dropdown-item>
            <el-dropdown-item :icon="ArrowRight"> 关闭右侧 </el-dropdown-item>
            <el-dropdown-item :icon="Close">关闭其他</el-dropdown-item>
            <el-dropdown-item :icon="CircleClose">关闭全部</el-dropdown-item>
            <el-dropdown-item :icon="Refresh"> 刷新</el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
    </div>
    <!-- <ArtMenuRight
      ref="menuRef"
      :menu-items="menuItems"
      :menu-width="140"
      :border-radius="10"
      @select="handleSelect"
    /> -->
  </div>
</template>

<script setup lang="ts">
  import {
    Close,
    ArrowDown,
    ArrowLeft,
    ArrowRight,
    CircleClose,
    Refresh
  } from '@element-plus/icons-vue';

  // const showMenu = (e: MouseEvent, path?: string) => {
  //   clickedPath.value = path || '';
  //   menuRef.value?.show(e);
  //   e.preventDefault();
  //   e.stopPropagation();
  // };

  // const handleSelect = () => {
  //   console.log('handleSelect');
  // };
</script>

<style lang="scss" scoped>
  @use './style';
</style>
