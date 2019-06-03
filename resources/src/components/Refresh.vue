<template>
  <span class="refresh" @click="onRefresh">
    <i class="el-icon-refresh-right"/>
  </span>
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
  cursor: pointer;
  align-items: center;
  font-size: 20px;
  padding: 0 20px;

  &:hover {
    color: #409EFF;
  }
}
</style>
