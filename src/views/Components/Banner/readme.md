# Banner 轮播图组件

## 概述

一个基于 Vue 3 构建的轻量级、可配置的轮播图组件。支持图片链接跳转、自动轮播、手动切换和响应式布局。

## 组件属性 (Props)

| 属性名     | 类型            | 默认值   | 描述                     |
| ---------- | --------------- | -------- | ------------------------ |
| `items`    | `IBannerItem[]` | **必填** | 轮播图数据列表           |
| `autoplay` | `boolean`       | `true`   | 是否自动轮播             |
| `interval` | `number`        | `3000`   | 自动轮播间隔时间（毫秒） |

### IBannerItem 类型定义

```typescript
interface IBannerItem {
  imgurl: string; // 图片路径（自动拼接 IMAGE_URL）
  link?: string; // 跳转链接（可选）
  alt?: string; // 图片 alt 描述（可选）
}
```

## 组件事件 (Emits)

| 事件名 | 参数            | 描述                         |
| ------ | --------------- | ---------------------------- |
| change | (index: number) | 当前展示的图片索引改变时触发 |

## 使用方法

### 基础用法

```vue
<template><Banner :items="bannerList" /></template>

<script setup>
  import Banner from '@/components/Banner.vue';

  const bannerList = [
    { imgurl: '/image1.jpg', link: 'https://example.com/1', alt: '促销活动' },
    { imgurl: '/image2.jpg', alt: '产品展示' },
    { imgurl: '/image3.jpg', link: 'https://example.com/3' }
  ];
</script>
```

### 关闭自动轮播

```vue
<Banner :items="bannerList" :autoplay="false" />
```

### 自定义轮播间隔（5秒）

```vue
<Banner :items="bannerList" :interval="5000" />
```

### 监听索引变化

```vue
<Banner :items="bannerList" @change="handleBannerChange" />
```

## 功能特性

- 自动轮播：支持配置是否自动播放及间隔时长

- 手动切换：左右箭头按钮进行上一张/下一张切换（仅多图时显示）

- 指示器跳转：点击底部圆点快速跳转到指定图片（仅多图时显示）

- 边界循环：首尾无缝循环切换

- 鼠标悬停暂停：鼠标悬停时自动暂停轮播，移出后恢复

- 响应式适配：移动端自动调整按钮和指示器尺寸

- 链接跳转：支持为单张图片配置跳转链接（新窗口打开）

## 样式说明

### 自定义样式覆盖

组件使用 scoped 样式，如需覆盖，可在父组件中使用深度选择器：

```scss
// 修改按钮背景色
.banner-container :deep(.banner-btn) {
  background-color: rgba(0, 0, 0, 0.3);
}

// 修改指示器激活态颜色
.banner-container :deep(.indicator.active) {
  background-color: #ff6b6b;
}
```

### 预设样式变量

| CSS 类名          | 描述                             |
| ----------------- | -------------------------------- |
| .banner-container | 最外层容器，相对定位             |
| .banner-wrapper   | 图片包裹层                       |
| .banner-img       | 图片样式（object-fit: cover）    |
| .banner-btn       | 翻页按钮（悬浮有变深效果）       |
| .indicators       | 指示器容器                       |
| .indicator        | 单个指示点（激活态放大并带阴影） |

### 响应式断点

- 移动端（宽度 ≤ 640px）：图片高度固定为 200px，按钮尺寸 32x32px，指示点 6x6px

## 注意事项

1. 图片路径：组件内部会自动拼接 IMAGE_URL 配置，只需传入相对路径即可。

2. 空列表处理：当 items 为空数组时，会显示"无图片"占位符。

3. 链接优先级：若 IBannerItem.link 存在，图片将被 \<a> 标签包裹并跳转。

4. 内存管理：组件卸载时会自动清除定时器，无需手动处理。

5. 单张图片：当只有一张图片时，左右按钮和指示器会自动隐藏。

## 依赖项

- Vue 3（使用 Composition API）

- SCSS 预处理器
