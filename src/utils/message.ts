import { ElMessage, ElMessageBox } from 'element-plus';

export default class Message {
  static success(message: string): void {
    this.message(message, 'success');
  }

  static error(message: string): void {
    this.message(message, 'error');
  }

  static warning(message: string): void {
    this.message(message, 'warning');
  }

  static confirm(message: string, callback: any): void {
    ElMessageBox.confirm(message, '提示', {
      confirmButtonText: '确认',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(callback);
  }

  // 定义一个静态方法，用于显示消息
  protected static message(message: string, type: any) {
    ElMessage({ message, type });
  }
}
