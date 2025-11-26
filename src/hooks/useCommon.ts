// 通用函数
export function useCommon() {
  // 刷新页面
  const refresh = () => {
    console.log('useCommon refresh');
  };

  return {
    refresh
  };
}
