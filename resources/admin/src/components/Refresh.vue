<template>
  <el-button class="refresh" @click="onRefresh">
    <i class="el-icon-refresh-right"/>
  </el-button>
</template>

<script>
export default {
  name: 'Refresh',
  methods: {
    onRefresh() {
      const r = this.$route
      this.$router.replace({
        path: r.fullPath,
        query: {
          _refresh: Date.now(),
        },
      })
    },
  },
}
</script>

<style scoped lang="scss">
.refresh {
  font-size: 20px !important;
}
</style>
