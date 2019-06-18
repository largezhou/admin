<template>
  <div>
    <div class="route" v-for="(item, index) of httpRoute" :key="index">
      <el-tag
        size="small"
        class="tag"
        v-for="i of item.method"
        :key="i"
      >
        {{ i }}
      </el-tag>
      <code>{{ item.path }}</code>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RouteShow',
  props: {
    data: Object,
  },
  computed: {
    httpRoute() {
      const t = this.data.http_path
      if (!t) {
        return []
      }

      return t.map((i) => {
        let method = this.data.http_method
        let path = i
        if (i.indexOf(':') !== -1) {
          [method, path] = i.split(':')
          method = method.split(',').filter(i => !!i)
        }
        if (method.length === 0) {
          method = ['ANY']
        }
        return {
          method,
          path,
        }
      })
    },
  },
}
</script>

<style scoped>
.tag {
  margin-right: 5px;
}

.route {
  margin-bottom: 5px;
}
</style>
