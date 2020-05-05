<template>
  <page-content center>
    <system-media/>
  </page-content>
</template>

<script>
import PageContent from '@c/PageContent'
import SystemMedia from '@c/SystemMedia'

export default {
  name: 'Index',
  components: {
    PageContent,
    SystemMedia,
  },
}
</script>

<style scoped lang="less">
@import "~@/styles/app";

.system-media {
  width: 800px;
  margin: 16px;
}
</style>
