import Vue from 'vue'

const cache = {}
const keys = []

// 因为重写后，无法在 vue devtools 中的组件数中看到该组件下缓存的组件
// 所以开发环境下，除了测试缓存效果，尽量不要开启路由的缓存
export default Object.assign({}, Vue.options.components.KeepAlive, {
  name: 'LzKeepAlive',
  created() {
    this.cache = cache
    this.keys = keys
  },
  destroyed() {},
})
