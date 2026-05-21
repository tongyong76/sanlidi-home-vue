.
<template>
  <div class="banner-container" @mouseenter="handleMouseEnter" @mouseleave="handleMouseLeave">
    <!-- 图片展示区域 -->
    <div class="banner-wrapper">
      <template v-if="currentItem">
        <a
          v-if="currentItem.link"
          :href="currentItem.link"
          target="_blank"
          rel="noopener noreferrer"
          class="banner-link"
        >
          <img
            :src="IMAGE_URL + currentItem.imgurl"
            :alt="currentItem.alt || 'banner image'"
            class="banner-img"
          />
        </a>
        <img
          v-else
          :src="currentItem.imgurl"
          :alt="currentItem.alt || 'banner image'"
          class="banner-img"
        />
      </template>
      <div v-else class="banner-placeholder"> No Images </div>
    </div>

    <!-- 左右翻页按钮（仅在多图时显示） -->
    <button v-if="items.length > 1" class="banner-btn prev" @click="prev" aria-label="上一张">
      &lt;
    </button>
    <button v-if="items.length > 1" class="banner-btn next" @click="next" aria-label="下一张">
      &gt;
    </button>

    <!-- 指示器（仅在多图时显示） -->
    <div v-if="items.length > 1" class="indicators">
      <span
        v-for="(item, idx) in items"
        :key="idx"
        class="indicator"
        :class="{ active: idx === currentIndex }"
        @click="jumpTo(idx)"
        :aria-label="`跳转到第${idx + 1}张`"
      ></span>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
  import { IMAGE_URL } from '@/config';
  import { IBannerItem } from '@/types/banner';

  /**
   * Banner 项数据类型
   */

  /**
   * 组件属性定义
   */
  const props = withDefaults(
    defineProps<{
      /** 轮播图数据列表 */
      items: IBannerItem[];
      /** 是否自动轮播 */
      autoplay?: boolean;
      /** 自动轮播间隔时间（毫秒） */
      interval?: number;
    }>(),
    {
      autoplay: true,
      interval: 3000
    }
  );

  /**
   * 组件事件定义
   */
  const emit = defineEmits<{
    /** 当前索引改变时触发 */
    (e: 'change', index: number): void;
  }>();

  // 当前展示的图片索引
  const currentIndex = ref(0);
  // 鼠标是否悬停在容器上
  const isHovering = ref(false);
  // 定时器实例
  let timer: ReturnType<typeof setInterval> | null = null;

  // 计算属性：总图片数量
  const total = computed(() => props.items.length);
  // 计算属性：当前展示的图片数据
  const currentItem = computed(() => props.items[currentIndex.value] || null);

  /**
   * 停止自动轮播
   */
  const stopAutoPlay = () => {
    if (timer) {
      clearInterval(timer);
      timer = null;
    }
  };

  /**
   * 启动自动轮播（需满足条件）
   */
  const startAutoPlay = () => {
    // 条件检查：未开启自动轮播、鼠标悬停、图片数量≤1 时不启动
    if (!props.autoplay) return;
    if (isHovering.value) return;
    if (total.value <= 1) return;

    // 先清理已有定时器，避免重复
    if (timer) stopAutoPlay();

    timer = setInterval(() => {
      // 自动翻到下一张
      next();
    }, props.interval);
  };

  /**
   * 重启自动轮播（停止后根据当前状态决定是否启动）
   */
  const restartAutoPlay = () => {
    stopAutoPlay();
    startAutoPlay();
  };

  /**
   * 设置当前索引（带边界检查和事件触发）
   * @param index 目标索引
   */
  const setCurrentIndex = (index: number) => {
    if (total.value === 0) return;

    // 边界循环处理
    let newIndex = index;
    if (newIndex < 0) newIndex = total.value - 1;
    if (newIndex >= total.value) newIndex = 0;

    if (currentIndex.value !== newIndex) {
      currentIndex.value = newIndex;
      emit('change', newIndex);
    }
  };

  /**
   * 上一张
   */
  const prev = () => {
    if (total.value <= 1) return;
    setCurrentIndex(currentIndex.value - 1);
  };

  /**
   * 下一张
   */
  const next = () => {
    if (total.value <= 1) return;
    setCurrentIndex(currentIndex.value + 1);
  };

  /**
   * 跳转到指定索引
   * @param index 目标索引
   */
  const jumpTo = (index: number) => {
    if (total.value <= 1) return;
    setCurrentIndex(index);
  };

  /**
   * 鼠标进入：暂停自动轮播
   */
  const handleMouseEnter = () => {
    isHovering.value = true;
  };

  /**
   * 鼠标离开：恢复自动轮播
   */
  const handleMouseLeave = () => {
    isHovering.value = false;
  };

  // 监听影响自动轮播的状态变化，自动重启轮播
  watch([() => props.autoplay, isHovering, () => total.value, currentIndex], () => {
    restartAutoPlay();
  });

  // 监听轮播图列表长度变化，修正当前索引越界问题
  watch(
    () => props.items.length,
    (newLen) => {
      if (newLen > 0 && currentIndex.value >= newLen) {
        setCurrentIndex(0);
      }
    }
  );

  // 组件挂载：启动自动轮播
  onMounted(() => {
    restartAutoPlay();
  });

  // 组件卸载：清除定时器，避免内存泄漏
  onUnmounted(() => {
    stopAutoPlay();
  });
</script>

<style lang="scss" scoped>
  .banner-container {
    position: relative;
    width: 100%;
    height: 100%;
    background-color: #f0f0f0;
  }

  .banner-wrapper {
    width: 100%;
    height: 100%;
  }

  .banner-link {
    display: block;
    width: 100%;
    height: 100%;
  }

  .banner-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .banner-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e0e0e0;
    color: #999;
    font-size: 16px;
  }

  /* 翻页按钮样式 */
  .banner-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    cursor: pointer;
    padding: 0;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    font-size: 24px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s;
    z-index: 10;
  }

  .banner-btn:hover {
    background-color: rgba(0, 0, 0, 0.8);
  }

  .prev {
    left: -60px;
  }

  .next {
    right: -60px;
  }

  /* 指示器样式 */
  .indicators {
    position: absolute;
    bottom: 16px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 10;
  }

  .indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.6);
    cursor: pointer;
    transition: all 0.3s;
  }

  .indicator.active {
    background-color: white;
    transform: scale(1.25);
    box-shadow: 0 0 4px rgba(0, 0, 0, 0.3);
  }

  .indicator:hover {
    background-color: rgba(255, 255, 255, 0.9);
  }

  /* 移动端适配 */
  @media (max-width: 640px) {
    .banner-wrapper {
      height: 200px;
    }

    .banner-btn {
      width: 32px;
      height: 32px;
      font-size: 18px;
    }

    .indicators {
      gap: 8px;
    }

    .indicator {
      width: 6px;
      height: 6px;
    }
  }
</style>
