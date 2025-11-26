<template>
  <SanException
    :data="{
      title: '500',
      desc: 'desc',
      btnText: 'btnText',
      imgUrl
    }"
  />
</template>

<script setup lang="ts">
  import imgUrl from '@images/svg/500.svg';
  defineOptions({ name: 'Exception500' });
</script>
