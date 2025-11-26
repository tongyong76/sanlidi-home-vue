// edit by Gwj 20250523
export interface IMenuItem {
  id: number;
  pid: number;
  name: string;
  icon: string;
  path: string;
  sort: number;
  is_enable?: boolean;
  children?: IMenuItem[];
}

export interface IMenuTree extends Partial<IMenuItem> {
  children?: IMenuItem[];
}

export interface IHistoryMenu {
  id: number;
  iidx: number;
  name: string;
  icon: string;
  link: string;
}
