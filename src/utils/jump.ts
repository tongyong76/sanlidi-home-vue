/**
 * 菜单跳转相关
 */
import { IMenuTree } from '@/typings/menu';
import { router } from '@/router';

// 打开外部链接
export const openExternalLink = (link: string) => {
  window.open(link, '_blank');
};

/**
 * 菜单跳转
 * @param item 菜单项
 * @param jumpToFirst 是否跳转到第一个子菜单
 * @returns
 */
export const handleMenuJump = (item: IMenuTree, jumpToFirst: boolean = false) => {
  // 如果不需要跳转到第一个子菜单，或者没有子菜单，直接跳转当前路径
  if (!jumpToFirst || !item.children?.length) {
    return router.push(item.path || '');
  }

  // 递归查找第一个可见的叶子节点菜单
  const findFirstLeafMenu = (items: IMenuTree[]): IMenuTree => {
    for (const child of items) {
      return child.children?.length ? findFirstLeafMenu(child.children) : child;
    }
    return items[0];
  };

  const firstChild = findFirstLeafMenu(item.children);

  // 如果第一个子菜单是外部链接则打开新窗口
  // if (firstChild.meta?.link) {
  //   return openExternalLink(firstChild.meta.link);
  // }

  // 跳转到子菜单路径
  router.push(firstChild.path || '');
};
