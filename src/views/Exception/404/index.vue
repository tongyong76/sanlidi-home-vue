<template>
  <SanException
    :data="{
      title: '404',
      desc: 'desc',
      btnText: 'btnText',
      imgUrl
    }"
  />
</template>

<script setup lang="ts">
  import imgUrl from '@images/svg/404.svg';
  defineOptions({ name: 'Exception404' });
</script>
