import Vue from 'vue'

let cache = {}
let keys = []

// 只有在生产环境下，才重写该组件
// 因为重写后，无法在 vue devtools 中的组件数中看到该组件下缓存的组件，，，
let overwrite = process.env.NODE_ENV === 'production'
  ? {
    name: 'LzKeepAlive',
    created() {
      this.cache = cache
      this.keys = keys
    },
    destroyed() {},
  }
  : {}

export default Object.assign({}, Vue.options.components.KeepAlive, overwrite)
