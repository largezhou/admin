<template>
  <component :is="component"/>
</template>

<script>
import pages from '@v/pages'
import _trim from 'lodash/trim'
import Page404 from '@v/errors/Page404'

export default {
  name: 'ContentView',
  computed: {
    component() {
      const path = this.$route.path
      // 尝试直接匹配
      const key = _trim(path, '/')
      let c = pages[key]
      // 如果没有，则用正则匹配
      if (!c) {
        const keys = Object.keys(pages)
        for (let i of keys) {
          if ((new RegExp(`^${i}$`)).test(key)) {
            c = pages[i]
          }
        }
      }
      return c || Page404
    },
  },
}
</script>
