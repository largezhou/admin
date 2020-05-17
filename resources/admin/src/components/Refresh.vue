<template>
  <a-button @click.stop="onRefresh">
    <a-icon type="reload"/>
  </a-button>
</template>

<script>
import { removeCacheByName } from '@c/LzKeepAlive'
import _last from 'lodash/last'

export default {
  name: 'Refresh',
  methods: {
    onRefresh() {
      removeCacheByName(_last(this.$route.matched)?.components?.default?.name)

      this.$router.replace({
        path: this.$route.fullPath,
        query: {
          _refresh: Date.now(),
        },
      })
    },
  },
}
</script>
