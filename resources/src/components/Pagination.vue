<template>
  <el-pagination
    ref="page"
    v-if="page"
    v-bind="attrs"
    @size-change="onSizeChange"
    @current-change="onChange"
    :page-sizes="pageSizes"
    :current-page.sync="currentPage"
    :page-size.sync="perPage"
    :total="page.total"
  />
</template>

<script>
const DEFAULT_CONFIG = {
  background: true,
  'page-sizes': [15, 30, 50, 100, 200],
  layout: 'total, prev, pager, next, sizes, jumper',
}

export default {
  name: 'Pagination',
  data() {
    return {
      currentPage: 1,
      perPage: 15,
    }
  },
  props: {
    page: Object,
  },
  computed: {
    attrs() {
      return Object.assign({}, DEFAULT_CONFIG, this.$attrs)
    },
    pageSizes() {
      const sizes = this.attrs['page-sizes']
      const perPage = this.page.per_page
      if (sizes.indexOf(perPage) === -1) {
        return [this.page.per_page, ...sizes]
      } else {
        return sizes
      }
    },
  },
  methods: {
    push() {
      const query = Object.assign({}, this.$route.query, {
        page: this.currentPage,
        per_page: this.perPage,
      })
      this.$router.push({
        path: this.$route.path,
        query,
      })
    },
    onSizeChange(perPage) {
      // 当切换后, 如果当前不大于总页数, 则不会触发 current-change 事件, 所以要跳转路由
      // 否则, 在 current-change 事件里跳转路由
      if (this.page.current_page <= (Math.ceil(this.page.total / perPage))) {
        this.push()
      }
    },
    onChange(page) {
      this.push()
    },
  },
  watch: {
    page: {
      handler(newVal) {
        if (!newVal) {
          return
        }
        this.currentPage = newVal.current_page
        this.perPage = newVal.per_page
        // 处理浏览器前进后退时, 分页器的当前页不对的问题
        this.$nextTick(() => {
          this.$refs.page.internalCurrentPage = newVal.current_page
        })
      },
      immediate: true,
    },
  },
}
</script>

<style scoped>

</style>
