<template>
  <div class="layout-side-bar">
    <div class="left-icon">
      <SanLogo class="logo" />
      <ElScrollbar style="height: calc(100% - 135px)">
        <div class="icon-items">
          <div
            class="item"
            v-for="item in store.menuTree"
            :key="item.id"
            @click="handleMenuJump(item, true)"
          >
            <div
              :class="{ 'is-active': item.path === '/' + route.path.split('/')[1] }"
              :style="{ margin: '5px', height: '60px' }"
            >
              <i class="iconfont" :class="item.icon"></i>
              <span>{{ item.name }}</span>
            </div>
          </div>
        </div>
      </ElScrollbar>
    </div>
    <div class="left-menu">
      <ElScrollbar style="height: calc(100% - 10px)">
        <div class="header" style="background: rgb(255, 255, 255)">
          <p>San Admin</p>
        </div>
        <ElMenu
          class="menu-items"
          :collapse="!showMenu"
          :unique-opened="true"
          :default-openeds="defaultOpenedMenus"
          :show-timeout="50"
          :hide-timeout="50"
        >
          <SanSideMenusSub :list="menuList" @close="handleMenuClose" />
          <!-- <SidebarSubmenu
            :list="menuList"
            :isMobile="isMobileMode"
            :theme="getMenuTheme"
            @close="handleMenuClose"
          /> -->
        </ElMenu>
      </ElScrollbar>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, computed } from 'vue';
  import { useAppStore } from '@/store/main';
  import { useRoute } from 'vue-router';
  import { ElScrollbar } from 'element-plus';
  import { handleMenuJump } from '@/utils/jump';

  const store = useAppStore();
  const route = useRoute();

  const showMenu = ref(true);
  const defaultOpenedMenus = ref([]);

  // 一级菜单
  // const topMenuList = computed(() => {
  //   return store.menuTree;
  // });

  // 一级菜单的子菜单
  const menuList = computed(() => {
    const allMenus = store.menuTree;

    const currentTopPath = `/${route.path.split('/')[1]}`;
    const currentMenu = allMenus.find((menu) => menu.path === currentTopPath);

    return currentMenu?.children ?? [];
  });

  /**
   * 处理菜单关闭（来自子组件）
   */
  const handleMenuClose = (): void => {
    console.log('菜单关闭了');
  };
</script>

<style lang="scss" scoped>
  @use './style';
</style>
