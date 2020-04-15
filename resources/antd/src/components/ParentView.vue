<template>
  <lz-keep-alive :include="$store.state.include">
    <router-view/>
  </lz-keep-alive>
</template>

<script>
import LzKeepAlive from '@c/LzKeepAlive'

export default {
  name: 'ParentView',
  components: {
    LzKeepAlive,
  },
}
</script>
