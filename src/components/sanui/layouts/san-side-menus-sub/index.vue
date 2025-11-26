<template>
  <template v-for="item in props.list" :key="item" :index="item.path" level="1">
    <!-- 包含子菜单的项目 -->
    <ElSubMenu v-if="hasChildren(item)" index="item.id">
      <template #title>
        <!-- <SanIcon :name="item.icon" color="#000" /> -->
        <span class="menu-name">
          {{ item.name }}
        </span>
        <!-- <div class="art-badge" style="right: 10px" /> -->
      </template>

      <SanSideMenusSub :list="item.children" @close="closeMenu" />
    </ElSubMenu>

    <!-- 普通菜单项 -->
    <ElMenuItem
      v-else
      :index="item.path"
      @click="goPage(item)"
      :class="{ 'is-active': item.path === route.path }"
    >
      <!-- <SanIcon :name="item.icon" color="#000" /> -->
      <!-- <div class="art-badge" style="right: 5px" /> -->

      <template #title>
        <span class="menu-name"> {{ item.name }} </span>
        <!-- <div class="art-badge" />
        <div class="art-text-badge"> 22 </div> -->
      </template>
    </ElMenuItem>
  </template>
</template>

<script setup lang="ts">
  import { onMounted, PropType } from 'vue';
  import { IMenuTree } from '@/typings/menu';
  import { handleMenuJump } from '@/utils/jump';
  import { useRoute } from 'vue-router';

  const route = useRoute();

  const props = defineProps({
    list: {
      type: Array as PropType<IMenuTree[]>,
      default: []
    },
    index: {
      type: String,
      default: ''
    }
  });

  const emit = defineEmits(['close']);

  onMounted(() => {
    console.log(props.list);
  });

  /**
   * 跳转到指定页面
   * @param item 菜单项数据
   */
  const goPage = (item: IMenuTree) => {
    closeMenu();
    handleMenuJump(item);
  };

  /**
   * 关闭菜单
   * 触发父组件的关闭事件
   */
  const closeMenu = (): void => {
    emit('close');
  };

  /**
   * 递归过滤菜单路由，移除隐藏的菜单项
   * 如果一个父菜单的所有子菜单都被隐藏，则父菜单也会被隐藏
   * @param items 菜单项数组
   * @returns 过滤后的菜单项数组
   */
  // const filterRoutes = (items: IMenuTree[]): IMenuTree[] => {
  //   return items
  //     .filter((item) => {
  //       // 如果当前项被隐藏，直接过滤掉
  //       // if (item.meta.isHide) {
  //       //   return false;
  //       // }

  //       // 如果有子菜单，递归过滤子菜单
  //       if (item.children && item.children.length > 0) {
  //         const filteredChildren = filterRoutes(item.children);
  //         // 如果所有子菜单都被过滤掉了，则隐藏父菜单
  //         return filteredChildren.length > 0;
  //       }

  //       // 叶子节点且未被隐藏，保留
  //       return true;
  //     })
  //     .map((item) => ({
  //       ...item,
  //       children: item.children ? filterRoutes(item.children) : undefined
  //     }));
  // };

  /**
   * 判断菜单项是否包含可见的子菜单
   * @param item 菜单项数据
   * @returns 是否包含可见的子菜单
   */
  const hasChildren = (item: IMenuTree): boolean => {
    if (!item.children || item.children.length === 0) {
      return false;
    }
    // 递归检查是否有可见的子菜单
    //const filteredChildren = filterRoutes(item.children);
    // return filteredChildren.length > 0;
    return true;
  };
</script>

<style lang="scss" scoped>
  :deep(.el-sub-menu__title),
  .el-menu-item {
    width: calc(100% - 16px);
    height: 46px !important;
    line-height: 46px !important;
    margin-left: 8px;
    margin-bottom: 4px;
    border-radius: 6px;

    span {
      font-size: 14px !important;
    }

    &:hover {
      background-color: var(--art-gray-200) !important;
    }

    &.is-active {
      background-color: var(--el-color-primary-light-9) !important;
    }

    .menu-icon {
      margin-left: -4px;
    }
  }

  // 右侧箭头
  :deep(.el-sub-menu__icon-arrow) {
    width: 13px !important;
    font-size: 13px !important;
  }
</style>
