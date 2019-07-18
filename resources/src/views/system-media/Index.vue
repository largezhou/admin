<template>
  <el-card>
    <template #header>
      <content-header/>
    </template>
    <el-row type="flex" justify="center">
      <system-media class="w-100"/>
    </el-row>
  </el-card>
</template>

<script>
import SystemMedia from '@c/SystemMedia'

export default {
  name: 'Index',
  components: {
    SystemMedia,
  },
}
</script>

<style scoped lang="scss">
/deep/ {
  .el-card__body {
    padding: 0;
  }
}
</style>
