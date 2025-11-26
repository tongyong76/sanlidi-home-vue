import { App } from 'vue';
import SanMagnifier from '@/components/Magnifier/index.vue';

import SanTab from './Tab/index.vue';
import SanModal from './Modal/index.vue';
import SanLoading from './Loading/index.vue';
import SanButton from './Button/index.vue';
import SanIcon from './sanui/base/san-icon/index.vue';
import SanLayouts from './sanui/layouts/san-layouts/index.vue';
import SanHeaderBar from './sanui/layouts/san-header-bar/index.vue';
import SanSideMenus from './sanui/layouts/san-side-menus/index.vue';
import SanSideMenusSub from './sanui/layouts/san-side-menus-sub/index.vue';
import SanPageContent from './sanui/layouts/san-page-content/index.vue';
import SanLogo from './sanui/base/san-logo/index.vue';
import SanException from './sanui/base/san-exception/index.vue';
import SanUcenter from './sanui/base/san-ucenter/index.vue';
import SanWorkTab from './sanui/layouts/san-work-tab/index.vue';
import SanSliderCheck from './sanui/forms/san-slider-check/index.vue';

let SanUI = <any>{};

SanUI.install = function (app: App): void {
  app.component('SanMagnifier', SanMagnifier);
  app.component('SanLogo', SanLogo);
  app.component('SanTab', SanTab);
  app.component('SanModal', SanModal);
  app.component('SanLoading', SanLoading);
  app.component('SanButton', SanButton);
  app.component('SanIcon', SanIcon);
  app.component('SanLayouts', SanLayouts);
  app.component('SanHeaderBar', SanHeaderBar);
  app.component('SanSideMenus', SanSideMenus);
  app.component('SanSideMenusSub', SanSideMenusSub);
  app.component('SanPageContent', SanPageContent);
  app.component('SanException', SanException);
  app.component('SanUcenter', SanUcenter);
  app.component('SanWorkTab', SanWorkTab);
  app.component('SanSliderCheck', SanSliderCheck);
};

export default SanUI;
