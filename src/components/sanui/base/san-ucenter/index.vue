<template>
  <div class="ucenter">
    <ElPopover
      ref="userMenuPopover"
      placement="bottom-end"
      :width="240"
      :hide-after="0"
      :offset="10"
      trigger="hover"
      :show-arrow="false"
      popper-class="user-menu-popover"
      popper-style="border: 1px solid var(--art-border-dashed-color); border-radius: calc(var(--custom-radius) / 2 + 4px); padding: 5px 16px; 5px 16px;"
    >
      <template #reference>
        <img class="cover" src="@images/svg/avatar.webp" alt="avatar" />
      </template>
      <template #default>
        <div class="user-menu-box">
          <div class="user-head">
            <img class="cover" src="@images/svg/avatar.webp" style="float: left" />
            <div class="user-wrap">
              <span class="name">admin</span>
              <span class="email">art.design@gmail.com</span>
            </div>
          </div>
          <ul class="user-menu">
            <li @click="goPage('111')">
              <i class="menu-icon iconfont-sys">&#xe734;</i>
              <span class="menu-txt">菜单一</span>
            </li>
            <li @click="goPage('222')">
              <i class="menu-icon iconfont-sys" style="font-size: 15px">&#xe828;</i>
              <span class="menu-txt">菜单二</span>
            </li>
            <li @click="goPage('333')">
              <i class="menu-icon iconfont-sys">&#xe8d6;</i>
              <span class="menu-txt">菜单三</span>
            </li>
            <li @click="goPage('444')">
              <i class="menu-icon iconfont-sys">&#xe817;</i>
              <span class="menu-txt">菜单四</span>
            </li>
            <div class="line"></div>
            <div class="logout-btn" @click="logout"> 退出 </div>
          </ul>
        </div>
      </template>
    </ElPopover>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted, PropType } from 'vue';
  import { ElMessageBox } from 'element-plus';
  import { useAppStore } from '@/store/main';
  import {} from 'vue';

  const userMenuPopover = ref();
  const store = useAppStore();

  interface IUserInfo {
    avatar: string;
    name: string;
  }

  const props = defineProps({
    userInfo: {
      type: Object as PropType<IUserInfo>,
      default: () => ({
        avatar: '',
        name: ''
      })
    },
    menus: {}
  });

  onMounted(() => {
    console.log(props.userInfo);
  });

  const goPage = (path: string) => {
    console.log('点击了菜单', path);
  };

  const logout = (): void => {
    closeUserMenu();
    setTimeout(() => {
      ElMessageBox.confirm('您是否要退出登录?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        customClass: 'san-dialog'
      }).then(() => {
        store.logout();
      });
    }, 200);
  };

  /**
   * 关闭用户菜单弹出层
   */
  const closeUserMenu = (): void => {
    setTimeout(() => {
      userMenuPopover.value.hide();
    }, 100);
  };
</script>

<style lang="scss" scoped>
  @use './style';
</style>
