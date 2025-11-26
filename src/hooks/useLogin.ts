import request from '@/utils/request';
import { IMenuTree } from '@/typings/menu';

export const useLogin = () => {
  // 获取管理员信息
  const getAdminInfo = async () => {
    // GET a/admin
    let res = await request('/admin', 'get');
    return res.data;
  };

  const getMenuTree = async () => {
    // GET a/menu/active
    let res = await request('/menu/active', 'get');
    return res.data;
  };

  const unfoldTree = (menus: IMenuTree[]) => {
    // 把树状菜单展开成ids集合
    let ids: string[] = [];
    menus.forEach((node: any) => {
      ids.push(node.id);
      if (node.children && node.children.length > 0) {
        ids = ids.concat(unfoldTree(node.children)); // 递归调用，并传递当前节点的id作为父ID
      }
    });
    return ids;
  };

  return {
    getAdminInfo,
    getMenuTree,
    unfoldTree,
  };
};
