<template>
  <Header />
  <router-view />
  <Footer />
</template>

<script setup lang="ts">
  import Header from '../Components/Header/index.vue';
  import Footer from '../Components/Footer/index.vue';
</script>

<style lang="scss" scoped></style>
