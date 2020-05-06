import Vue from 'vue'
import _remove from 'lodash/remove'

const cache = {}
const keys = []

/**
 * 通过组件名来移除一个缓存
 *
 * @param {string} name
 */
export const removeCacheByName = (name) => {
  if (!name) {
    return
  }

  const key = findKeyByName(name)

  cache[key].componentInstance.$destroy()

  delete cache[key]
  _remove(keys, (i) => i === key)
}

/**
 * 通过组件名来查找到他的缓存 key
 * @param {string} name
 * @return {string}
 */
const findKeyByName = (name) => {
  for (const key of Object.keys(cache)) {
    const v = cache[key]
    if (v.componentOptions?.Ctor?.options?.name === name) {
      return key
    }
  }
}

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
