<template>
  <div class="san-loading">
    <div class="loading">
      <div style="--x: 0"></div>
      <div style="--x: 1"></div>
      <div style="--x: 2"></div>
      <div style="--x: 3"></div>
      <div style="--x: 4"></div>
      <div style="--x: 5"></div>
    </div>
    <div class="notice"><slot></slot></div>
  </div>
</template>

<script setup lang="ts"></script>

<style lang="scss" scoped>
.san-loading {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background-color: #f5f7fa;
  position: relative;
  .loading {
    display: flex;
    transform: scale(0.4, 0.4);
    div {
      position: relative;
      width: 40px;
      height: 220px;
      background: linear-gradient(to bottom, rgba(0, 0, 0, 0.05), #eef1f5);
      margin: 20px;
      border-radius: 20px;
      border: 2px solid #eef1f5;
      box-shadow: 15px 15px 20px rgba(0, 0, 0, 0.1), -15px -15px 20px #fff,
        inset -5px -5px 5px rgba(255, 255, 255, 0.5),
        inset 5px 5px 5px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      &::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        box-shadow: inset -5px -5px 5px rgba(0, 0, 0, 0.1),
          0 420px 0 400px lightskyblue;
        animation: animate 0.6s ease-in-out infinite;
        animation-delay: calc(var(--x) * 0.1s);
        filter: hue-rotate(calc(var(--x) * 60deg));
        transform: translateY(180px);
      }
    }
  }
  .notice {
    position: absolute;
    top: 50%;
    margin-top: 80px;
    font-size: 20px;
    color: #000;
  }
}

@keyframes animate {
  0% {
    transform: translateY(180px);
    // filter: hue-rotate(0deg);
  }
  50% {
    transform: translateY(0px);
    // filter: hue-rotate(180deg);
  }
  100% {
    transform: translateY(180px);
    // filter: hue-rotate(360deg);
  }
}
</style>
