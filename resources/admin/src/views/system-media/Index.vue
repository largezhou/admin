<template>
  <el-card>
    <template #header>
      <content-header/>
    </template>
    <el-row type="flex" justify="center">
      <system-media/>
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
@import '~element-ui/packages/theme-chalk/src/common/var';

::v-deep {
  .el-card__body {
    padding: 0;
  }

  .system-media {
    width: 825px;
    border: 1px solid $--color-info-light;
    margin: 20px;
  }
}
</style>
