<template>
  <loading-action :action="cacheConfig" title="清空并重新缓存配置">缓存配置</loading-action>
</template>

<script>
import LoadingAction from '@c/LoadingAction'
import { cacheConfig } from '@/api/configs'

export default {
  name: 'CacheConfig',
  components: {
    LoadingAction,
  },
  methods: {
    cacheConfig,
  },
}
</script>
